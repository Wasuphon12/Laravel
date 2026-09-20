<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        ขอบคุณที่สมัครสมาชิก กรุณายืนยันอีเมลโดยกดลิงก์ที่เราส่งให้ หากยังไม่ได้รับอีเมล สามารถขอส่งลิงก์ใหม่ได้
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            ส่งลิงก์ยืนยันใหม่ไปยังอีเมลที่ใช้สมัครแล้ว
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    ส่งอีเมลยืนยันอีกครั้ง
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                ออกจากระบบ
            </button>
        </form>
    </div>
</x-guest-layout>
