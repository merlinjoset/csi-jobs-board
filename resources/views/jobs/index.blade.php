@extends('layouts.app')
@section('title', 'Browse jobs')

@section('content')
<section class="relative mb-8 overflow-hidden rounded-3xl border border-stone-200 bg-gradient-to-br from-emerald-50 via-white to-white p-8 shadow-sm sm:p-12">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-accent-500/20 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-10 h-72 w-72 rounded-full bg-emerald-300/30 blur-3xl"></div>
        <div class="absolute inset-0 opacity-60" style="background-image:radial-gradient(circle at 1px 1px,#d4d4d8 1px,transparent 0);background-size:22px 22px;-webkit-mask-image:linear-gradient(to bottom,black,transparent);mask-image:linear-gradient(to bottom,black,transparent);"></div>
    </div>
    <div class="relative">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200">✦ Resume-matched job search</span>
        <h1 class="mt-4 max-w-2xl text-3xl font-extrabold tracking-tight text-stone-900 sm:text-4xl">Find your next job, matched to your resume</h1>
        <p class="mt-3 max-w-2xl text-stone-600">Browse openings from providers, upload your resume, and get the most relevant roles suggested to you automatically.</p>
        <div class="mt-6 flex flex-wrap gap-3">
            @guest
                <a href="{{ route('register') }}" class="rounded-xl bg-accent-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-accent-500">Register to apply</a>
                <a href="{{ route('login') }}" class="rounded-xl border border-stone-300 bg-white px-5 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-50">Sign in</a>
            @else
                <a href="{{ route('dashboard') }}" class="rounded-xl bg-accent-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-accent-500">Go to dashboard</a>
            @endguest
        </div>
    </div>
</section>

<form method="GET" action="{{ route('home') }}" class="mb-6 grid grid-cols-1 gap-3 rounded-xl border border-stone-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_200px_180px_auto]">
    <input name="q" value="{{ $q }}" placeholder="Search title, company, or skill..." class="rounded-lg border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
    <select name="category" class="rounded-lg border border-stone-300 px-3 py-2.5 text-sm">
        <option value="">All categories</option>
        @foreach ($categories as $c)<option value="{{ $c }}" @selected($category === $c)>{{ $c }}</option>@endforeach
    </select>
    <select name="type" class="rounded-lg border border-stone-300 px-3 py-2.5 text-sm">
        <option value="">All types</option>
        @foreach ($types as $t)<option value="{{ $t }}" @selected($type === $t)>{{ $t }}</option>@endforeach
    </select>
    <button class="rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-900">Search</button>
</form>

<p class="mb-4 text-sm text-stone-500">{{ $jobs->total() }} {{ Str::plural('opening', $jobs->total()) }} found</p>

@if ($jobs->isEmpty())
    <div class="rounded-xl border border-dashed border-stone-300 bg-white p-12 text-center">
        <p class="font-medium text-stone-700">No jobs match your search</p>
        <p class="mt-1 text-sm text-stone-500">Try different keywords or clear the filters.</p>
    </div>
@else
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($jobs as $job)
            <a href="{{ route('jobs.show', $job) }}" class="group relative flex flex-col overflow-hidden rounded-2xl border border-stone-200/70 bg-white/80 p-5 shadow-sm ring-1 ring-transparent backdrop-blur transition hover:-translate-y-1 hover:shadow-xl hover:ring-brand-200">
                <span aria-hidden="true" class="absolute inset-x-0 top-0 h-1 origin-left scale-x-0 bg-gradient-to-r from-brand-500 to-accent-500 transition-transform duration-300 group-hover:scale-x-100"></span>
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-semibold text-stone-900 group-hover:text-brand-700">{{ $job->title }}</h3>
                        <p class="text-sm text-stone-500">{{ $job->company }}</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-brand-100 px-2.5 py-1 text-xs font-medium text-brand-700">{{ $job->employment_type }}</span>
                </div>
                <p class="mt-3 line-clamp-2 text-sm text-stone-600">{{ $job->description }}</p>
                <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-stone-500">
                    <span>{{ $job->location }}</span>
                    @if ($job->salary)<span>{{ $job->salary }}</span>@endif
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-3 text-xs">
                    <span class="font-medium text-brand-600">{{ $job->category }}</span>
                    <span class="text-stone-400">{{ $job->created_at->diffForHumans() }}</span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-6">{{ $jobs->links() }}</div>
@endif
@endsection
