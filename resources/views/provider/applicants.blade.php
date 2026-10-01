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
                    @php $tone = ['applied'=>'bg-stone-100 text-stone-600','shortlisted'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700'][$app->status]; @endphp
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

                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-stone-100 pt-3">
                    @if ($app->resume)
                        <a href="{{ route('resumes.show', $app->resume) }}" target="_blank" class="rounded-lg border border-brand-700 px-3 py-1.5 text-sm font-semibold text-brand-700 hover:bg-brand-50">View resume ({{ $app->resume->original_name }})</a>
                    @else
                        <span class="text-sm text-stone-400">No resume attached</span>
                    @endif
                    <span class="flex-1"></span>
                    <form method="POST" action="{{ route('provider.applications.status', $app) }}" class="flex gap-1">@csrf
                        <button name="status" value="shortlisted" class="rounded-md px-2.5 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-50">Shortlist</button>
                        <button name="status" value="rejected" class="rounded-md px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Reject</button>
                        <button name="status" value="applied" class="rounded-md px-2.5 py-1.5 text-xs font-medium text-stone-600 hover:bg-stone-100">Reset</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
