@extends('layouts.app')
@section('title', 'Applicants')

@section('content')
<a href="{{ route('provider.dashboard') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-stone-500 hover:text-brand-700">&larr; Back to dashboard</a>

<h1 class="text-2xl font-bold text-stone-900">Applicants for {{ $job->title }}</h1>
<p class="mt-1 text-sm text-stone-600">{{ $job->company }} &middot; {{ $applications->count() }} {{ Str::plural('applicant', $applications->count()) }}</p>

@if ($applications->isEmpty())
    <div class="mt-6 rounded-xl border border-dashed border-stone-300 bg-white p-12 text-center text-sm text-stone-500">
        No one has applied to this job yet.
    </div>
@else
    <div class="mt-6 space-y-4">
        @foreach ($applications as $app)
            <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 font-semibold text-brand-700">{{ strtoupper(substr($app->seeker->name, 0, 1)) }}</span>
                            <div>
                                <p class="font-semibold text-stone-900">{{ $app->seeker->name }}</p>
                                <p class="text-xs text-stone-500">{{ $app->seeker->email }}@if($app->seeker->phone) &middot; {{ $app->seeker->phone }}@endif</p>
                            </div>
                        </div>
                    </div>
                    @php $tone = ['applied'=>'bg-stone-100 text-stone-600','shortlisted'=>'bg-emerald-100 text-emerald-700','interview'=>'bg-brand-100 text-brand-700','rejected'=>'bg-red-100 text-red-700'][$app->status] ?? 'bg-stone-100 text-stone-600'; @endphp
                    <span class="rounded-full px-2.5 py-1 text-xs font-medium capitalize {{ $tone }}">{{ $app->status }}</span>
                </div>

                @if ($app->cover_note)
                    <p class="mt-3 rounded-lg bg-stone-50 p-3 text-sm text-stone-700">{{ $app->cover_note }}</p>
                @endif

                @if ($app->resume && $app->resume->skillList())
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @foreach ($app->resume->skillList() as $skill)
                            <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium capitalize text-brand-700">{{ $skill }}</span>
                        @endforeach
                    </div>
                @endif

                @if ($app->interview_at)
                    <div class="mt-3 rounded-lg border border-brand-200 bg-brand-50/60 p-3 text-sm">
                        <p class="font-semibold text-brand-800">Interview scheduled</p>
                        <p class="text-stone-700">{{ $app->interview_at->format('D, d M Y \a\t g:i A') }} &middot; {{ $app->interview_mode }}@if($app->interview_location) &middot; {{ $app->interview_location }}@endif</p>
                        @if ($app->interview_note)<p class="mt-1 text-stone-600">{{ $app->interview_note }}</p>@endif
                    </div>
                @endif

                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-stone-100 pt-3">
                    @if ($app->resume)
                        <button type="button" data-resume-url="{{ route('resumes.show', $app->resume) }}" data-resume-name="{{ $app->resume->original_name }}" class="rounded-lg border border-brand-700 px-3 py-1.5 text-sm font-semibold text-brand-700 hover:bg-brand-50">View resume ({{ $app->resume->original_name }})</button>
                    @else
                        <span class="text-sm text-stone-400">No resume attached</span>
                    @endif
                    <span class="flex-1"></span>
                    <form method="POST" action="{{ route('provider.applications.status', $app) }}" class="flex gap-1">@csrf
                        <button name="status" value="shortlisted" class="rounded-md px-2.5 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-50">Accept / Shortlist</button>
                        <button name="status" value="rejected" class="rounded-md px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Reject</button>
                        <button name="status" value="applied" class="rounded-md px-2.5 py-1.5 text-xs font-medium text-stone-600 hover:bg-stone-100">Reset</button>
                    </form>
                </div>

                <details class="group mt-3">
                    <summary class="flex cursor-pointer items-center gap-1.5 text-sm font-medium text-brand-700 hover:text-brand-800">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 transition group-open:rotate-90" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                        {{ $app->interview_at ? 'Reschedule interview / send response' : 'Schedule interview / send response' }}
                    </summary>
                    <form method="POST" action="{{ route('provider.applications.interview', $app) }}" class="mt-3 grid grid-cols-1 gap-3 rounded-lg bg-stone-50 p-4 sm:grid-cols-2">@csrf
                        @php $inp = 'w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200'; @endphp
                        <label class="block"><span class="mb-1 block text-xs font-medium text-stone-600">Date &amp; time</span>
                            <input type="datetime-local" name="interview_at" required class="{{ $inp }}" value="{{ old('interview_at', optional($app->interview_at)->format('Y-m-d\TH:i')) }}"></label>
                        <label class="block"><span class="mb-1 block text-xs font-medium text-stone-600">Mode</span>
                            <select name="interview_mode" class="{{ $inp }}">
                                @foreach (['In-person','Online','Phone'] as $m)<option @selected($app->interview_mode === $m)>{{ $m }}</option>@endforeach
                            </select></label>
                        <label class="block sm:col-span-2"><span class="mb-1 block text-xs font-medium text-stone-600">Location or meeting link</span>
                            <input name="interview_location" class="{{ $inp }}" value="{{ old('interview_location', $app->interview_location) }}" placeholder="e.g. Office, Deira  /  https://meet..."></label>
                        <label class="block sm:col-span-2"><span class="mb-1 block text-xs font-medium text-stone-600">Note to applicant</span>
                            <textarea name="interview_note" rows="2" class="{{ $inp }}" placeholder="e.g. Please bring your certificates.">{{ old('interview_note', $app->interview_note) }}</textarea></label>
                        <div class="sm:col-span-2">
                            <button class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Send interview invite</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('provider.applications.status', $app) }}" class="mt-2 flex items-end gap-2 rounded-lg bg-stone-50 p-4">@csrf
                        <input type="hidden" name="status" value="{{ $app->status === 'applied' ? 'shortlisted' : $app->status }}">
                        <label class="block flex-1"><span class="mb-1 block text-xs font-medium text-stone-600">Quick response message</span>
                            <input name="provider_message" class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200" value="{{ $app->provider_message }}" placeholder="e.g. Thanks for applying, we would like to move forward."></label>
                        <button class="rounded-lg border border-brand-700 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">Send</button>
                    </form>
                </details>
            </div>
        @endforeach
    </div>
@endif
@endsection
