<x-app-layout>
    <x-slot name="header"><div class="page-shell"><a href="{{ route('home') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">← กลับไปเลือกสินค้า</a></div></x-slot>
    <div class="page-shell grid gap-7 py-8 lg:grid-cols-[minmax(0,1.25fr)_minmax(340px,0.75fr)] lg:py-10">
        <div class="space-y-4">
            @if($product->images->isNotEmpty())
                <div x-data="{ images: @js($product->images->pluck('image_url')->values()), active: 0, next() { this.active = (this.active + 1) % this.images.length }, previous() { this.active = (this.active - 1 + this.images.length) % this.images.length } }" class="product-gallery card overflow-hidden">
                    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100"><img :src="images[active]" onerror="this.onerror=null;this.src='https://placehold.co/800x500/7f1d1d/f9fafb?text=SecondPC+Product';" class="h-full w-full object-contain" alt="{{ $product->name }}">
                        <template x-if="images.length > 1"><button type="button" @click="previous" class="gallery-control left-4" aria-label="รูปก่อนหน้า">‹</button></template>
                        <template x-if="images.length > 1"><button type="button" @click="next" class="gallery-control right-4" aria-label="รูปถัดไป">›</button></template>
                        <template x-if="images.length > 1"><div class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-black/60 px-3 py-1 text-xs font-bold text-white"><span x-text="active + 1"></span> / <span x-text="images.length"></span></div></template>
                    </div>
                    <template x-if="images.length > 1"><div class="flex gap-2 overflow-x-auto border-t border-gray-700 p-3"><template x-for="(image, index) in images" :key="image"><button type="button" @click="active = index" :class="active === index ? 'gallery-thumb-active' : ''" class="gallery-thumb"><img :src="image" onerror="this.onerror=null;this.src='https://placehold.co/800x500/7f1d1d/f9fafb?text=SecondPC';" alt="รูปสินค้า"></button></template></div></template>
                </div>
            @else
                <div class="card grid aspect-[4/3] place-items-center bg-slate-100 text-slate-400">ไม่มีรูปสินค้า</div>
            @endif
            <section class="card p-6"><h2 class="text-lg font-bold text-slate-900">รายละเอียดสินค้า</h2><p class="mt-3 whitespace-pre-line leading-7 text-slate-600">{{ $product->description }}</p></section>
        </div>
        <aside class="card h-fit p-6 lg:sticky lg:top-6">
            <div class="flex items-center justify-between gap-3"><span class="text-sm text-emerald-700">● พร้อมจำหน่าย</span><span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-bold text-emerald-700">เหลือ {{ number_format($product->stock_quantity) }} ชิ้น</span></div>
            <h1 class="mt-4 text-2xl font-bold leading-8 text-slate-900">{{ $product->name }}</h1><p class="mt-4 text-3xl font-bold tracking-tight text-slate-900">฿{{ number_format($product->price, 2) }}</p><p class="mt-2 text-sm text-slate-500">ลงขายเมื่อ {{ $product->created_at->format('d/m/Y H:i') }}</p>
            <div class="my-6 border-t border-slate-100"></div><h2 class="font-bold text-slate-900">สเปกเครื่อง</h2>
            <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">@foreach($product->specs as $key => $value)<div class="rounded-xl bg-slate-50 p-3"><dt class="text-xs font-medium uppercase text-slate-500">{{ $key }}</dt><dd class="mt-1 font-semibold text-slate-800">{{ $value }}</dd></div>@endforeach</dl>
            @php($profile = $product->dealer->dealerProfile)
            <div class="mt-6 rounded-xl bg-slate-50 p-4"><p class="text-xs font-semibold text-slate-500">จัดจำหน่ายโดย</p><p class="mt-1 font-bold text-slate-900">{{ $profile?->store_name ?? $product->dealer->name }}</p><p class="mt-2 text-sm text-emerald-700">✓ ร้านค้าผ่านการยืนยันตัวตน</p>@if($profile?->reviews_count)<p class="mt-2 text-sm font-bold text-amber-700">★ {{ number_format((float) $profile->reviews_avg_rating, 1) }} <span class="font-normal text-slate-500">จาก {{ $profile->reviews_count }} รีวิว</span></p>@else<p class="mt-2 text-sm text-slate-500">ร้านค้าใหม่ ยังไม่มีรีวิว</p>@endif</div>
            @auth
                @if(auth()->user()->role === 'customer' && $product->status === 'available')
                    <div class="mt-6 grid gap-2 sm:grid-cols-2">
                        <a href="{{ route('checkout.create', ['products' => $product->id]) }}" class="btn-primary w-full">ซื้อทันที</a>
                        <form method="POST" action="{{ route('cart.add', $product) }}">@csrf<button class="btn-secondary w-full">เพิ่มลงตะกร้า</button></form>
                    </div>
                    <form method="POST" action="{{ route('messages.store') }}" class="mt-3">@csrf<input type="hidden" name="receiver_id" value="{{ $product->dealer_id }}"><input type="hidden" name="product_id" value="{{ $product->id }}"><label class="sr-only" for="message">ข้อความถึงร้านค้า</label><input id="message" required name="message" placeholder="สอบถามร้านค้า" class="min-h-11 w-full rounded-xl border-slate-200"><button class="mt-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">ส่งข้อความถึงร้านค้า</button></form>
                @endif
            @else
                <a href="{{ route('register') }}" class="btn-primary mt-6 w-full">สมัครสมาชิกเพื่อสั่งซื้อ</a><a href="{{ route('login') }}" class="mt-3 block text-center text-sm font-semibold text-indigo-700">มีบัญชีอยู่แล้ว? เข้าสู่ระบบ</a>
            @endauth
        </aside>
    </div>
</x-app-layout>
