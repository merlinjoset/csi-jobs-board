@extends('layouts.app')
@section('title', 'My applications')

@section('content')
<h1 class="text-2xl font-bold text-stone-900">My applications</h1>
<p class="mt-1 text-sm text-stone-600">Track your applications and responses from providers here.</p>

@if ($applications->isEmpty())
    <div class="mt-6 rounded-xl border border-dashed border-stone-300 bg-white p-12 text-center">
        <p class="font-medium text-stone-700">You have not applied to any jobs yet</p>
        <a href="{{ route('home') }}" class="mt-3 inline-block rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Browse jobs</a>
    </div>
@else
    @php
        $labels = ['applied'=>'Applied','shortlisted'=>'Shortlisted','interview'=>'Interview scheduled','rejected'=>'Not selected'];
        $tones = ['applied'=>'bg-stone-100 text-stone-600','shortlisted'=>'bg-emerald-100 text-emerald-700','interview'=>'bg-brand-100 text-brand-700','rejected'=>'bg-red-100 text-red-700'];
    @endphp
    <div class="mt-6 space-y-4">
        @foreach ($applications as $app)
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <a href="{{ route('jobs.show', $app->jobPost) }}" class="font-semibold text-stone-900 hover:text-brand-700">{{ $app->jobPost->title }}</a>
                        <p class="text-sm text-stone-500">{{ $app->jobPost->company }} &middot; applied {{ $app->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $tones[$app->status] ?? 'bg-stone-100 text-stone-600' }}">{{ $labels[$app->status] ?? ucfirst($app->status) }}</span>
                </div>

                {{-- Interview invite --}}
                @if ($app->interview_at)
                    <div class="mt-4 flex items-start gap-3 rounded-xl border border-brand-200 bg-brand-50/70 p-4">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        </span>
                        <div class="text-sm">
                            <p class="font-semibold text-brand-800">You are invited to an interview</p>
                            <p class="text-stone-700">{{ $app->interview_at->format('l, d M Y \a\t g:i A') }}</p>
                            <p class="text-stone-600">{{ $app->interview_mode }}@if($app->interview_location) &middot; {{ $app->interview_location }}@endif</p>
                            @if ($app->interview_note)<p class="mt-1 text-stone-600">&ldquo;{{ $app->interview_note }}&rdquo;</p>@endif
                        </div>
                    </div>
                @elseif ($app->status === 'shortlisted')
                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50/70 p-4 text-sm text-emerald-800">
                        <p class="font-semibold">Good news! The provider shortlisted your application.</p>
                        <p class="text-emerald-700">They may reach out with interview details soon.</p>
                    </div>
                @elseif ($app->status === 'rejected')
                    <div class="mt-4 rounded-xl border border-stone-200 bg-stone-50 p-4 text-sm text-stone-600">
                        The provider has decided not to move forward this time. Keep applying, more roles are added regularly.
                    </div>
                @endif

                {{-- Provider's written response --}}
                @if ($app->provider_message)
                    <div class="mt-3 rounded-xl bg-stone-50 p-4 text-sm">
                        <p class="text-xs font-medium uppercase tracking-wide text-stone-400">Message from {{ $app->jobPost->company }}</p>
                        <p class="mt-1 text-stone-700">{{ $app->provider_message }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif
@endsection
