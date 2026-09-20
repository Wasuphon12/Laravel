@props(['order'])

@php
    $items = $order->items;
    $paymentFailed = $order->payment_status === 'failed';
    $paymentPaid = $order->payment_status === 'paid';
    $hasShippedItem = $items->contains(fn ($item) => in_array($item->delivery_status, ['shipped', 'delivered'], true));
    $allDelivered = $items->isNotEmpty() && $items->every(fn ($item) => $item->delivery_status === 'delivered');

    $steps = [
        ['label' => 'สร้างคำสั่งซื้อ', 'detail' => 'สร้างเมื่อ '.$order->created_at->format('d/m/Y H:i'), 'state' => 'complete'],
        [
            'label' => 'ชำระเงินแล้ว',
            'detail' => $paymentFailed ? 'ชำระเงินไม่สำเร็จ' : ($paymentPaid ? 'ระบบยืนยันการชำระเงินแล้ว' : 'รอลูกค้าชำระเงิน'),
            'state' => $paymentFailed ? 'failed' : ($paymentPaid ? 'complete' : 'current'),
        ],
        [
            'label' => 'ร้านจัดส่ง',
            'detail' => $hasShippedItem ? 'ร้านค้าบันทึกการจัดส่งแล้ว' : ($paymentPaid ? 'รอร้านค้าจัดส่ง' : 'รอการชำระเงิน'),
            'state' => $hasShippedItem ? 'complete' : ($paymentPaid ? 'current' : 'waiting'),
        ],
        [
            'label' => 'ลูกค้าได้รับสินค้า',
            'detail' => $allDelivered ? 'ลูกค้ายืนยันรับสินค้าแล้ว' : ($hasShippedItem ? 'รอลูกค้ายืนยันรับสินค้า' : 'รอการจัดส่ง'),
            'state' => $allDelivered ? 'complete' : ($hasShippedItem ? 'current' : 'waiting'),
        ],
    ];
@endphp

<section class="card p-5 sm:p-6" aria-labelledby="order-timeline-title">
    <div class="mb-5 flex flex-wrap items-end justify-between gap-2">
        <div>
            <p class="eyebrow">ติดตามสถานะ</p>
            <h2 id="order-timeline-title" class="mt-1 text-xl font-bold text-slate-900">ไทม์ไลน์คำสั่งซื้อ</h2>
        </div>
        <p class="text-sm text-slate-400">คำสั่งซื้อ #{{ $order->id }}</p>
    </div>

    <ol class="order-timeline" aria-label="สถานะคำสั่งซื้อ">
        @foreach($steps as $index => $step)
            <li class="order-timeline-step order-timeline-{{ $step['state'] }}">
                <span class="order-timeline-marker" aria-hidden="true">{{ $step['state'] === 'complete' ? '✓' : ($step['state'] === 'failed' ? '!' : $index + 1) }}</span>
                <div class="min-w-0">
                    <p class="font-bold text-slate-100">{{ $step['label'] }}</p>
                    <p class="mt-1 text-xs leading-5 {{ $step['state'] === 'failed' ? 'text-red-300' : ($step['state'] === 'current' ? 'text-amber-200' : 'text-slate-400') }}">{{ $step['detail'] }}</p>
                </div>
            </li>
        @endforeach
    </ol>
</section>
