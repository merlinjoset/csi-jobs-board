@extends('layouts.app')
@section('title', 'Browse jobs')

@section('content')
<section class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-brand-700 to-brand-900 p-8 text-white shadow-sm sm:p-10">
    <h1 class="max-w-xl text-2xl font-bold sm:text-3xl">Find your next job in our community</h1>
    <p class="mt-2 max-w-2xl text-brand-100">Browse openings posted by job providers. Seekers can upload a resume and get matched to the most relevant roles automatically.</p>
    @guest
        <a href="{{ route('register') }}" class="mt-5 inline-block rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-brand-800 hover:bg-brand-50">Register to apply</a>
    @endguest
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
            <a href="{{ route('jobs.show', $job) }}" class="group flex flex-col rounded-xl border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md">
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
