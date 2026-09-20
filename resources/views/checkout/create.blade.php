<x-app-layout>
    <x-slot name="header"><div class="page-shell"><p class="eyebrow">Direct dealer payment</p><h1 class="mt-1 font-bold text-2xl text-slate-900">ชำระเงินแยกตามร้านค้า</h1></div></x-slot>

    <form method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data" class="page-shell max-w-5xl space-y-6 py-8 sm:py-10">@csrf
        <input type="hidden" name="payment_method" value="promptpay_direct">
        @foreach($products as $product)<input type="hidden" name="product_ids[]" value="{{ $product->id }}">@endforeach

        <section class="direct-payment-intro"><div><p class="eyebrow">Split payment</p><h2 class="mt-1 text-xl font-bold text-white">โอนตรงเข้าบัญชีของแต่ละร้าน</h2><p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">ตะกร้านี้มีสินค้า {{ $products->count() }} รายการจาก {{ $paymentGroups->count() }} ร้าน ระบบจะแยกยอดและ QR ให้แต่ละร้านโดยอัตโนมัติ</p></div><div class="direct-payment-total"><span>ยอดรวมทั้งหมด</span><strong>฿{{ number_format($total, 2) }}</strong></div></section>

        @foreach($paymentGroups as $group)
            @php($dealerKey = $group['dealer']->id)
            <fieldset class="direct-payment-card">
                <legend class="sr-only">การชำระเงินให้ร้าน {{ $group['dealer']->dealerProfile?->store_name ?? $group['dealer']->name }}</legend>
                <input type="hidden" name="payments[{{ $dealerKey }}][dealer_id]" value="{{ $dealerKey }}">
                <div class="flex flex-wrap items-start justify-between gap-3"><div><p class="eyebrow">ร้านค้า</p><h2 class="mt-1 text-lg font-bold text-slate-900">{{ $group['dealer']->dealerProfile?->store_name ?? $group['dealer']->name }}</h2><p class="mt-1 text-xs text-emerald-700">✓ รับเงินเข้าบัญชีร้านโดยตรง</p></div><div class="text-right"><p class="text-xs text-slate-500">ยอดที่ต้องโอนให้ร้านนี้</p><p class="mt-1 text-2xl font-bold text-slate-900">฿{{ number_format($group['total'], 2) }}</p></div></div>
                <div class="mt-5 grid gap-5 lg:grid-cols-[12rem_1fr]">
                    <div class="direct-qr-box">
                        @if($group['qr_data'])
                            <img class="mx-auto h-40 w-40" src="https://api.qrserver.com/v1/create-qr-code/?size=320x320&amp;data={{ urlencode($group['qr_data']) }}" alt="PromptPay QR ของร้าน {{ $group['dealer']->dealerProfile?->store_name }}">
                            <p class="mt-2 text-xs font-bold text-slate-300">PromptPay {{ $group['promptpay_id'] }}</p>
                        @else
                            <p class="text-sm font-bold text-red-300">ร้านค้ายังไม่ได้ตั้งค่า PromptPay</p>
                        @endif
                    </div>
                    <div><p class="font-bold text-slate-900">1. สแกน QR และโอน <span class="text-red-300">฿{{ number_format($group['total'], 2) }}</span></p><ul class="mt-3 space-y-2 text-sm leading-6 text-slate-400">@foreach($group['products'] as $product)<li>• {{ $product->name }}</li>@endforeach</ul><div class="mt-5 grid gap-3 sm:grid-cols-2"><div><label for="slip_{{ $dealerKey }}" class="mb-2 block text-sm font-bold text-slate-900">2. แนบสลิปของร้านนี้</label><input id="slip_{{ $dealerKey }}" required type="file" name="payments[{{ $dealerKey }}][payment_slip]" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-300" aria-describedby="slip_help_{{ $dealerKey }}"><p id="slip_help_{{ $dealerKey }}" class="mt-2 text-xs text-slate-500">JPG, PNG, WebP ไม่เกิน 5 MB</p>@error("payments.$dealerKey.payment_slip")<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror</div><div><label for="reference_{{ $dealerKey }}" class="mb-2 block text-sm font-bold text-slate-900">เลขอ้างอิงการโอน <span class="font-normal text-slate-500">(ถ้ามี)</span></label><input id="reference_{{ $dealerKey }}" name="payments[{{ $dealerKey }}][transaction_ref]" value="{{ old("payments.$dealerKey.transaction_ref") }}" placeholder="เช่น 1234567890" class="min-h-12 w-full rounded-xl"></div></div></div>
                </div>
            </fieldset>
        @endforeach
        @error('payments')<p class="text-sm font-bold text-red-400">{{ $message }}</p>@enderror

        <section class="card p-6"><h2 class="font-bold text-slate-900">ที่อยู่จัดส่ง <span class="text-xs font-normal text-slate-500">ใช้ร่วมกับทุกร้านค้า</span></h2><div class="mt-4 grid gap-3 sm:grid-cols-2"><div><label class="sr-only" for="shipping_name">ชื่อผู้รับ</label><input id="shipping_name" required name="shipping_address[name]" value="{{ old('shipping_address.name') }}" autocomplete="name" placeholder="ชื่อผู้รับ" class="min-h-12 w-full rounded-xl"></div><div><label class="sr-only" for="shipping_phone">เบอร์โทร</label><input id="shipping_phone" required name="shipping_address[phone]" value="{{ old('shipping_address.phone') }}" autocomplete="tel" inputmode="tel" placeholder="เบอร์โทร" class="min-h-12 w-full rounded-xl"></div><div class="sm:col-span-2"><label class="sr-only" for="shipping_address">ที่อยู่</label><textarea id="shipping_address" required name="shipping_address[address]" autocomplete="street-address" placeholder="ที่อยู่" class="min-h-28 w-full rounded-xl">{{ old('shipping_address.address') }}</textarea></div><div><label class="sr-only" for="shipping_postcode">รหัสไปรษณีย์</label><input id="shipping_postcode" required name="shipping_address[postcode]" value="{{ old('shipping_address.postcode') }}" autocomplete="postal-code" inputmode="numeric" placeholder="รหัสไปรษณีย์" class="min-h-12 w-full rounded-xl"></div></div></section>
        <button class="btn-primary w-full">ส่งสลิปให้ {{ $paymentGroups->count() }} ร้านค้า · ยอดรวม ฿{{ number_format($total, 2) }}</button>
    </form>
</x-app-layout>
