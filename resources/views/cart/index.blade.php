<x-app-layout>
    <x-slot name="header">
        <div class="page-shell flex items-center justify-between gap-4">
            <div><p class="eyebrow">Presentation checkout</p><h1 class="mt-1 text-2xl font-bold text-slate-900">ตะกร้าสินค้า</h1></div>
            <a href="{{ route('home') }}" class="text-sm font-bold text-red-400 hover:text-red-300">← เลือกสินค้าเพิ่ม</a>
        </div>
    </x-slot>

    <div class="page-shell py-8 sm:py-10">
        @if($products->isNotEmpty())
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
                <section class="space-y-3">
                    @foreach($products as $product)
                        <article class="cart-item">
                            <div class="cart-item-image">
                                @if($product->images->first())<img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}">@else<span>PC</span>@endif
                            </div>
                            <div class="min-w-0 flex-1"><p class="text-xs font-bold text-red-300">{{ $product->dealer->dealerProfile?->store_name }}</p><a href="{{ route('products.show', $product) }}" class="mt-1 block font-bold text-slate-900 hover:text-red-300">{{ $product->name }}</a><p class="mt-1 text-sm text-slate-500">พร้อมจำหน่าย</p></div>
                            <div class="flex flex-col items-end gap-3"><p class="text-lg font-bold text-slate-900">฿{{ number_format($product->price, 2) }}</p><form method="POST" action="{{ route('cart.remove', $product) }}">@csrf @method('DELETE')<button class="text-xs font-bold text-slate-400 hover:text-red-300">นำออก</button></form></div>
                        </article>
                    @endforeach
                </section>
                <aside class="cart-summary h-fit"><p class="eyebrow">Order summary</p><h2 class="mt-1 text-lg font-bold text-white">สรุปรายการสั่งซื้อ</h2><div class="my-5 border-t border-gray-700"></div><div class="flex items-center justify-between text-sm text-slate-300"><span>{{ $products->count() }} รายการ</span><span>รวมสินค้า</span></div><div class="mt-2 flex items-end justify-between"><span class="text-sm text-slate-400">ยอดชำระทั้งหมด</span><strong class="text-2xl text-white">฿{{ number_format($total, 2) }}</strong></div><a href="{{ route('checkout.create', ['products' => $products->pluck('id')->join(',')]) }}" class="btn-primary mt-6 w-full">ไปชำระเงิน</a><form method="POST" action="{{ route('cart.clear') }}" class="mt-2">@csrf @method('DELETE')<button class="btn-secondary w-full">ล้างตะกร้า</button></form><p class="mt-4 text-center text-xs leading-5 text-slate-400">สำหรับการนำเสนอ ระบบจะแสดง QR และรับสลิปจำลองในขั้นตอนถัดไป</p></aside>
            </div>
        @else
            <section class="card mx-auto max-w-lg p-10 text-center"><div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-red-500/10 text-2xl text-red-300">⌁</div><h2 class="mt-4 text-xl font-bold text-slate-900">ตะกร้ายังว่าง</h2><p class="mt-2 text-sm text-slate-500">เลือกอุปกรณ์ที่ต้องการ แล้วกลับมาชำระเงินพร้อมกันได้</p><a href="{{ route('home') }}" class="btn-primary mt-5">เลือกซื้อสินค้า</a></section>
        @endif
    </div>
</x-app-layout>
