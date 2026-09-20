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
        <div class="palette-page min-h-screen">
            @include('layouts.navigation')

            @guest
                <nav class="palette-nav border-b text-white shadow-lg shadow-indigo-950/20 backdrop-blur">
                    <div class="page-shell flex min-h-16 items-center justify-between gap-4">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-white"><span class="brand-mark grid h-9 w-9 place-items-center rounded-xl">PC</span><span>SecondPC</span></a>
                        <div class="flex items-center gap-2"><a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-gray-600 px-4 py-2 text-sm font-semibold text-white hover:border-red-400 hover:bg-gray-800">เข้าสู่ระบบ</a><a href="{{ route('register') }}" class="btn-primary">สมัครสมาชิก</a></div>
                    </div>
                </nav>
            @endguest

            <!-- Page Heading -->
            @isset($header)
                <header class="border-b border-gray-700 bg-[#1F2937]/95 backdrop-blur">
                    <div class="page-shell py-5">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @if (session('success'))
                    <div class="page-shell pt-5"><div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div></div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
