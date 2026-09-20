<x-app-layout>
    <x-slot name="header"><h1 class="font-bold text-2xl">คำสั่งซื้อ #{{ $order->id }}</h1></x-slot>

    @php($isDirectPayment = $order->payment_method === 'promptpay_direct')
    <div class="max-w-4xl mx-auto space-y-5 p-6">
        <section class="card p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div><p class="text-sm text-slate-500">ยอดชำระทั้งหมด</p><p class="mt-1 text-3xl font-bold text-slate-900">฿{{ number_format($order->total_amount, 2) }}</p></div>
                <span class="rounded-full px-3 py-1 text-sm font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $order->payment_status === 'paid' ? 'ชำระเงินยืนยันแล้ว' : 'รอชำระ / ตรวจสอบสลิป' }}</span>
            </div>

            @if($isDirectPayment)
                <div class="mt-4 rounded-xl border border-red-500/35 bg-red-500/10 p-4 text-sm text-slate-200">ชำระเงินรูปแบบ <b>โอนตรงเข้าร้านค้า</b> — ร้าน {{ $dealer?->dealerProfile?->store_name ?? $dealer?->name }} จะเป็นผู้ตรวจสอบสลิปและยืนยันยอดด้วยตนเอง</div>
            @endif

            @if($order->payment_status === 'pending')
                <div class="mt-6 grid gap-6 rounded-2xl border border-[#B2B0E8] bg-[#f4f4fc] p-5 sm:grid-cols-[190px_1fr]">
                    <div class="rounded-xl bg-white p-3 text-center shadow-sm">@if($qrData)<img class="mx-auto h-40 w-40" src="https://api.qrserver.com/v1/create-qr-code/?size=320x320&amp;data={{ urlencode($qrData) }}" alt="QR Code ชำระเงิน">@else<p class="py-12 text-sm font-bold text-red-700">ร้านค้ายังไม่ได้ตั้งค่า PromptPay</p>@endif<p class="mt-2 text-xs text-slate-500">{{ $isDirectPayment ? 'PromptPay ของร้านค้า' : 'อ้างอิง SPC-'.$order->id }}</p></div>
                    <div><h2 class="text-lg font-bold text-slate-900">สแกน QR เพื่อชำระเงิน</h2><p class="mt-1 text-sm leading-6 text-slate-600">ชำระยอด <b>฿{{ number_format($order->total_amount, 2) }}</b> @if($isDirectPayment)เข้าร้าน <b>{{ $dealer?->dealerProfile?->store_name ?? $dealer?->name }}</b> โดยตรง @endif แล้วแนบสลิปเพื่อส่งให้ร้านค้าตรวจสอบ</p><p class="mt-2 text-xs text-slate-500">{{ $isDirectPayment ? 'เงินจะเข้าบัญชี PromptPay ของร้านค้าโดยตรง' : 'QR นี้เป็นโหมดทดสอบของระบบ' }}</p></div>
                </div>

                <form method="POST" action="{{ route('orders.payment-slip', $order) }}" enctype="multipart/form-data" class="mt-5 rounded-2xl border border-dashed border-[#7A85C1] p-5">@csrf
                    <h2 class="font-bold text-slate-900">แนบสลิปการโอนเงิน</h2>
                    <div class="mt-3 grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
                        <input required type="file" name="payment_slip" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-600">
                        <input name="transaction_ref" value="{{ old('transaction_ref', $order->transaction_ref) }}" placeholder="เลขอ้างอิง (ถ้ามี)" class="min-h-11 rounded-xl border-slate-300">
                        <button class="btn-primary">ส่งสลิป</button>
                    </div>
                    @error('payment_slip')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    @if($order->slip_status === 'pending')<p class="mt-3 text-sm font-medium text-amber-700">ส่งสลิปเมื่อ {{ $order->payment_submitted_at?->format('d/m/Y H:i') }} แล้ว — รอร้านค้าตรวจสอบ</p>@endif
                </form>
                @if($order->slip_status === 'not_submitted' && ! $order->payment_slip_path)
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" class="mt-4 text-right" onsubmit="return confirm('ยกเลิกคำสั่งซื้อนี้ใช่หรือไม่? สินค้าจะกลับไปพร้อมขายทันที')">@csrf @method('DELETE')<button class="text-sm font-bold text-red-400 hover:text-red-300">ยกเลิกคำสั่งซื้อ</button></form>
                @endif
            @else
                <p class="mt-5 text-sm font-medium text-emerald-700">✓ ร้านค้าตรวจสอบสลิปแล้วเมื่อ {{ $order->payment_verified_at?->format('d/m/Y H:i') }} {{ $isDirectPayment ? 'เงินเข้าร้านโดยตรง และรายการสามารถจัดส่งได้' : 'รายการสามารถจัดส่งได้' }}</p>
            @endif

            @if($order->payment_slip_path)
                <div class="mt-5"><p class="mb-2 text-sm font-bold text-slate-900">สลิปที่ส่ง</p><a href="{{ asset('storage/'.$order->payment_slip_path) }}" target="_blank"><img src="{{ asset('storage/'.$order->payment_slip_path) }}" class="max-h-72 rounded-xl border border-slate-200" alt="สลิปการชำระเงิน"></a></div>
            @endif
        </section>

        @foreach($order->items as $item)
            <section class="card p-6"><div class="flex justify-between gap-4"><h2 class="font-bold text-slate-900">{{ $item->product->name }}</h2><b class="text-slate-900">฿{{ number_format($item->price,2) }}</b></div><p class="mt-2 text-sm text-slate-600">ร้าน: {{ $item->dealer->dealerProfile?->store_name ?? $item->dealer->name }}</p><p class="text-sm text-slate-600">จัดส่ง: {{ $item->delivery_status }} · ชำระตรงเข้าร้านค้า</p>@if($order->payment_status === 'paid' && $item->delivery_status === 'shipped')<form method="POST" action="{{ route('orders.confirm-delivery',$order) }}" class="mt-3">@csrf<button class="rounded bg-emerald-600 px-3 py-2 text-white">ยืนยันรับสินค้า</button></form>@endif @if(! $item->dispute)<form method="POST" action="{{ route('disputes.store',$item) }}" class="mt-3">@csrf<textarea name="reason" required placeholder="ปัญหาที่พบ" class="w-full rounded border-gray-300"></textarea><button class="mt-2 text-sm text-red-700">เปิดข้อพิพาท</button></form>@endif
                @if($item->delivery_status === 'delivered')
                    @if($item->dealerReview)
                        <p class="mt-4 text-sm font-semibold text-amber-700">★ คุณให้คะแนนร้านนี้ {{ $item->dealerReview->rating }}/5 แล้ว</p>
                    @else
                        <form method="POST" action="{{ route('dealer-reviews.store', $item) }}" class="mt-4 rounded-xl border border-gray-700 bg-slate-50 p-4">@csrf
                            <p class="font-bold text-slate-900">ให้คะแนนร้านค้า</p><div class="mt-3 grid gap-3 sm:grid-cols-[150px_1fr_auto]"><select required name="rating" class="rounded-xl"><option value="">เลือกคะแนน</option>@for($rating = 5; $rating >= 1; $rating--)<option value="{{ $rating }}">{{ $rating }} ดาว</option>@endfor</select><input name="comment" maxlength="500" placeholder="ความคิดเห็นเพิ่มเติม (ไม่บังคับ)" class="rounded-xl"><button class="btn-primary">ส่งคะแนน</button></div>
                        </form>
                    @endif
                @endif
            </section>
        @endforeach
    </div>
</x-app-layout>
