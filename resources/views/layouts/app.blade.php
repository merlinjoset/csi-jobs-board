<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CSI Job Portal')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                brand: { 50:'#fbf4f1',100:'#f6e4dc',200:'#ecc5b6',300:'#dc9a83',400:'#c56b4d',500:'#a8482a',600:'#8a3419',700:'#6e2410',800:'#581601',900:'#3f1002' },
                accent: { 500:'#f03c02', 600:'#d13102' },
            } } }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Open Sans', system-ui, sans-serif; } </style>
</head>
<body class="min-h-screen bg-stone-50 text-stone-800">
    <header class="sticky top-0 z-20 border-b border-stone-200 bg-white/90 backdrop-blur">
        <div class="h-1 w-full bg-gradient-to-r from-brand-800 via-accent-500 to-brand-800"></div>
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-800 text-white font-bold">C</span>
                <span class="leading-tight">
                    <span class="block text-base font-bold text-stone-900">CSI Job Portal</span>
                    <span class="block text-xs text-stone-500">Jobs for our community</span>
                </span>
            </a>
            <nav class="flex items-center gap-1 text-sm sm:gap-2">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900">Browse jobs</a>
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900">Dashboard</a>
                        <a href="{{ route('admin.users') }}" class="rounded-lg px-3 py-2 font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900">Users</a>
                        <a href="{{ route('admin.jobs') }}" class="rounded-lg px-3 py-2 font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900">Jobs</a>
                        <a href="{{ route('admin.applications') }}" class="rounded-lg px-3 py-2 font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900">Applications</a>
                    @elseif (auth()->user()->isProvider())
                        <a href="{{ route('provider.dashboard') }}" class="rounded-lg px-3 py-2 font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900">Dashboard</a>
                        <a href="{{ route('provider.jobs.create') }}" class="rounded-lg bg-brand-800 px-4 py-2 font-semibold text-white hover:bg-brand-900">+ Post a job</a>
                    @else
                        <a href="{{ route('seeker.dashboard') }}" class="rounded-lg px-3 py-2 font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900">Dashboard</a>
                        <a href="{{ route('seeker.applications') }}" class="rounded-lg px-3 py-2 font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900">My applications</a>
                    @endif
                    <span class="ml-1 hidden text-right leading-tight sm:block">
                        <span class="block text-sm font-medium text-stone-800">{{ auth()->user()->name }}</span>
                        <span class="block text-xs capitalize text-stone-400">{{ auth()->user()->role }}</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="rounded-lg px-3 py-2 font-medium text-stone-500 hover:text-stone-900">Sign out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900">Sign in</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-brand-800 px-4 py-2 font-semibold text-white hover:bg-brand-900">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-12 border-t border-stone-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-6 text-center text-sm text-stone-500 sm:px-6">
            CSI Job Portal &middot; Built with Laravel and MariaDB
        </div>
    </footer>
</body>
</html>
