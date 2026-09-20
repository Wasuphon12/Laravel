<x-app-layout>
    <x-slot name="header">
        <div class="page-shell">
            <p class="eyebrow">ข้อมูลสำหรับจัดส่ง</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">ที่อยู่</h1>
        </div>
    </x-slot>

    <main class="profile-page py-10 sm:py-12">
        <section class="profile-panel mx-auto max-w-2xl p-5 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-slate-100">ที่อยู่จัดส่งเริ่มต้น</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-400">ระบบจะใช้ข้อมูลนี้กับคำสั่งซื้อใหม่โดยอัตโนมัติ กรุณาตรวจสอบให้ถูกต้องก่อนสั่งซื้อ</p>
                </div>
                <span class="rounded-full border border-red-500/30 bg-red-500/10 px-3 py-1 text-xs font-bold text-red-300">ใช้กับคำสั่งซื้อใหม่</span>
            </div>

            @if (session('status') === 'address-updated')
                <div class="mt-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm font-medium text-emerald-300" role="status">บันทึกที่อยู่จัดส่งแล้ว</div>
            @endif

            @if (session('info'))
                <div class="mt-6 rounded-xl border border-amber-400/30 bg-amber-400/10 px-4 py-3 text-sm font-medium text-amber-200" role="status">{{ session('info') }}</div>
            @endif

            <form method="POST" action="{{ route('address.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PATCH')

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="shipping_name" class="mb-2 block text-sm font-semibold text-slate-200">ชื่อผู้รับ</label>
                        <input id="shipping_name" name="shipping_name" type="text" required autocomplete="name" value="{{ old('shipping_name', $user->shipping_name) }}" class="min-h-12 w-full rounded-xl" placeholder="ชื่อ-นามสกุล">
                        @error('shipping_name')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="shipping_phone" class="mb-2 block text-sm font-semibold text-slate-200">เบอร์โทรศัพท์</label>
                        <input id="shipping_phone" name="shipping_phone" type="tel" required autocomplete="tel" inputmode="tel" value="{{ old('shipping_phone', $user->shipping_phone) }}" class="min-h-12 w-full rounded-xl" placeholder="เช่น 0812345678">
                        @error('shipping_phone')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="shipping_address" class="mb-2 block text-sm font-semibold text-slate-200">ที่อยู่</label>
                    <textarea id="shipping_address" name="shipping_address" required autocomplete="street-address" rows="4" class="w-full rounded-xl" placeholder="บ้านเลขที่ ถนน แขวง/ตำบล เขต/อำเภอ จังหวัด">{{ old('shipping_address', $user->shipping_address) }}</textarea>
                    @error('shipping_address')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror
                </div>

                <div class="max-w-xs">
                    <label for="shipping_postcode" class="mb-2 block text-sm font-semibold text-slate-200">รหัสไปรษณีย์</label>
                    <input id="shipping_postcode" name="shipping_postcode" required autocomplete="postal-code" inputmode="numeric" value="{{ old('shipping_postcode', $user->shipping_postcode) }}" class="min-h-12 w-full rounded-xl" placeholder="เช่น 10110">
                    @error('shipping_postcode')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit" class="btn-primary">บันทึกที่อยู่</button>
                    <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-gray-600 px-4 text-sm font-semibold text-slate-200 transition hover:border-gray-500 hover:bg-gray-800">กลับหน้าแรก</a>
                </div>
            </form>
        </section>
    </main>
</x-app-layout>
