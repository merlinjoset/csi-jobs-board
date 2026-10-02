@extends('layouts.app')
@section('title', 'Admin dashboard')

@section('content')
<h1 class="text-2xl font-bold text-stone-900">Admin dashboard</h1>
<p class="mt-1 text-sm text-stone-600">Overview of the portal. Manage users, jobs, and applications from the backend.</p>

<div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
    @php
        $cards = [
            ['Users', $stats['users'], 'text-stone-900', route('admin.users')],
            ['Seekers', $stats['seekers'], 'text-brand-700', route('admin.users', ['role' => 'seeker'])],
            ['Providers', $stats['providers'], 'text-brand-700', route('admin.users', ['role' => 'provider'])],
            ['Resumes', $stats['resumes'], 'text-stone-900', null],
            ['Jobs', $stats['jobs'], 'text-stone-900', route('admin.jobs')],
            ['Open jobs', $stats['open_jobs'], 'text-emerald-600', route('admin.jobs', ['status' => 'open'])],
            ['Applications', $stats['applications'], 'text-accent-600', route('admin.applications')],
        ];
    @endphp
    @foreach ($cards as [$label, $value, $tone, $link])
        <a @if($link) href="{{ $link }}" @endif class="rounded-xl border border-stone-200 bg-white p-4 shadow-sm {{ $link ? 'transition hover:border-brand-300 hover:shadow-md' : '' }}">
            <p class="text-xs font-medium uppercase tracking-wide text-stone-500">{{ $label }}</p>
            <p class="mt-1 text-3xl font-bold {{ $tone }}">{{ $value }}</p>
        </a>
    @endforeach
</div>

<div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-semibold text-stone-900">Recent jobs</h2>
            <a href="{{ route('admin.jobs') }}" class="text-sm font-medium text-brand-700 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-stone-100 rounded-xl border border-stone-200 bg-white">
            @forelse ($recentJobs as $job)
                <div class="flex items-center justify-between px-4 py-3 text-sm">
                    <div class="min-w-0">
                        <a href="{{ route('jobs.show', $job) }}" class="font-medium text-stone-800 hover:text-brand-700">{{ $job->title }}</a>
                        <p class="text-xs text-stone-500">{{ $job->company }} &middot; by {{ $job->provider->name }}</p>
                    </div>
                    <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium capitalize {{ $job->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-200 text-stone-600' }}">{{ $job->status }}</span>
                </div>
            @empty
                <p class="px-4 py-6 text-center text-sm text-stone-400">No jobs yet.</p>
            @endforelse
        </div>
    </section>

    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-semibold text-stone-900">Recent applications</h2>
            <a href="{{ route('admin.applications') }}" class="text-sm font-medium text-brand-700 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-stone-100 rounded-xl border border-stone-200 bg-white">
            @forelse ($recentApplications as $app)
                <div class="flex items-center justify-between px-4 py-3 text-sm">
                    <div class="min-w-0">
                        <p class="font-medium text-stone-800">{{ $app->seeker->name }}</p>
                        <p class="text-xs text-stone-500">applied to {{ $app->jobPost->title }}</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-stone-100 px-2 py-0.5 text-xs font-medium capitalize text-stone-600">{{ $app->status }}</span>
                </div>
            @empty
                <p class="px-4 py-6 text-center text-sm text-stone-400">No applications yet.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
