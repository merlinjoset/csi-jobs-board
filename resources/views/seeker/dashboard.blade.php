@extends('layouts.app')
@section('title', 'Seeker dashboard')

@section('content')
<h1 class="text-2xl font-bold text-stone-900">Welcome, {{ $user->name }}</h1>
<p class="mt-1 text-sm text-stone-600">Upload your resume and the portal will suggest the jobs that fit you best.</p>

<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-[340px_1fr]">
    <aside class="space-y-4">
        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="font-semibold text-stone-900">Your resume</h2>
            @if ($resume)
                <div class="mt-3 rounded-lg bg-stone-50 p-3 text-sm">
                    <p class="font-medium text-stone-800">{{ $resume->original_name }}</p>
                    <p class="text-xs text-stone-500">Uploaded {{ $resume->created_at->diffForHumans() }}</p>
                    <a href="{{ route('resumes.show', $resume) }}" target="_blank" class="mt-1 inline-block text-xs font-medium text-brand-700 hover:underline">View file</a>
                </div>
                @if ($resume->skillList())
                    <div class="mt-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-stone-500">Detected skills</p>
                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                            @foreach ($resume->skillList() as $skill)
                                <span class="rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-medium capitalize text-brand-700">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="mt-3 text-xs text-stone-500">No known skills detected yet. Try a more detailed resume.</p>
                @endif
            @else
                <p class="mt-2 text-sm text-stone-600">No resume uploaded yet.</p>
            @endif

            <form method="POST" action="{{ route('seeker.resume.upload') }}" enctype="multipart/form-data" class="mt-4 space-y-2">
                @csrf
                <input type="file" name="resume" accept=".pdf,.docx,.txt" required class="block w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-800 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-900">
                <p class="text-xs text-stone-400">PDF, DOCX, or TXT. Max 5 MB.</p>
                <button class="w-full rounded-lg border border-brand-700 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">{{ $resume ? 'Upload new resume' : 'Upload resume' }}</button>
            </form>
        </div>
    </aside>

    <div>
        <h2 class="mb-3 font-semibold text-stone-900">Suggested jobs for you</h2>
        @if (! $resume)
            <div class="rounded-xl border border-dashed border-stone-300 bg-white p-10 text-center text-sm text-stone-500">
                Upload a resume to see jobs matched to your skills.
            </div>
        @elseif (empty($suggestions))
            <div class="rounded-xl border border-dashed border-stone-300 bg-white p-10 text-center text-sm text-stone-500">
                We could not match your resume to current openings. <a href="{{ route('home') }}" class="font-medium text-brand-700 underline">Browse all jobs</a> instead.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($suggestions as $s)
                    <a href="{{ route('jobs.show', $s['job']) }}" class="block rounded-xl border border-stone-200 bg-white p-5 shadow-sm transition hover:border-brand-300 hover:shadow-md">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-stone-900">{{ $s['job']->title }}</h3>
                                <p class="text-sm text-stone-500">{{ $s['job']->company }} &middot; {{ $s['job']->location }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">Match score {{ $s['score'] }}</span>
                        </div>
                        @if (! empty($s['matched']))
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @foreach ($s['matched'] as $m)
                                    <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium capitalize text-brand-700">{{ $m }}</span>
                                @endforeach
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif

        @if ($applications->isNotEmpty())
            <h2 class="mb-3 mt-8 font-semibold text-stone-900">Recent applications</h2>
            <div class="divide-y divide-stone-100 rounded-xl border border-stone-200 bg-white">
                @foreach ($applications as $app)
                    <div class="flex items-center justify-between px-4 py-3 text-sm">
                        <a href="{{ route('jobs.show', $app->jobPost) }}" class="font-medium text-stone-800 hover:text-brand-700">{{ $app->jobPost->title }}</a>
                        <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-medium capitalize text-stone-600">{{ $app->status }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
