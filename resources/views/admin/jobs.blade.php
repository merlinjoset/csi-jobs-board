@extends('layouts.app')
@section('title', 'Manage jobs')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-stone-900">Jobs</h1>
        <p class="mt-1 text-sm text-stone-600">Moderate every posting on the portal.</p>
    </div>
    <div class="flex gap-1 rounded-lg border border-stone-200 bg-stone-100 p-1 text-sm">
        @foreach (['' => 'All', 'open' => 'Open', 'closed' => 'Closed'] as $key => $label)
            <a href="{{ route('admin.jobs', $key ? ['status' => $key] : []) }}" class="rounded-md px-3 py-1.5 font-medium {{ $status === $key ? 'bg-white text-brand-700 shadow-sm' : 'text-stone-600 hover:text-stone-900' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

<div class="mt-6 overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Job</th>
                    <th class="px-4 py-3 font-medium">Provider</th>
                    <th class="px-4 py-3 font-medium text-center">Applicants</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach ($jobs as $job)
                    <tr class="hover:bg-stone-50/60">
                        <td class="px-4 py-3">
                            <a href="{{ route('jobs.show', $job) }}" class="font-medium text-stone-900 hover:text-brand-700">{{ $job->title }}</a>
                            <p class="text-xs text-stone-500">{{ $job->company }} &middot; {{ $job->category }}</p>
                        </td>
                        <td class="px-4 py-3 text-stone-600">{{ $job->provider->name }}</td>
                        <td class="px-4 py-3 text-center text-stone-600">{{ $job->applications_count }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize {{ $job->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-200 text-stone-600' }}">{{ $job->status }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                <form method="POST" action="{{ route('admin.jobs.toggle', $job) }}">@csrf
                                    <button class="rounded-md px-2 py-1 text-xs font-medium text-stone-600 hover:bg-stone-100">{{ $job->status === 'open' ? 'Close' : 'Reopen' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.jobs.delete', $job) }}" onsubmit="return confirm('Delete this job and its applications?')">@csrf @method('DELETE')
                                    <button class="rounded-md px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $jobs->links() }}</div>
@endsection
