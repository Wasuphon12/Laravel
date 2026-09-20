@auth
<nav x-data="{ open: false }" class="bg-[#1F2937] border-b border-gray-700 text-white">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-red-500" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">เลือกซื้อสินค้า</x-nav-link>
                    @if (Auth::user()->role === 'customer')
                        <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">ตะกร้า @if($cartCount)<span class="nav-count">{{ $cartCount }}</span>@endif</x-nav-link>
                    @endif
                    @if (Auth::user()->isDealer())
                        <x-nav-link :href="route('dealer.products')" :active="request()->routeIs('dealer.products*')">สินค้าร้าน</x-nav-link>
                        <x-nav-link :href="route('dealer.kyc')" :active="request()->routeIs('dealer.kyc')">KYC</x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative" x-data="{ notificationOpen: false }">
                    <button type="button" class="nav-notification" @click="notificationOpen = !notificationOpen" :aria-expanded="notificationOpen" aria-controls="notification-menu" aria-label="เปิดการแจ้งเตือน">
                        <span aria-hidden="true">🔔</span>
                        @if($unreadMessageCount)<span class="nav-count">{{ $unreadMessageCount > 9 ? '9+' : $unreadMessageCount }}</span>@endif
                    </button>
                    <div id="notification-menu" x-cloak x-show="notificationOpen" x-transition.origin.top.right @click.outside="notificationOpen = false" class="notification-menu">
                        <form method="POST" action="{{ route('messages.read-all') }}">@csrf<button class="notification-clear" type="submit">ล้างข้อความ</button></form>
                    </div>
                </div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-200 bg-gray-800 hover:text-white hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('dashboard')">
                            แดชบอร์ด
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')">เลือกซื้อสินค้า</x-responsive-nav-link>
            @if (Auth::user()->role === 'customer')<x-responsive-nav-link :href="route('cart.index')">ตะกร้า ({{ $cartCount }})</x-responsive-nav-link>@endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')">
                    แดชบอร์ด
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<section x-data="{
    open: false,
    activeChat: null,
    openPanel() { this.open = true; this.activeChat = null; this.$nextTick(() => this.$refs.panel.focus()) },
    closePanel() { this.open = false; this.activeChat = null; this.$nextTick(() => this.$refs.trigger.focus()) }
}" @keydown.escape.window="if (open) closePanel()">
    <button x-ref="trigger" type="button" class="floating-chat" @click="open ? closePanel() : openPanel()" :aria-expanded="open" aria-controls="chat-popup" aria-label="เปิดข้อความ">
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.4 8.4 0 0 1-9 8.5 9.7 9.7 0 0 1-4.2-1L3 20l1.4-4.1A8.5 8.5 0 1 1 21 11.5Z" />
            <path d="M8 12h.01M12 12h.01M16 12h.01" />
        </svg>
        <span class="sr-only">ข้อความ</span>
        @if($unreadMessageCount)<span class="floating-chat-count">{{ $unreadMessageCount > 9 ? '9+' : $unreadMessageCount }}</span>@endif
    </button>

    <div x-cloak x-show="open" x-transition.opacity class="chat-popup-backdrop" @click="closePanel()" aria-hidden="true"></div>
    <aside id="chat-popup" x-ref="panel" x-cloak x-show="open" x-transition.origin.bottom.right tabindex="-1" role="dialog" aria-modal="false" aria-labelledby="chat-popup-title" class="chat-popup-panel">
        <header class="chat-popup-header">
            <div>
                <p class="eyebrow">Messages</p>
                <h2 id="chat-popup-title" class="mt-1 text-lg font-bold text-white" x-text="activeChat ? 'บทสนทนา' : 'ข้อความ'">ข้อความ</h2>
            </div>
            <button type="button" class="chat-popup-close" @click="closePanel()" aria-label="ปิดหน้าต่างข้อความ">×</button>
        </header>

        <div x-show="activeChat === null" class="chat-popup-directory">
            @forelse($chatConversations as $conversation)
                @php($otherUser = $conversation['user'])
                <button type="button" class="chat-popup-conversation" @click="activeChat = {{ $otherUser->id }}">
                    <span class="conversation-avatar">{{ mb_strtoupper(mb_substr($otherUser->name, 0, 1)) }}</span>
                    <span class="min-w-0 flex-1 text-left"><span class="flex items-center gap-2"><b class="truncate text-slate-100">{{ $otherUser->name }}</b>@if($conversation['unread'])<span class="unread-count">{{ $conversation['unread'] }}</span>@endif</span><span class="mt-1 block truncate text-xs text-slate-400">{{ $conversation['latest']->sender_id === auth()->id() ? 'คุณ: ' : '' }}{{ $conversation['latest']->message }}</span></span>
                    <span aria-hidden="true" class="text-red-300">›</span>
                </button>
            @empty
                <div class="chat-popup-empty"><span aria-hidden="true">✉</span><p class="mt-2 font-bold text-slate-100">ยังไม่มีข้อความ</p><p class="mt-1 text-xs text-slate-400">เมื่อมีการพูดคุยกับลูกค้าหรือร้านค้า จะแสดงที่นี่</p></div>
            @endforelse
        </div>

        @foreach($chatConversations as $conversation)
            @php($otherUser = $conversation['user'])
            <section x-cloak x-show="activeChat === {{ $otherUser->id }}" class="chat-popup-room">
                <button type="button" class="chat-popup-back" @click="activeChat = null">← รายชื่อข้อความ</button>
                <div class="mt-3 flex items-center gap-2 border-b border-gray-700 pb-3"><span class="conversation-avatar">{{ mb_strtoupper(mb_substr($otherUser->name, 0, 1)) }}</span><div><h3 class="font-bold text-slate-100">{{ $otherUser->name }}</h3><p class="text-xs text-slate-400">ห้องสนทนาส่วนตัว</p></div></div>
                <div class="chat-popup-messages">
                    @foreach($conversation['messages'] as $message)
                        <div class="chat-line {{ $message->sender_id === auth()->id() ? 'chat-own' : 'chat-other' }}"><div class="chat-bubble">@if($message->product)<p class="mb-1 text-xs font-bold text-red-300">{{ $message->product->name }}</p>@endif<p class="whitespace-pre-line">{{ $message->message }}</p><time>{{ $message->created_at->format('d/m H:i') }}</time></div></div>
                    @endforeach
                </div>
                <form method="POST" action="{{ route('messages.store') }}" class="chat-popup-form">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                    <label class="sr-only" for="popup_message_{{ $otherUser->id }}">ข้อความถึง {{ $otherUser->name }}</label>
                    <textarea id="popup_message_{{ $otherUser->id }}" required name="message" maxlength="3000" rows="2" placeholder="พิมพ์ข้อความ..." class="min-h-11 flex-1 rounded-xl"></textarea>
                    <button type="submit" class="btn-primary shrink-0">ส่ง</button>
                </form>
            </section>
        @endforeach
    </aside>
</section>
@endauth
