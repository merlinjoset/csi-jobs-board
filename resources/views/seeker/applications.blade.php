@extends('layouts.app')
@section('title', 'My applications')

@section('content')
<h1 class="text-2xl font-bold text-stone-900">My applications</h1>
<p class="mt-1 text-sm text-stone-600">Jobs you have applied to and their current status.</p>

@if ($applications->isEmpty())
    <div class="mt-6 rounded-xl border border-dashed border-stone-300 bg-white p-12 text-center">
        <p class="font-medium text-stone-700">You have not applied to any jobs yet</p>
        <a href="{{ route('home') }}" class="mt-3 inline-block rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-900">Browse jobs</a>
    </div>
@else
    <div class="mt-6 overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Job</th>
                    <th class="px-4 py-3 font-medium">Company</th>
                    <th class="px-4 py-3 font-medium">Applied</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach ($applications as $app)
                    <tr class="hover:bg-stone-50/60">
                        <td class="px-4 py-3"><a href="{{ route('jobs.show', $app->jobPost) }}" class="font-medium text-stone-900 hover:text-brand-700">{{ $app->jobPost->title }}</a></td>
                        <td class="px-4 py-3 text-stone-600">{{ $app->jobPost->company }}</td>
                        <td class="px-4 py-3 text-stone-500">{{ $app->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            @php $tone = ['applied'=>'bg-stone-100 text-stone-600','shortlisted'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700'][$app->status]; @endphp
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize {{ $tone }}">{{ $app->status }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
