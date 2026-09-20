<x-guest-layout>
    <div class="mb-8">
        <p class="text-sm font-semibold tracking-widest text-[#3B38A0]">WELCOME BACK</p>
        <h1 class="mt-2 text-3xl font-bold text-[#1A2A80]">เข้าสู่ระบบ</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">จัดการคำสั่งซื้อ สินค้า และข้อความของคุณ</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl border border-[#B2B0E8] bg-[#f4f4fc] p-3 text-sm text-[#1A2A80]" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="อีเมล" class="font-semibold text-slate-700" />
            <x-text-input id="email" class="mt-2 block min-h-12 w-full rounded-xl border-[#B2B0E8]" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="รหัสผ่าน" class="font-semibold text-slate-700" />

            <x-text-input id="password" class="mt-2 block min-h-12 w-full rounded-xl border-[#B2B0E8]"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-[#7A85C1] text-[#3B38A0] shadow-sm focus:ring-[#7A85C1]" name="remember">
                <span class="ms-2 text-sm text-slate-600">จดจำการเข้าสู่ระบบ</span>
            </label>
        </div>

        <div class="mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-[#3B38A0] hover:text-[#1A2A80]" href="{{ route('password.request') }}">
                    ลืมรหัสผ่าน?
                </a>
            @endif

            <button type="submit" class="btn-primary mt-6 w-full">เข้าสู่ระบบ</button>
        </div>
    </form>
    <p class="mt-7 text-center text-sm text-slate-600">ยังไม่มีบัญชี? <a href="{{ route('register') }}" class="font-bold text-[#3B38A0] hover:text-[#1A2A80]">สมัครสมาชิก</a></p>
</x-guest-layout>
