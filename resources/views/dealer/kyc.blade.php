<x-app-layout>
    <x-slot name="header">
        <div class="page-shell">
            <p class="eyebrow">การยืนยันร้านค้า</p>
            <h1 class="mt-1 font-bold text-2xl text-slate-900">ยืนยันตัวตนร้านค้า (KYC)</h1>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('dealer.kyc.save') }}" enctype="multipart/form-data" class="card mx-auto my-8 max-w-2xl space-y-5 p-6">
        @csrf
        @method('PUT')

        <p class="text-sm text-slate-300">สถานะ: <b>{{ $profile?->status ?? 'ยังไม่ส่งข้อมูล' }}</b></p>

        <div>
            <label for="store_name" class="mb-2 block text-sm font-bold text-slate-100">ชื่อร้าน</label>
            <input id="store_name" required name="store_name" value="{{ old('store_name', $profile?->store_name) }}" class="w-full rounded border-gray-300">
            @error('store_name')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="rounded-xl border border-dashed border-slate-500 bg-slate-900/50 p-4">
            <label for="id_card_file" class="block cursor-pointer text-sm font-bold text-slate-100">แนบรูปบัตรประชาชน / เอกสารทะเบียน</label>
            <p id="id_card_help" class="mt-1 text-xs text-slate-400">รองรับ JPG, PNG, WebP ขนาดไม่เกิน 5 MB {{ $profile?->id_card_url ? 'หากไม่เลือกไฟล์ใหม่ ระบบจะใช้เอกสารเดิม' : '' }}</p>
            <input id="id_card_file" name="id_card_file" type="file" accept="image/jpeg,image/png,image/webp" aria-describedby="id_card_help" @if (! $profile?->id_card_url) required @endif class="mt-3 block w-full text-sm text-slate-200 file:mr-4 file:rounded-lg file:border-0 file:bg-red-500 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-red-600">
            @if ($profile?->id_card_url)
                <a href="{{ $profile->id_card_url }}" target="_blank" rel="noopener" class="mt-3 inline-block text-sm font-semibold text-red-300 underline underline-offset-4 hover:text-red-200">ดูเอกสารที่ส่งปัจจุบัน</a>
            @endif
            @error('id_card_file')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="bank_name" class="mb-2 block text-sm font-bold text-slate-100">ธนาคาร</label>
                <input id="bank_name" required name="bank_name" value="{{ old('bank_name', $profile?->bank_name) }}" class="w-full rounded border-gray-300">
                @error('bank_name')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="bank_account" class="mb-2 block text-sm font-bold text-slate-100">เลขที่บัญชี</label>
                <input id="bank_account" required name="bank_account" value="{{ old('bank_account', $profile?->bank_account) }}" inputmode="numeric" class="w-full rounded border-gray-300">
                @error('bank_account')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="promptpay_id" class="mb-2 block text-sm font-bold text-slate-100">PromptPay สำหรับรับเงินโดยตรง</label>
            <input id="promptpay_id" required name="promptpay_id" value="{{ old('promptpay_id', $profile?->promptpay_id) }}" inputmode="numeric" pattern="(?:\d{10}|\d{13}|\d{15})" aria-describedby="promptpay_help" placeholder="เบอร์มือถือ 10 หลัก หรือเลขบัตร/Tax ID" class="w-full rounded border-gray-300">
            <p id="promptpay_help" class="mt-2 text-xs text-slate-400">ระบบจะสร้าง QR ของร้านจากหมายเลขนี้ ลูกค้าจะโอนเข้าร้านโดยตรง</p>
            @error('promptpay_id')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="btn-primary">ส่งตรวจสอบ</button>
    </form>
</x-app-layout>
