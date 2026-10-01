@extends('layouts.app')
@section('title', 'Provider dashboard')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-stone-900">Your job posts</h1>
        <p class="mt-1 text-sm text-stone-600">Manage your openings and review applicants with their resumes.</p>
    </div>
    <a href="{{ route('provider.jobs.create') }}" class="rounded-lg bg-brand-800 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-900">+ Post a job</a>
</div>

@if ($jobs->isEmpty())
    <div class="mt-6 rounded-xl border border-dashed border-stone-300 bg-white p-12 text-center">
        <p class="font-medium text-stone-700">You have not posted any jobs yet</p>
        <a href="{{ route('provider.jobs.create') }}" class="mt-3 inline-block rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-900">Post your first job</a>
    </div>
@else
    <div class="mt-6 space-y-3">
        @foreach ($jobs as $job)
            <div class="flex flex-col gap-3 rounded-xl border border-stone-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('jobs.show', $job) }}" class="font-semibold text-stone-900 hover:text-brand-700">{{ $job->title }}</a>
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize {{ $job->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-200 text-stone-600' }}">{{ $job->status }}</span>
                    </div>
                    <p class="text-sm text-stone-500">{{ $job->company }} &middot; {{ $job->location }} &middot; {{ $job->category }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <a href="{{ route('provider.jobs.applicants', $job) }}" class="rounded-lg bg-brand-50 px-3 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-100">
                        {{ $job->applications_count }} {{ Str::plural('applicant', $job->applications_count) }}
                    </a>
                    <form method="POST" action="{{ route('provider.jobs.toggle', $job) }}">@csrf
                        <button class="rounded-lg border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">{{ $job->status === 'open' ? 'Close' : 'Reopen' }}</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
