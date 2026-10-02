@extends('layouts.app')
@section('title', 'All applications')

@section('content')
<h1 class="text-2xl font-bold text-stone-900">Applications</h1>
<p class="mt-1 text-sm text-stone-600">Every application across the portal.</p>

<div class="mt-6 overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Seeker</th>
                    <th class="px-4 py-3 font-medium">Job</th>
                    <th class="px-4 py-3 font-medium">Provider</th>
                    <th class="px-4 py-3 font-medium">Resume</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Applied</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($applications as $app)
                    <tr class="hover:bg-stone-50/60">
                        <td class="px-4 py-3">
                            <span class="block font-medium text-stone-900">{{ $app->seeker->name }}</span>
                            <span class="block text-xs text-stone-500">{{ $app->seeker->email }}</span>
                        </td>
                        <td class="px-4 py-3"><a href="{{ route('jobs.show', $app->jobPost) }}" class="text-stone-700 hover:text-brand-700">{{ $app->jobPost->title }}</a></td>
                        <td class="px-4 py-3 text-stone-600">{{ $app->jobPost->provider->name }}</td>
                        <td class="px-4 py-3">
                            @if ($app->resume)
                                <button type="button" data-resume-url="{{ route('resumes.show', $app->resume) }}" data-resume-name="{{ $app->resume->original_name }}" class="text-xs font-medium text-brand-700 hover:underline">View</button>
                            @else
                                <span class="text-xs text-stone-400">none</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php $tone = ['applied'=>'bg-stone-100 text-stone-600','shortlisted'=>'bg-emerald-100 text-emerald-700','interview'=>'bg-brand-100 text-brand-700','rejected'=>'bg-red-100 text-red-700'][$app->status] ?? 'bg-stone-100 text-stone-600'; @endphp
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize {{ $tone }}">{{ $app->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-stone-500">{{ $app->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-stone-400">No applications yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $applications->links() }}</div>
@endsection
