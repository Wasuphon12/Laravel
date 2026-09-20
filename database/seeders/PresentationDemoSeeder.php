<?php

namespace Database\Seeders;

use App\Models\DealerProfile;
use App\Models\DealerReview;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PresentationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::updateOrCreate(
            ['email' => 'demo-customer@secondpc.test'],
            ['name' => 'ลูกค้าทดสอบ Demo', 'password' => Hash::make('password'), 'role' => 'customer'],
        );
        User::updateOrCreate(
            ['email' => 'admin@secondpc.test'],
            ['name' => 'SecondPC Admin', 'password' => Hash::make('password'), 'role' => 'admin'],
        );

        $pendingDealer = User::updateOrCreate(
            ['email' => 'newstore@secondpc.test'],
            ['name' => 'New Store Demo', 'password' => Hash::make('password'), 'role' => 'dealer'],
        );
        $pendingDealer->dealerProfile()->updateOrCreate([], [
            'store_name' => 'New Store Demo', 'id_card_url' => 'https://placehold.co/800x500/7f1d1d/f9fafb?text=KYC+Pending',
            'bank_name' => 'KBank', 'bank_account' => '111-2-33333-4', 'promptpay_id' => '0856789012', 'status' => 'pending',
        ]);

        collect([
            'demo-dealer@secondpc.test' => '0812345678', 'redgear@secondpc.test' => '0823456789',
            'bytecraft@secondpc.test' => '0834567890', 'techrenew@secondpc.test' => '0845678901',
        ])->each(function (string $promptPayId, string $email): void {
            User::query()->where('email', $email)->first()?->dealerProfile?->update(['promptpay_id' => $promptPayId]);
        });

        $products = Product::query()
            ->where('status', 'available')
            ->whereDoesntHave('orderItem')
            ->with('dealer')
            ->orderBy('id')
            ->get();

        $scenarios = [
            ['ref' => 'DEMO-PRESENT-SLIP', 'payment' => 'pending', 'slip' => 'pending', 'delivery' => 'pending', 'product' => 'reserved', 'title' => 'รอตรวจสอบสลิป'],
            ['ref' => 'DEMO-PRESENT-PACK', 'payment' => 'paid', 'slip' => 'verified', 'delivery' => 'pending', 'product' => 'sold', 'title' => 'ชำระแล้ว รอร้านจัดส่ง'],
            ['ref' => 'DEMO-PRESENT-SHIP', 'payment' => 'paid', 'slip' => 'verified', 'delivery' => 'shipped', 'product' => 'sold', 'title' => 'จัดส่งแล้ว'],
            ['ref' => 'DEMO-PRESENT-DONE', 'payment' => 'paid', 'slip' => 'verified', 'delivery' => 'delivered', 'product' => 'sold', 'title' => 'รับสินค้าแล้ว'],
        ];

        $items = [];
        foreach ($scenarios as $position => $scenario) {
            $order = Order::query()->where('transaction_ref', $scenario['ref'])->with('items.product')->first();
            $product = $order?->items->first()?->product ?? $products->shift();
            if (! $product) {
                continue;
            }

            $order ??= new Order();
            $order->forceFill([
                'customer_id' => $customer->id,
                'total_amount' => $product->price,
                'payment_method' => 'promptpay_direct',
                'payment_status' => $scenario['payment'],
                'transaction_ref' => $scenario['ref'],
                'payment_slip_path' => null,
                'slip_status' => $scenario['slip'],
                'payment_submitted_at' => now()->subDays(4 - $position),
                'payment_verified_at' => $scenario['payment'] === 'paid' ? now()->subDays(3 - $position) : null,
                'shipping_address' => ['name' => 'ลูกค้าทดสอบ Demo', 'phone' => '081-234-5678', 'address' => '99 ถนนสุขุมวิท กรุงเทพฯ', 'postcode' => '10110'],
            ])->save();

            $item = OrderItem::query()->updateOrCreate(
                ['order_id' => $order->id, 'product_id' => $product->id],
                [
                    'dealer_id' => $product->dealer_id, 'price' => $product->price,
                    'tracking_number' => $scenario['delivery'] === 'shipped' ? 'TH-DEMO-20260906-03' : null,
                    'delivery_status' => $scenario['delivery'],
                ],
            );
            $product->update(['status' => $scenario['product']]);
            $items[$scenario['title']] = $item;
        }

        if (isset($items['รับสินค้าแล้ว'])) {
            $completed = $items['รับสินค้าแล้ว'];
            DealerReview::query()->updateOrCreate(
                ['order_item_id' => $completed->id],
                ['reviewer_id' => $customer->id, 'dealer_id' => $completed->dealer_id, 'rating' => 5, 'comment' => 'สินค้าตรงปก จัดส่งรวดเร็ว แพ็กสินค้าเรียบร้อย'],
            );
            $disputeItem = $items['จัดส่งแล้ว'] ?? $completed;
            $dispute = Dispute::query()->firstOrNew(['order_item_id' => $disputeItem->id]);
            $dispute->forceFill([
                'customer_id' => $customer->id, 'dealer_id' => $disputeItem->dealer_id,
                'reason' => 'ขอตรวจสอบรายละเอียดการรับประกันสินค้าก่อนยืนยันรับสินค้า',
                'evidence_urls' => [], 'status' => 'open',
            ])->save();
        }
    }
}
