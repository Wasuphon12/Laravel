<x-app-layout>
    <x-slot name="header">
        <div class="page-shell">
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-red-300 hover:text-red-200">← กลับแผงผู้ดูแลระบบ</a>
            <p class="eyebrow mt-4">ตรวจสอบ KYC</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">ข้อมูลที่ร้านค้ายื่น</h1>
        </div>
    </x-slot>

    <main class="page-shell mx-auto max-w-4xl py-8 sm:py-10">
        <section class="card space-y-7 p-6 sm:p-8" aria-labelledby="kyc-heading">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-700 pb-6">
                <div>
                    <p class="eyebrow">คำขอยืนยันตัวตนร้านค้า</p>
                    <h2 id="kyc-heading" class="mt-1 text-xl font-bold text-white">{{ $profile->store_name }}</h2>
                    <p class="mt-2 text-sm text-slate-300">ยื่นเมื่อ {{ $profile->created_at->translatedFormat('j F Y เวลา H:i น.') }}</p>
                </div>
                <span class="rounded-full border border-amber-400/30 bg-amber-400/10 px-3 py-1 text-sm font-bold text-amber-200">สถานะ: {{ $profile->status === 'pending' ? 'รอตรวจสอบ' : $profile->status }}</span>
            </div>

            <section aria-labelledby="account-heading">
                <h3 id="account-heading" class="text-base font-bold text-white">ข้อมูลบัญชีผู้ยื่น</h3>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-900/70 p-4"><dt class="text-sm text-slate-400">ชื่อบัญชี</dt><dd class="mt-1 font-bold text-white">{{ $profile->user->name }}</dd></div>
                    <div class="rounded-xl bg-slate-900/70 p-4"><dt class="text-sm text-slate-400">อีเมล</dt><dd class="mt-1 break-all font-bold text-white">{{ $profile->user->email }}</dd></div>
                </dl>
            </section>

            <section aria-labelledby="payment-heading">
                <h3 id="payment-heading" class="text-base font-bold text-white">ข้อมูลรับเงินของร้าน</h3>
                <dl class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-xl bg-slate-900/70 p-4"><dt class="text-sm text-slate-400">ธนาคาร</dt><dd class="mt-1 font-bold text-white">{{ $profile->bank_name }}</dd></div>
                    <div class="rounded-xl bg-slate-900/70 p-4"><dt class="text-sm text-slate-400">เลขที่บัญชี</dt><dd class="mt-1 font-bold text-white">{{ $profile->bank_account }}</dd></div>
                    <div class="rounded-xl bg-slate-900/70 p-4"><dt class="text-sm text-slate-400">PromptPay</dt><dd class="mt-1 font-bold text-white">{{ $profile->promptpay_id }}</dd></div>
                </dl>
            </section>

            <section aria-labelledby="document-heading">
                <h3 id="document-heading" class="text-base font-bold text-white">เอกสารประกอบการยืนยัน</h3>
                @if ($profile->id_card_url)
                    <figure class="mt-4 overflow-hidden rounded-xl border border-slate-700 bg-slate-900/70 p-3">
                        <img src="{{ $profile->id_card_url }}" alt="เอกสารยืนยันตัวตนของร้าน {{ $profile->store_name }}" width="1200" height="800" class="max-h-[36rem] w-full rounded-lg object-contain" />
                        <figcaption class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-300">
                            <span>เอกสารที่ผู้ขายแนบไว้สำหรับตรวจสอบ</span>
                            <a href="{{ $profile->id_card_url }}" target="_blank" rel="noopener" class="font-semibold text-red-300 underline underline-offset-4 hover:text-red-200">เปิดรูปขนาดเต็ม</a>
                        </figcaption>
                    </figure>
                @else
                    <p class="mt-3 rounded-xl border border-amber-400/30 bg-amber-400/10 p-4 text-sm text-amber-100">ไม่พบเอกสารแนบ โปรดปฏิเสธคำขอนี้เพื่อให้ร้านค้ายื่นข้อมูลใหม่</p>
                @endif
            </section>

            @if ($profile->status === 'pending')
                <form method="POST" action="{{ route('admin.kyc.update', $profile) }}" class="flex flex-wrap gap-3 border-t border-slate-700 pt-6">
                    @csrf
                    @method('PATCH')
                    <button name="status" value="approved" class="admin-action admin-action-ok">อนุมัติร้านค้า</button>
                    <button name="status" value="rejected" class="admin-action">ปฏิเสธคำขอ</button>
                </form>
            @endif
        </section>
    </main>
</x-app-layout>
