<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SecondPC') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans">
        <div class="relative min-h-screen overflow-hidden bg-[#111827] px-4 py-8 sm:grid sm:place-items-center">
            <div class="grid w-full max-w-5xl overflow-hidden rounded-3xl border border-gray-700 bg-[#1F2937] shadow-2xl shadow-black/30 lg:grid-cols-[1fr_0.9fr]">
                <section class="relative hidden min-h-[620px] overflow-hidden bg-[#1A2A80] p-10 text-white lg:block">
                    <div class="absolute -right-20 -top-16 h-64 w-64 rounded-full bg-[#B2B0E8]/30 blur-3xl"></div><div class="absolute -bottom-20 left-8 h-52 w-52 rounded-full bg-[#7A85C1]/40 blur-3xl"></div>
                    <a href="{{ route('home') }}" class="relative flex items-center gap-3 text-xl font-bold"><span class="grid h-11 w-11 place-items-center rounded-xl bg-[#B2B0E8] text-sm text-[#1A2A80]">PC</span>SecondPC</a>
                    <div class="relative mt-28"><p class="text-sm font-semibold tracking-widest text-[#B2B0E8]">SECOND-HAND COMPUTER MARKETPLACE</p><h1 class="mt-5 text-4xl font-bold leading-tight">ซื้อคอมมือสอง<br>อย่างมั่นใจ</h1><p class="mt-5 max-w-sm text-base leading-7 text-[#d9d9f6]">เลือกสินค้าที่ตรวจสอบได้ คุยกับร้านค้าที่ผ่าน KYC และชำระเงินอย่างปลอดภัย</p></div>
                    <p class="absolute bottom-10 text-sm text-[#B2B0E8]">สินค้าชัดเจน · ซื้อขายสบายใจ</p>
                </section>
                <div class="flex min-h-[620px] items-center px-6 py-10 sm:px-12">
                    <div class="w-full">
                        <a href="{{ route('home') }}" class="mb-9 flex items-center gap-2 text-xl font-bold text-[#1A2A80] lg:hidden"><span class="grid h-10 w-10 place-items-center rounded-xl bg-[#1A2A80] text-sm text-white">PC</span>SecondPC</a>
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
