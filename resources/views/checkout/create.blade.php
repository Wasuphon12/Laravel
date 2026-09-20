<x-app-layout>
    <x-slot name="header">
        <div class="page-shell">
            <p class="eyebrow">ระบบชำระเงินสาธิต</p>
            <h1 class="mt-1 font-bold text-2xl text-slate-900">สร้าง QR ชำระเงินจำลอง</h1>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('checkout.store') }}" class="page-shell max-w-5xl space-y-6 py-8 sm:py-10">
        @csrf
        <input type="hidden" name="payment_method" value="demo_gateway">
        @foreach($products as $product)<input type="hidden" name="product_ids[]" value="{{ $product->id }}">@endforeach

        <section class="direct-payment-intro">
            <div>
                <p class="eyebrow">ขั้นตอนการชำระเงิน</p>
                <h2 class="mt-1 text-xl font-bold text-white">ระบบรับชำระเงินแบบสาธิต</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">ระบบจะสร้างคิวอาร์โค้ดเฉพาะแต่ละร้าน มีอายุ 15 นาที และใช้ปุ่มจำลองการแจ้งผลชำระเงินกลับ เพื่อยืนยันการชำระเงินอัตโนมัติ ไม่มีการโอนเงินจริงหรือแนบสลิป</p>
            </div>
            <div class="direct-payment-total"><span>ยอดรวมทั้งหมด</span><strong>฿{{ number_format($total, 2) }}</strong></div>
        </section>

        @foreach($paymentGroups as $group)
            <section class="direct-payment-card">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div><p class="eyebrow">ร้านค้า</p><h2 class="mt-1 text-lg font-bold text-slate-900">{{ $group['dealer']->dealerProfile?->store_name ?? $group['dealer']->name }}</h2><p class="mt-1 text-xs font-semibold text-amber-300">⌁ จะสร้าง QR จำลองหลังยืนยันคำสั่งซื้อ</p></div>
                    <div class="text-right"><p class="text-xs text-slate-500">ยอดจำลองของร้านนี้</p><p class="mt-1 text-2xl font-bold text-slate-900">฿{{ number_format($group['total'], 2) }}</p></div>
                </div>
                <ul class="mt-5 space-y-2 border-t border-gray-700 pt-4 text-sm leading-6 text-slate-300" role="list">
                    @foreach($group['products'] as $product)<li>• {{ $product->name }}</li>@endforeach
                </ul>
            </section>
        @endforeach

        <button type="submit" class="btn-primary w-full">สร้างคิวอาร์โค้ดจำลองสำหรับ {{ $paymentGroups->count() }} ร้านค้า · ยอดรวม ฿{{ number_format($total, 2) }}</button>
    </form>
</x-app-layout>
