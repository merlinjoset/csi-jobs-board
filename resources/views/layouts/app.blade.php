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
            theme: { extend: {
                colors: {
                    brand: { 50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b' },
                    accent: { 500:'#f59e0b', 600:'#d97706' },
                },
                fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
            } }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', system-ui, sans-serif; } </style>
</head>
<body class="relative min-h-screen text-stone-800">
    {{-- Fancy layered background: soft gradient wash + blurred color orbs --}}
    <div aria-hidden="true" class="fixed inset-0 -z-10 bg-gradient-to-b from-emerald-50 via-stone-50 to-stone-50">
        <div class="absolute -top-28 -left-24 h-96 w-96 rounded-full bg-brand-400/25 blur-3xl"></div>
        <div class="absolute top-24 right-[-6rem] h-80 w-80 rounded-full bg-accent-500/15 blur-3xl"></div>
        <div class="absolute bottom-10 left-1/3 h-72 w-72 rounded-full bg-brand-300/20 blur-3xl"></div>
    </div>

    @php
        $u = auth()->user();
        $nav = [['label' => 'Browse jobs', 'url' => route('home'), 'active' => request()->routeIs('home') || request()->routeIs('jobs.show')]];
        if ($u) {
            if ($u->isAdmin()) {
                $nav[] = ['label' => 'Dashboard', 'url' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')];
                $nav[] = ['label' => 'Users', 'url' => route('admin.users'), 'active' => request()->routeIs('admin.users')];
                $nav[] = ['label' => 'Jobs', 'url' => route('admin.jobs'), 'active' => request()->routeIs('admin.jobs')];
                $nav[] = ['label' => 'Applications', 'url' => route('admin.applications'), 'active' => request()->routeIs('admin.applications')];
                $nav[] = ['label' => 'Email', 'url' => route('admin.email'), 'active' => request()->routeIs('admin.email')];
            } elseif ($u->isProvider()) {
                $nav[] = ['label' => 'Dashboard', 'url' => route('provider.dashboard'), 'active' => request()->routeIs('provider.dashboard') || request()->routeIs('provider.jobs.applicants')];
            } else {
                $nav[] = ['label' => 'Dashboard', 'url' => route('seeker.dashboard'), 'active' => request()->routeIs('seeker.dashboard')];
                $nav[] = ['label' => 'My applications', 'url' => route('seeker.applications'), 'active' => request()->routeIs('seeker.applications')];
            }
        }
    @endphp

    <header class="sticky top-0 z-30 border-b border-stone-200/60 bg-white/70 shadow-[0_1px_0_0_rgba(0,0,0,0.02),0_8px_24px_-16px_rgba(0,0,0,0.25)] backdrop-blur-xl">
        <div class="h-1 w-full bg-gradient-to-r from-brand-700 via-accent-500 to-brand-700"></div>
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-2.5 sm:px-6">
            {{-- Brand --}}
            <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-600 to-brand-800 text-white shadow-md ring-1 ring-black/5 transition group-hover:shadow-lg">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 12h18"/>
                    </svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-base font-extrabold tracking-tight text-stone-900">CSI Job Portal</span>
                    <span class="block text-[11px] font-medium text-stone-500">Jobs for our community</span>
                </span>
            </a>

            {{-- Desktop nav with active pills --}}
            <nav class="hidden items-center gap-1 rounded-full border border-stone-200/70 bg-white/60 p-1 md:flex">
                @foreach ($nav as $item)
                    <a href="{{ $item['url'] }}" class="rounded-full px-3.5 py-1.5 text-sm font-medium transition {{ $item['active'] ? 'bg-brand-600 text-white shadow-sm' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900' }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>

            {{-- Right actions --}}
            <div class="flex items-center gap-2">
                @auth
                    @if ($u->isProvider())
                        <a href="{{ route('provider.jobs.create') }}" class="hidden items-center gap-1.5 rounded-full bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 sm:inline-flex">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                            Post a job
                        </a>
                    @endif

                    {{-- User dropdown --}}
                    <div class="relative" data-menu>
                        <button data-menu-btn class="flex items-center gap-2 rounded-full border border-stone-200/80 bg-white/70 py-1 pl-1 pr-2 shadow-sm transition hover:bg-stone-50">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-bold text-white">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                            <span class="hidden text-left leading-tight sm:block">
                                <span class="block text-sm font-semibold text-stone-800">{{ \Illuminate\Support\Str::of($u->name)->words(2, '') }}</span>
                                <span class="block text-[11px] capitalize text-stone-400">{{ $u->role }}</span>
                            </span>
                            <svg viewBox="0 0 24 24" class="h-4 w-4 text-stone-400" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div data-menu-panel class="absolute right-0 mt-2 hidden w-60 overflow-hidden rounded-2xl border border-stone-200 bg-white/95 shadow-xl ring-1 ring-black/5 backdrop-blur">
                            <div class="border-b border-stone-100 px-4 py-3">
                                <p class="text-sm font-semibold text-stone-900">{{ $u->name }}</p>
                                <p class="truncate text-xs text-stone-500">{{ $u->email }}</p>
                                <span class="mt-1.5 inline-block rounded-full bg-brand-50 px-2 py-0.5 text-[11px] font-medium capitalize text-brand-700 ring-1 ring-brand-100">{{ $u->role }}</span>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-stone-700 hover:bg-stone-50">Dashboard</a>
                                @if ($u->isSeeker())
                                    <a href="{{ route('seeker.applications') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-stone-700 hover:bg-stone-50">My applications</a>
                                @elseif ($u->isProvider())
                                    <a href="{{ route('provider.jobs.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-stone-700 hover:bg-stone-50">Post a job</a>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-stone-100">@csrf
                                <button class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm font-medium text-red-600 hover:bg-red-50">Sign out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-full px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900 sm:inline-block">Sign in</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">Register</a>
                @endauth

                {{-- Mobile toggle --}}
                <button data-mobile-btn class="rounded-lg p-2 text-stone-600 hover:bg-stone-100 md:hidden" aria-label="Toggle menu">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile panel --}}
        <div data-mobile-panel class="hidden border-t border-stone-200 bg-white/95 px-4 py-3 backdrop-blur md:hidden">
            <nav class="flex flex-col gap-1">
                @foreach ($nav as $item)
                    <a href="{{ $item['url'] }}" class="rounded-lg px-3 py-2.5 text-sm font-medium {{ $item['active'] ? 'bg-brand-50 text-brand-700' : 'text-stone-700 hover:bg-stone-50' }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>
            @auth
                @if ($u->isProvider())
                    <a href="{{ route('provider.jobs.create') }}" class="mt-2 block rounded-lg bg-brand-600 px-3 py-2.5 text-center text-sm font-semibold text-white">Post a job</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-stone-100 pt-2">@csrf
                    <button class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium text-red-600 hover:bg-red-50">Sign out</button>
                </form>
            @else
                <div class="mt-2 flex gap-2 border-t border-stone-100 pt-2">
                    <a href="{{ route('login') }}" class="flex-1 rounded-lg border border-stone-300 px-3 py-2.5 text-center text-sm font-semibold text-stone-700">Sign in</a>
                    <a href="{{ route('register') }}" class="flex-1 rounded-lg bg-brand-600 px-3 py-2.5 text-center text-sm font-semibold text-white">Register</a>
                </div>
            @endauth
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

    <footer class="mt-12 border-t border-stone-200 bg-white/70 backdrop-blur">
        <div class="mx-auto max-w-6xl px-4 py-6 text-center text-sm text-stone-500 sm:px-6">
            CSI Job Portal &middot; Jobs for our community
        </div>
    </footer>

    <script>
        // Premium menu: user dropdown + mobile panel, with click-outside to close.
        (function () {
            const menu = document.querySelector('[data-menu]');
            const menuBtn = menu?.querySelector('[data-menu-btn]');
            const menuPanel = menu?.querySelector('[data-menu-panel]');
            const mobileBtn = document.querySelector('[data-mobile-btn]');
            const mobilePanel = document.querySelector('[data-mobile-panel]');

            menuBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                menuPanel.classList.toggle('hidden');
            });
            mobileBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                mobilePanel.classList.toggle('hidden');
            });
            document.addEventListener('click', (e) => {
                if (menuPanel && !menu.contains(e.target)) menuPanel.classList.add('hidden');
                if (mobilePanel && !mobilePanel.contains(e.target) && e.target !== mobileBtn && !mobileBtn?.contains(e.target)) {
                    mobilePanel.classList.add('hidden');
                }
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') { menuPanel?.classList.add('hidden'); mobilePanel?.classList.add('hidden'); }
            });
        })();
    </script>

    {{-- In-app resume preview modal --}}
    <div id="resumeModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-stone-900/50 backdrop-blur-sm" data-resume-close></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="flex h-[85vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5">
                <div class="flex items-center justify-between gap-3 border-b border-stone-200 px-4 py-3">
                    <div class="flex min-w-0 items-center gap-2">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-700">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 3v4a1 1 0 001 1h4"/><path d="M17 21H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/></svg>
                        </span>
                        <span id="resumeTitle" class="truncate text-sm font-semibold text-stone-800">Resume</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <a id="resumeOpen" href="#" target="_blank" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-brand-700 hover:bg-brand-50">Open in new tab</a>
                        <button type="button" data-resume-close class="rounded-lg p-1.5 text-stone-500 hover:bg-stone-100" aria-label="Close">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                        </button>
                    </div>
                </div>
                <iframe id="resumeFrame" class="h-full w-full flex-1 bg-stone-50" title="Resume preview"></iframe>
            </div>
        </div>
    </div>
    <script>
        (function () {
            const modal = document.getElementById('resumeModal');
            const frame = document.getElementById('resumeFrame');
            const title = document.getElementById('resumeTitle');
            const openLink = document.getElementById('resumeOpen');
            function openModal(url, name) {
                frame.src = url;
                title.textContent = name || 'Resume';
                openLink.href = url;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            function closeModal() {
                modal.classList.add('hidden');
                frame.src = 'about:blank';
                document.body.style.overflow = '';
            }
            document.addEventListener('click', (e) => {
                const trigger = e.target.closest('[data-resume-url]');
                if (trigger) { e.preventDefault(); openModal(trigger.getAttribute('data-resume-url'), trigger.getAttribute('data-resume-name')); return; }
                if (e.target.closest('[data-resume-close]')) closeModal();
            });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
        })();
    </script>
</body>
</html>
