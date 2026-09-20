<x-app-layout>
    @php
        $paymentLabels = [
            'pending' => 'รอชำระเงิน',
            'paid' => 'ชำระเงินยืนยันแล้ว',
            'failed' => 'ชำระเงินไม่สำเร็จ',
        ];
        $shippingAddress = $order->shipping_address ?? [];
    @endphp

    <x-slot name="header">
        <div class="page-shell">
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-red-300 hover:text-red-200">← กลับแดชบอร์ดร้านค้า</a>
            <h1 class="mt-3 text-2xl font-bold text-slate-900">รายละเอียดคำสั่งซื้อ #{{ $order->id }}</h1>
        </div>
    </x-slot>

    <main class="page-shell max-w-4xl space-y-5 py-8 sm:py-10">
        <section class="card p-5 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-700 pb-5">
                <div>
                    <p class="eyebrow">รายการสั่งซื้อ</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">สินค้าที่ลูกค้าสั่ง</h2>
                    <p class="mt-2 text-sm text-slate-400">ลูกค้า: <span class="font-semibold text-slate-200">{{ $order->customer->name }}</span></p>
                </div>
                <span class="rounded-full px-3 py-1 text-sm font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-500/15 text-emerald-300' : ($order->payment_status === 'failed' ? 'bg-red-500/15 text-red-300' : 'bg-amber-500/15 text-amber-200') }}">{{ $paymentLabels[$order->payment_status] ?? 'ไม่ทราบสถานะ' }}</span>
            </div>

            <ul class="divide-y divide-gray-700" role="list">
                @foreach($order->items as $item)
                    <li class="flex flex-wrap items-center justify-between gap-3 py-5">
                        <div class="flex min-w-0 items-center gap-4">
                            @if($item->product->images->first())
                                <img src="{{ $item->product->images->first()->image_url }}" alt="{{ $item->product->name }}" width="80" height="80" class="h-20 w-20 shrink-0 rounded-xl border border-gray-700 object-cover" onerror="this.onerror=null;this.src='https://placehold.co/160x160/1f2937/f9fafb?text=SecondPC';">
                            @else
                                <div class="grid h-20 w-20 shrink-0 place-items-center rounded-xl border border-gray-700 bg-slate-900 text-center text-xs font-bold text-slate-400">ไม่มีรูป</div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-bold text-slate-100">{{ $item->product->name }}</p>
                                <p class="mt-1 text-sm text-slate-400">สถานะจัดส่ง: {{ ['pending' => 'รอจัดส่ง', 'shipped' => 'จัดส่งแล้ว', 'delivered' => 'ส่งถึงแล้ว'][$item->delivery_status] ?? $item->delivery_status }}</p>
                                @if($item->tracking_number)<p class="mt-1 text-xs font-semibold text-red-300">เลขพัสดุ: {{ $item->tracking_number }}</p>@endif
                            </div>
                        </div>
                        <p class="text-lg font-bold text-slate-100">฿{{ number_format($item->price, 2) }}</p>
                    </li>
                @endforeach
            </ul>

            <div class="flex justify-between border-t border-gray-700 pt-5 text-lg font-bold text-slate-100"><span>ยอดรวมสำหรับร้านของคุณ</span><span>฿{{ number_format($order->items->sum('price'), 2) }}</span></div>
        </section>

        <x-order-timeline :order="$order" />

        <section class="card p-5 sm:p-6">
            <p class="eyebrow">การจัดส่ง</p>
            <h2 class="mt-1 text-xl font-bold text-slate-900">ที่อยู่สำหรับจัดส่ง</h2>
            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-900/60 p-4"><dt class="text-xs font-semibold text-slate-400">ชื่อผู้รับ</dt><dd class="mt-1 font-bold text-slate-100">{{ $shippingAddress['name'] ?? '-' }}</dd></div>
                <div class="rounded-xl bg-slate-900/60 p-4"><dt class="text-xs font-semibold text-slate-400">เบอร์โทรศัพท์</dt><dd class="mt-1 font-bold text-slate-100">{{ $shippingAddress['phone'] ?? '-' }}</dd></div>
                <div class="rounded-xl bg-slate-900/60 p-4 sm:col-span-2"><dt class="text-xs font-semibold text-slate-400">ที่อยู่</dt><dd class="mt-1 whitespace-pre-line font-bold leading-6 text-slate-100">{{ $shippingAddress['address'] ?? '-' }}</dd></div>
                <div class="rounded-xl bg-slate-900/60 p-4"><dt class="text-xs font-semibold text-slate-400">รหัสไปรษณีย์</dt><dd class="mt-1 font-bold text-slate-100">{{ $shippingAddress['postcode'] ?? '-' }}</dd></div>
            </dl>
        </section>
    </main>
</x-app-layout>
