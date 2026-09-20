<x-app-layout>
    @php
        $paidOrders = $orders->where('payment_status', 'paid');
        $pendingOrders = $orders->where('payment_status', 'pending');
        $statusLabels = ['pending' => ['รอชำระเงิน', 'order-status-pending'], 'paid' => ['ชำระเงินแล้ว', 'order-status-paid'], 'failed' => ['ชำระเงินไม่สำเร็จ', 'order-status-failed']];
    @endphp
    <x-slot name="header"><div class="page-shell"><p class="eyebrow">ศูนย์รวมลูกค้า</p><h1 class="mt-1 font-bold text-2xl text-slate-900">คำสั่งซื้อของฉัน</h1></div></x-slot>

    <div class="page-shell py-8 sm:py-10">
        <section class="customer-hero">
            <div><p class="text-sm font-semibold tracking-widest text-red-300">ภาพรวมคำสั่งซื้อ</p><h2 class="mt-2 text-2xl font-bold text-white sm:text-3xl">ติดตามทุกคำสั่งซื้อ<br class="sm:hidden"> ได้ในที่เดียว</h2><p class="mt-3 max-w-xl text-sm leading-6 text-slate-300">ดูสถานะการชำระเงิน การจัดส่ง และยืนยันรับสินค้าจากร้านค้าได้ในที่เดียว</p></div>
            <div class="customer-hero-icon" aria-hidden="true">▣</div>
        </section>

        <section class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="order-stat"><span>คำสั่งซื้อทั้งหมด</span><strong>{{ $orders->count() }}</strong><small>รายการ</small></div>
            <div class="order-stat"><span>ชำระเงินแล้ว</span><strong>{{ $paidOrders->count() }}</strong><small>รายการ</small></div>
            <div class="order-stat"><span>รอตรวจสอบ</span><strong>{{ $pendingOrders->count() }}</strong><small>รายการ</small></div>
        </section>

        <section class="mt-9"><div class="mb-4 flex flex-wrap items-end justify-between gap-3"><div><p class="eyebrow">ประวัติการสั่งซื้อ</p><h2 class="mt-1 text-xl font-bold text-slate-900">รายการล่าสุด</h2></div><a href="{{ route('home') }}" class="text-sm font-bold text-red-400 hover:text-red-300">เลือกซื้อสินค้าเพิ่ม →</a></div>
            <div class="grid gap-4">
                @forelse($orders as $order)
                    @php($status = $statusLabels[$order->payment_status] ?? ['ไม่ทราบสถานะ', 'order-status-pending'])
                    <a href="{{ route('orders.show', $order) }}" class="order-card group">
                        <div class="order-thumb">
                            @if($order->items->first()?->product?->images->first())<img src="{{ $order->items->first()->product->images->first()->image_url }}" alt="{{ $order->items->first()->product->name }}">@else<span>PC</span>@endif
                        </div>
                        <div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-2"><p class="font-bold text-slate-900">คำสั่งซื้อ #{{ $order->id }}</p><span class="{{ $status[1] }}">{{ $status[0] }}</span></div><p class="mt-1 truncate text-sm text-slate-500">{{ $order->items->pluck('product.name')->join(' · ') }}</p><p class="mt-2 text-xs text-slate-500">สั่งซื้อเมื่อ {{ $order->created_at->format('d M Y · H:i') }} · {{ $order->items->count() }} สินค้า</p></div>
                        <div class="flex items-center gap-4"><p class="text-lg font-bold text-slate-900">฿{{ number_format($order->total_amount, 2) }}</p><span class="order-arrow">→</span></div>
                    </a>
                @empty
                    <div class="card p-10 text-center"><div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-red-500/10 text-2xl text-red-400">⌁</div><h2 class="mt-4 font-bold text-slate-900">ยังไม่มีคำสั่งซื้อ</h2><p class="mt-2 text-sm text-slate-500">เริ่มเลือกฮาร์ดแวร์ที่ต้องการได้เลย</p><a href="{{ route('home') }}" class="btn-primary mt-5">เลือกซื้อสินค้า</a></div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
