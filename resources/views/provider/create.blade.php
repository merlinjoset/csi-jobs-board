@extends('layouts.app')
@section('title', 'Post a job')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('provider.dashboard') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-stone-500 hover:text-brand-700">&larr; Back to dashboard</a>
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-2xl font-bold text-stone-900">Post a job</h1>
        <p class="mt-1 text-sm text-stone-600">List the skills clearly so the portal can match the right seekers.</p>

        <form method="POST" action="{{ route('provider.jobs.store') }}" class="mt-6 space-y-5">
            @csrf
            @php $input = 'w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200'; @endphp

            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-stone-700">Job title</span>
                <input name="title" value="{{ old('title') }}" required class="{{ $input }}" placeholder="e.g. Office Administrator">
            </label>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <label class="block"><span class="mb-1.5 block text-sm font-medium text-stone-700">Company</span>
                    <input name="company" value="{{ old('company') }}" required class="{{ $input }}" placeholder="e.g. Gulf Star Trading"></label>
                <label class="block"><span class="mb-1.5 block text-sm font-medium text-stone-700">Location</span>
                    <input name="location" value="{{ old('location') }}" required class="{{ $input }}" placeholder="e.g. Dubai"></label>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <label class="block"><span class="mb-1.5 block text-sm font-medium text-stone-700">Category</span>
                    <select name="category" class="{{ $input }}">@foreach ($categories as $c)<option @selected(old('category') === $c)>{{ $c }}</option>@endforeach</select></label>
                <label class="block"><span class="mb-1.5 block text-sm font-medium text-stone-700">Employment type</span>
                    <select name="employment_type" class="{{ $input }}">@foreach ($types as $t)<option @selected(old('employment_type') === $t)>{{ $t }}</option>@endforeach</select></label>
            </div>

            <label class="block"><span class="mb-1.5 block text-sm font-medium text-stone-700">Salary (optional)</span>
                <input name="salary" value="{{ old('salary') }}" class="{{ $input }}" placeholder="e.g. AED 4,000-5,000 / month"></label>

            <label class="block"><span class="mb-1.5 block text-sm font-medium text-stone-700">Description</span>
                <textarea name="description" rows="5" required class="{{ $input }}" placeholder="Describe the role and responsibilities...">{{ old('description') }}</textarea></label>

            <label class="block"><span class="mb-1.5 block text-sm font-medium text-stone-700">Skills (comma separated)</span>
                <input name="skills" value="{{ old('skills') }}" class="{{ $input }}" placeholder="e.g. excel, administration, customer service">
                <span class="mt-1 block text-xs text-stone-400">These power resume matching. Use terms seekers would list, e.g. php, laravel, sales, driving.</span></label>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('provider.dashboard') }}" class="rounded-lg border border-stone-300 px-5 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50">Cancel</a>
                <button class="rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-900">Publish job</button>
            </div>
        </form>
    </div>
</div>
@endsection
