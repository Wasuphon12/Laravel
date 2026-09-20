<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\PromptPayQrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Request $request, PromptPayQrService $promptPayQr): View
    {
        $ids = array_filter(explode(',', (string) $request->query('products')));
        $products = Product::query()->whereIn('id', $ids)->where('status', 'available')->with('dealer.dealerProfile')->get();
        abort_if($products->isEmpty(), 404, 'ไม่พบสินค้าที่พร้อมจำหน่าย');

        $total = $products->sum('price');
        $paymentGroups = $products->groupBy('dealer_id')->map(function ($dealerProducts) use ($promptPayQr) {
            $dealer = $dealerProducts->first()->dealer;
            $amount = $dealerProducts->sum('price');
            $promptPayId = $dealer?->dealerProfile?->promptpay_id;

            return [
                'dealer' => $dealer,
                'products' => $dealerProducts,
                'total' => $amount,
                'promptpay_id' => $promptPayId,
                'qr_data' => $promptPayQr->payload($promptPayId, (float) $amount),
            ];
        })->values();

        return view('checkout.create', compact('products', 'total', 'paymentGroups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'], 'product_ids.*' => ['integer', 'distinct', 'exists:products,id'],
            'payment_method' => ['required', 'in:promptpay_direct'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.dealer_id' => ['required', 'integer', 'distinct', 'exists:users,id'],
            'payments.*.payment_slip' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'payments.*.transaction_ref' => ['nullable', 'string', 'max:100'],
            'shipping_address.name' => ['required', 'string'], 'shipping_address.phone' => ['required', 'string'],
            'shipping_address.address' => ['required', 'string'], 'shipping_address.postcode' => ['required', 'string'],
        ]);

        $orders = DB::transaction(function () use ($request, $data) {
            $products = Product::query()->whereIn('id', $data['product_ids'])->with('dealer.dealerProfile')->lockForUpdate()->get();
            if ($products->count() !== count($data['product_ids']) || $products->contains(fn (Product $product) => $product->status !== 'available')) {
                throw ValidationException::withMessages(['product_ids' => 'มีสินค้าบางรายการถูกจองหรือจำหน่ายไปแล้ว']);
            }
            $productsByDealer = $products->groupBy('dealer_id');
            $submittedDealerIds = collect($data['payments'])->pluck('dealer_id')->map(fn ($id) => (int) $id)->sort()->values();
            if ($submittedDealerIds->all() !== $productsByDealer->keys()->map(fn ($id) => (int) $id)->sort()->values()->all()) {
                throw ValidationException::withMessages(['payments' => 'กรุณาแนบสลิปให้ครบทุกร้านค้า']);
            }

            return $productsByDealer->map(function ($dealerProducts, $dealerId) use ($request, $data) {
                $payment = collect($data['payments'])->first(fn ($value) => (int) $value['dealer_id'] === (int) $dealerId);
                $order = $request->user()->orders()->create([
                    'total_amount' => $dealerProducts->sum('price'), 'payment_method' => 'promptpay_direct', 'shipping_address' => $data['shipping_address'],
                ]);
                $slipPath = Storage::disk('public')->putFile("payment-slips/{$order->id}", $payment['payment_slip']);
                $reference = filled($payment['transaction_ref']) ? $payment['transaction_ref'] : now()->format('YmdHis');
                $order->update([
                    'payment_slip_path' => $slipPath,
                    'transaction_ref' => "DIRECT-{$order->id}-{$reference}",
                    'slip_status' => 'pending',
                    'payment_submitted_at' => now(),
                ]);
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

        return redirect()->route('dashboard')->with('success', "ส่งสลิปให้ {$orders->count()} ร้านค้าแล้ว รอแต่ละร้านตรวจสอบการชำระเงิน");
    }

    public function show(Request $request, Order $order, PromptPayQrService $promptPayQr): View
    {
        abort_unless($order->customer_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $order->load('items.product', 'items.dealer.dealerProfile', 'items.dealerReview', 'paymentVerifier');
        $dealer = $order->items->first()?->dealer;
        $qrData = $order->payment_method === 'promptpay_direct'
            ? $promptPayQr->payload($dealer?->dealerProfile?->promptpay_id, (float) $order->total_amount)
            : "SECONDPC|ORDER:{$order->id}|AMOUNT:".number_format((float) $order->total_amount, 2, '.', '')."|REF:SPC-{$order->id}";

        return view('orders.show', compact('order', 'qrData', 'dealer'));
    }

    public function submitPaymentSlip(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->customer_id === $request->user()->id && $order->payment_status === 'pending', 403);
        $data = $request->validate([
            'payment_slip' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'transaction_ref' => ['nullable', 'string', 'max:100'],
        ]);

        if ($order->payment_slip_path) Storage::disk('public')->delete($order->payment_slip_path);
        $path = Storage::disk('public')->putFile("payment-slips/{$order->id}", $data['payment_slip']);
        $order->update([
            'payment_slip_path' => $path,
            'transaction_ref' => $data['transaction_ref'] ?: "SLIP-{$order->id}-".now()->format('YmdHis'),
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

        return back()->with('success', 'ยืนยันรับสินค้าแล้ว เงินได้โอนเข้าร้านค้าโดยตรงเรียบร้อย');
    }
}
