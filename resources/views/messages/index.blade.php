<x-app-layout>
    <x-slot name="header"><div class="page-shell"><p class="eyebrow">แชทลูกค้า</p><h1 class="mt-1 font-bold text-2xl text-slate-900">ข้อความ</h1></div></x-slot>
    <div class="max-w-5xl mx-auto p-6">
        <section class="chat-directory"><div><p class="text-sm font-semibold text-red-300">กล่องข้อความลูกค้า</p><h2 class="mt-1 text-2xl font-bold text-white">พูดคุยกับลูกค้า</h2><p class="mt-2 text-sm text-slate-300">เลือกชื่อลูกค้าเพื่อเปิดห้องแชทและตอบกลับเป็นบทสนทนาเดียว</p></div><div class="chat-directory-count">{{ $conversations->count() }}<span>แชท</span></div></section>
        <div class="mt-6 grid gap-3">
            @forelse($conversations as $conversation)
                @php($otherUser = $conversation['user'])
                @php($latest = $conversation['latest'])
                <a href="{{ route('messages.show', $otherUser) }}" class="conversation-card group"><div class="conversation-avatar">{{ mb_strtoupper(mb_substr($otherUser->name, 0, 1)) }}</div><div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-2"><p class="font-bold text-slate-900">{{ $otherUser->name }}</p>@if($conversation['unread'])<span class="unread-count">{{ $conversation['unread'] }} ใหม่</span>@endif</div>@if($latest->product)<p class="mt-1 truncate text-xs font-semibold text-red-400">เกี่ยวกับ: {{ $latest->product->name }}</p>@endif<p class="mt-1 truncate text-sm text-slate-500">{{ $latest->sender_id === auth()->id() ? 'คุณ: ' : '' }}{{ $latest->message }}</p></div><div class="flex items-center gap-3"><time class="hidden text-xs text-slate-500 sm:block">{{ $latest->created_at->diffForHumans() }}</time><span class="chat-button">แชท <span aria-hidden="true">→</span></span></div></a>
            @empty
                <div class="card p-12 text-center"><div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-red-500/10 text-2xl text-red-400">✉</div><h2 class="mt-4 font-bold text-slate-900">ยังไม่มีข้อความ</h2><p class="mt-2 text-sm text-slate-500">เมื่อลูกค้าส่งข้อความถึงร้านค้า จะแสดงเป็นรายชื่อแชทในหน้านี้</p></div>
            @endforelse
        </div>
    </div>
</x-app-layout>
