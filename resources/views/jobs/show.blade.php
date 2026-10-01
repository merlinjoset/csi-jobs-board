@extends('layouts.app')
@section('title', $job->title)

@section('content')
<a href="{{ route('home') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-stone-500 hover:text-brand-700">&larr; Back to all jobs</a>

<div class="mx-auto max-w-3xl overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
    <div class="border-b border-stone-100 bg-gradient-to-br from-brand-50 to-stone-50 p-6 sm:p-8">
        <div class="flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-white px-2.5 py-1 text-xs font-medium text-brand-700 shadow-sm">{{ $job->category }}</span>
            <span class="rounded-full bg-white px-2.5 py-1 text-xs font-medium text-stone-600 shadow-sm">{{ $job->employment_type }}</span>
            @if ($job->status !== 'open')<span class="rounded-full bg-stone-200 px-2.5 py-1 text-xs font-medium text-stone-600">Closed</span>@endif
        </div>
        <h1 class="mt-3 text-2xl font-bold text-stone-900 sm:text-3xl">{{ $job->title }}</h1>
        <p class="mt-1 text-stone-600">{{ $job->company }}</p>
        <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-lg bg-white/70 p-3"><p class="text-xs uppercase tracking-wide text-stone-400">Location</p><p class="mt-0.5 text-sm font-medium">{{ $job->location }}</p></div>
            <div class="rounded-lg bg-white/70 p-3"><p class="text-xs uppercase tracking-wide text-stone-400">Compensation</p><p class="mt-0.5 text-sm font-medium">{{ $job->salary ?: 'Not specified' }}</p></div>
            <div class="rounded-lg bg-white/70 p-3"><p class="text-xs uppercase tracking-wide text-stone-400">Posted by</p><p class="mt-0.5 text-sm font-medium">{{ $job->provider->name }}</p></div>
        </div>
    </div>

    <div class="p-6 sm:p-8">
        <section>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-500">About this role</h2>
            <p class="mt-2 whitespace-pre-line leading-relaxed text-stone-700">{{ $job->description }}</p>
        </section>

        @if ($job->skillList())
            <section class="mt-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-500">Skills</h2>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($job->skillList() as $skill)
                        <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-medium capitalize text-stone-700">{{ $skill }}</span>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-8 rounded-xl bg-stone-50 p-5">
            @auth
                @if (auth()->user()->isSeeker())
                    @if ($alreadyApplied)
                        <p class="text-center font-medium text-emerald-700">You have applied to this job. Track it under <a href="{{ route('seeker.applications') }}" class="underline">My applications</a>.</p>
                    @elseif ($job->status !== 'open')
                        <p class="text-center text-stone-600">This job is closed and no longer accepting applications.</p>
                    @else
                        <form method="POST" action="{{ route('jobs.apply', $job) }}" class="space-y-3">
                            @csrf
                            <p class="font-semibold text-stone-900">Apply with your latest resume</p>
                            <textarea name="cover_note" rows="3" placeholder="Optional note to the provider..." class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200"></textarea>
                            <button class="rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-900">Submit application</button>
                            <p class="text-xs text-stone-500">No resume yet? Upload one from your <a href="{{ route('seeker.dashboard') }}" class="underline">dashboard</a> first.</p>
                        </form>
                    @endif
                @else
                    <p class="text-center text-stone-600">You are signed in as a provider. Switch to a seeker account to apply.</p>
                @endif
            @else
                <div class="text-center">
                    <p class="font-medium text-stone-900">Want to apply?</p>
                    <p class="mt-1 text-sm text-stone-600">Create a seeker account and upload your resume.</p>
                    <a href="{{ route('register') }}" class="mt-3 inline-block rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-900">Register to apply</a>
                </div>
            @endauth
        </section>
    </div>
</div>
@endsection
