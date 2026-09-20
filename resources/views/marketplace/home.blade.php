<x-app-layout>
    <div class="page-shell py-8 sm:py-10">
        <section class="palette-hero relative overflow-hidden rounded-3xl px-6 py-10 text-white sm:px-10 sm:py-14">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-red-500/20 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/2 h-40 w-80 rounded-full bg-red-900/40 blur-3xl"></div>
            <div class="relative max-w-2xl">
                <p class="eyebrow">ซื้ออุปกรณ์คอมพิวเตอร์มือสองอย่างมั่นใจ</p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-5xl">ฮาร์ดแวร์ชัดเจน<br>ซื้อขายสบายใจ</h1>
                <p class="mt-5 max-w-xl text-base leading-7 text-slate-300">เลือกได้ทั้งคอมพิวเตอร์ โน้ตบุ๊ก CPU, GPU, RAM, SSD/HDD และอุปกรณ์อื่น ๆ จากร้านค้าที่ผ่าน KYC</p>
                <div class="mt-6 flex flex-wrap gap-2"><span class="gamer-chip">⚡ สเปกชัดเจน</span><span class="gamer-chip">✓ ร้านค้าผ่าน KYC</span><span class="gamer-chip">▣ โอนตรงเข้าร้าน</span></div>
                @guest
                    <a href="{{ route('register') }}" class="btn-primary mt-7">สมัครสมาชิกเพื่อเริ่มใช้งาน</a>
                @endguest
            </div>
        </section>

        <section class="palette-search card -mt-5 relative mx-auto max-w-5xl p-4 sm:-mt-6">
            <form class="space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <label class="sr-only" for="search">ค้นหาสินค้า</label>
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#7A85C1]">⌕</span>
                        <input id="search" name="search" value="{{ request('search') }}" placeholder="ค้นหาสินค้า, แบรนด์ หรือชื่อร้านค้า" class="min-h-12 w-full rounded-xl border-[#B2B0E8] bg-white pl-10 pr-4">
                    </div>
                    <button class="btn-primary min-w-32">ค้นหาสินค้า</button>
                    @if (request()->hasAny(['search', 'category', 'cpu_model', 'cpu_gen', 'ram_capacity', 'ram_type', 'gpu_series', 'storage']))
                        <a href="{{ route('home') }}" class="btn-secondary min-w-28 border-[#7A85C1] bg-white">ล้าง</a>
                    @endif
                </div>
                <div class="border-t border-[#7A85C1]/30 pt-4">
                    <p class="mb-3 text-xs font-bold tracking-widest text-[#3B38A0]">กรองตามสเปก</p>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600" for="category">หมวดสินค้า</label>
                            <select id="category" name="category" class="min-h-11 w-full rounded-xl border-[#B2B0E8] bg-white">
                        <option value="">ทุกหมวดสินค้า</option>
                        @foreach ($filterOptions['category'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['category'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600" for="cpu_model">รุ่น CPU</label>
                    <select id="cpu_model" name="cpu_model" class="min-h-11 w-full rounded-xl border-[#B2B0E8] bg-white">
                        <option value="">CPU ทุกรุ่น</option>
                        @foreach ($filterOptions['cpu_model'] as $value => $label)<option value="{{ $value }}" @selected(($filters['cpu_model'] ?? '') === $value)>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600" for="cpu_gen">เจเนอเรชัน CPU</label>
                    <select id="cpu_gen" name="cpu_gen" class="min-h-11 w-full rounded-xl border-[#B2B0E8] bg-white">
                        <option value="">ทุก Gen</option>
                        @foreach ($filterOptions['cpu_gen'] as $value => $label)<option value="{{ $value }}" @selected(($filters['cpu_gen'] ?? '') === $value)>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600" for="ram_capacity">RAM (GB)</label>
                    <select id="ram_capacity" name="ram_capacity" class="min-h-11 w-full rounded-xl border-[#B2B0E8] bg-white">
                        <option value="">ทุกขนาด</option>
                        @foreach ($filterOptions['ram_capacity'] as $option)<option value="{{ $option }}" @selected(($filters['ram_capacity'] ?? '') === $option)>{{ $option }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600" for="ram_type">ชนิด RAM</label>
                    <select id="ram_type" name="ram_type" class="min-h-11 w-full rounded-xl border-[#B2B0E8] bg-white">
                        <option value="">ทุกชนิด</option>
                        @foreach ($filterOptions['ram_type'] as $option)<option value="{{ $option }}" @selected(($filters['ram_type'] ?? '') === $option)>{{ $option }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600" for="gpu_series">ซีรีส์ GPU</label>
                    <select id="gpu_series" name="gpu_series" class="min-h-11 w-full rounded-xl border-[#B2B0E8] bg-white">
                        <option value="">GPU ทุกซีรีส์</option>
                        @foreach ($filterOptions['gpu_series'] as $value => $label)<option value="{{ $value }}" @selected(($filters['gpu_series'] ?? '') === $value)>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600" for="storage">พื้นที่เก็บข้อมูล</label>
                    <select id="storage" name="storage" class="min-h-11 w-full rounded-xl border-[#B2B0E8] bg-white">
                        <option value="">SSD / HDD ทั้งหมด</option>
                        @foreach ($filterOptions['storage'] as $option)<option value="{{ $option }}" @selected(($filters['storage'] ?? '') === $option)>{{ $option }}</option>@endforeach
                    </select>
                </div>
                    </div>
                </div>
            </form>
        </section>

        <div class="mb-6 mt-10 flex items-end justify-between gap-4">
            <div><p class="eyebrow">เลือกให้ตรงกับคุณ</p><h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">สินค้าแนะนำ</h2><p class="mt-1 text-sm text-slate-500">จากร้านค้าที่ผ่านการยืนยันตัวตน</p></div>
            <span class="rounded-full bg-[#B2B0E8]/40 px-3 py-1.5 text-sm font-medium text-[#1A2A80]">{{ $products->total() }} รายการ</span>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($products as $product)
                <a href="{{ route('products.show', $product) }}" class="product-card group card overflow-hidden transition duration-200 hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-red-500/40">
                    @if ($product->images->first())
                        <img src="{{ $product->images->first()->image_url }}" onerror="this.onerror=null;this.src='https://placehold.co/800x500/7f1d1d/f9fafb?text=SecondPC+Product';" class="h-48 w-full object-cover transition duration-300 group-hover:scale-105" alt="{{ $product->name }}">
                    @else
                        <div class="product-art"><span>{{ $filterOptions['category'][$product->category] ?? $product->category }}</span></div>
                    @endif
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-2"><span class="rounded-full bg-[#B2B0E8]/35 px-2.5 py-1 text-xs font-bold text-[#1A2A80]">{{ $filterOptions['category'][$product->category] ?? $product->category }}</span><span class="text-xs text-slate-400">เกรด {{ $product->condition_grade }}</span></div>
                        <h2 class="mt-3 line-clamp-2 min-h-12 font-bold leading-6 text-slate-900">{{ $product->name }}</h2>
                        <p class="mt-3 text-xl font-bold text-slate-900">฿{{ number_format($product->price, 2) }}</p>
                        <p class="mt-3 truncate text-sm text-slate-500">{{ $product->dealer->dealerProfile?->store_name }}</p>
                        @if($product->dealer->dealerProfile?->reviews_count)
                            <p class="mt-1 text-xs font-semibold text-amber-700">★ {{ number_format((float) $product->dealer->dealerProfile->reviews_avg_rating, 1) }} <span class="font-normal text-slate-500">({{ $product->dealer->dealerProfile->reviews_count }} รีวิว)</span></p>
                        @else
                            <p class="mt-1 text-xs text-slate-500">ร้านค้าใหม่ ยังไม่มีรีวิว</p>
                        @endif
                        <p class="mt-1 text-xs text-slate-400">ลงขาย {{ $product->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </a>
            @empty
                <p class="card col-span-full p-10 text-center text-slate-500">ยังไม่มีสินค้าที่ตรงกับคำค้นหา</p>
            @endforelse
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    </div>
</x-app-layout>
