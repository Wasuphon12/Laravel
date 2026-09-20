<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\DemoPaymentGatewayService;
use App\Services\PromptPayQrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $ids = array_filter(explode(',', (string) $request->query('products')));
        $products = Product::query()->whereIn('id', $ids)->where('status', 'available')->with('dealer.dealerProfile')->get();
        abort_if($products->isEmpty(), 404, 'ไม่พบสินค้าที่พร้อมจำหน่าย');

        if (! $this->savedShippingAddress($request)) {
            $request->session()->put('intended_checkout', $request->fullUrl());

            return redirect()->route('address.edit')->with('info', 'กรุณาบันทึกที่อยู่จัดส่งก่อนสร้างคำสั่งซื้อ');
        }

        $total = $products->sum('price');
        $paymentGroups = $products->groupBy('dealer_id')->map(function ($dealerProducts) {
            $dealer = $dealerProducts->first()->dealer;
            $amount = $dealerProducts->sum('price');

            return [
                'dealer' => $dealer,
                'products' => $dealerProducts,
                'total' => $amount,
            ];
        })->values();

        return view('checkout.create', compact('products', 'total', 'paymentGroups'));
    }

    public function store(Request $request, DemoPaymentGatewayService $demoGateway): RedirectResponse
    {
        $data = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'], 'product_ids.*' => ['integer', 'distinct', 'exists:products,id'],
            'payment_method' => ['required', 'in:demo_gateway'],
        ]);
        $shippingAddress = $this->savedShippingAddress($request);
        if (! $shippingAddress) {
            $request->session()->put('intended_checkout', route('checkout.create', ['products' => implode(',', $data['product_ids'])]));

            return redirect()->route('address.edit')->with('info', 'กรุณาบันทึกที่อยู่จัดส่งก่อนสร้างคำสั่งซื้อ');
        }

        $orders = DB::transaction(function () use ($request, $data, $demoGateway, $shippingAddress) {
            $products = Product::query()->whereIn('id', $data['product_ids'])->with('dealer.dealerProfile')->lockForUpdate()->get();
            if ($products->count() !== count($data['product_ids']) || $products->contains(fn (Product $product) => $product->status !== 'available')) {
                throw ValidationException::withMessages(['product_ids' => 'มีสินค้าบางรายการถูกจองหรือจำหน่ายไปแล้ว']);
            }
            $productsByDealer = $products->groupBy('dealer_id');
            return $productsByDealer->map(function ($dealerProducts) use ($request, $demoGateway, $shippingAddress) {
                $order = $request->user()->orders()->create([
                    'total_amount' => $dealerProducts->sum('price'),
                    'payment_method' => 'demo_gateway',
                    'shipping_address' => $shippingAddress,
                ]);
                $demoGateway->prepare($order);
                foreach ($dealerProducts as $product) {
                    $order->items()->create([
                        'product_id' => $product->id, 'dealer_id' => $product->dealer_id, 'price' => $product->price,
                    ]);
                    $product->update(['status' => 'reserved']);
                }

                return $order;
            });
        });
        $request->session()->forget('cart');

        return redirect()->route('orders.show', $orders->first())->with('success', "สร้าง QR ชำระเงินจำลองสำหรับ {$orders->count()} ร้านค้าแล้ว");
    }

    public function show(Request $request, Order $order, PromptPayQrService $promptPayQr, DemoPaymentGatewayService $demoGateway): View
    {
        abort_unless($order->customer_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $order->load('items.product', 'items.dealer.dealerProfile', 'items.dealerReview', 'paymentVerifier');
        $dealer = $order->items->first()?->dealer;
        $isDemoGateway = $order->payment_method === 'demo_gateway';
        $qrData = $isDemoGateway
            ? $demoGateway->qrPayload($order)
            : ($order->payment_method === 'promptpay_direct'
            ? $promptPayQr->payload($dealer?->dealerProfile?->promptpay_id, (float) $order->total_amount)
            : "SECONDPC|ORDER:{$order->id}|AMOUNT:".number_format((float) $order->total_amount, 2, '.', '')."|REF:SPC-{$order->id}");

        return view('orders.show', compact('order', 'qrData', 'dealer', 'isDemoGateway'));
    }

    /**
     * Show a dealer only the order items that belong to that dealer.
     */
    public function dealerOrder(Request $request, Order $order): View
    {
        abort_unless($order->items()->where('dealer_id', $request->user()->id)->exists(), 403);

        $order->load([
            'customer',
            'items' => fn ($query) => $query->where('dealer_id', $request->user()->id)->with('product.images'),
        ]);

        return view('dealer.order-show', compact('order'));
    }

    public function simulateGatewayCallback(Request $request, Order $order, DemoPaymentGatewayService $demoGateway): RedirectResponse
    {
        abort_unless($order->customer_id === $request->user()->id, 403);

        DB::transaction(function () use ($order, $demoGateway): void {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($order->payment_expires_at?->isPast() && $order->payment_status === 'pending') {
                $order->update(['payment_status' => 'failed']);
            }

            abort_unless($demoGateway->hasActivePaymentRequest($order), 422, 'คำขอชำระเงินจำลองนี้หมดอายุหรือยืนยันไปแล้ว');

            // Simulate a signed gateway webhook and independently validate its active payment request.
            $order->update([
                'payment_status' => 'paid',
                'payment_verified_at' => now(),
                'payment_verified_by' => null,
            ]);
        });

        return back()->with('success', 'Demo Gateway ส่ง webhook สำเร็จ ระบบยืนยันสถานะชำระเงินแล้ว');
    }

    public function submitPaymentSlip(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->customer_id === $request->user()->id && $order->payment_status === 'pending', 403);
        $data = $request->validate([
            'payment_slip' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($order->payment_slip_path) Storage::disk('public')->delete($order->payment_slip_path);
        $path = Storage::disk('public')->putFile("payment-slips/{$order->id}", $data['payment_slip']);
        $order->update([
            'payment_slip_path' => $path,
            'transaction_ref' => "SLIP-{$order->id}-".now()->format('YmdHis'),
            'slip_status' => 'pending',
            'payment_submitted_at' => now(),
            'payment_verified_at' => null,
            'payment_verified_by' => null,
        ]);

        return back()->with('success', 'อัปโหลดสลิปแล้ว รอร้านค้าตรวจสอบและยืนยันการชำระเงิน');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->customer_id === $request->user()->id, 403);

        DB::transaction(function () use ($order): void {
            $order = Order::query()->with('items.product')->lockForUpdate()->findOrFail($order->id);
            abort_unless(
                $order->payment_status === 'pending' && $order->slip_status === 'not_submitted' && ! $order->payment_slip_path,
                422,
                'ไม่สามารถยกเลิกคำสั่งซื้อที่ส่งสลิปหรือยืนยันการชำระเงินแล้ว',
            );

            $order->items->each(fn ($item) => $item->product->update(['status' => 'available']));
            $order->delete();
        });

        return redirect()->route('dashboard')->with('success', 'ยกเลิกคำสั่งซื้อแล้ว สินค้ากลับมาพร้อมจำหน่าย');
    }

    public function verifyPaymentSlip(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->items()->where('dealer_id', $request->user()->id)->exists(), 403);
        abort_unless($order->slip_status === 'pending' && $order->payment_slip_path, 422, 'ยังไม่มีสลิปที่รอตรวจสอบ');

        $order->update([
            'payment_status' => 'paid',
            'slip_status' => 'verified',
            'payment_verified_at' => now(),
            'payment_verified_by' => $request->user()->id,
        ]);

        return back()->with('success', 'ยืนยันสลิปแล้ว รายการพร้อมสำหรับการจัดส่ง');
    }

    public function confirmDelivery(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->customer_id === $request->user()->id, 403);
        foreach ($order->items()->where('delivery_status', 'shipped')->get() as $item) {
            $item->update(['delivery_status' => 'delivered']);
        }

        return back()->with('success', $order->payment_method === 'demo_gateway'
            ? 'ยืนยันรับสินค้าแล้ว (โหมดสาธิต)'
            : 'ยืนยันรับสินค้าแล้ว เงินได้โอนเข้าร้านค้าโดยตรงเรียบร้อย');
    }

    /**
     * Get a complete default address from the customer's saved address page.
     * Checkout intentionally never accepts the address from its form.
     *
     * @return array{name: string, phone: string, address: string, postcode: string}|null
     */
    private function savedShippingAddress(Request $request): ?array
    {
        $address = [
            'name' => $request->user()->shipping_name,
            'phone' => $request->user()->shipping_phone,
            'address' => $request->user()->shipping_address,
            'postcode' => $request->user()->shipping_postcode,
        ];

        return collect($address)->contains(fn ($value) => blank($value)) ? null : $address;
    }
}
