@extends('layouts.app')
@section('title', 'Manage users')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-stone-900">Users</h1>
        <p class="mt-1 text-sm text-stone-600">Manage accounts and roles.</p>
    </div>
    <div class="flex gap-1 rounded-lg border border-stone-200 bg-stone-100 p-1 text-sm">
        @foreach (['' => 'All', 'seeker' => 'Seekers', 'provider' => 'Providers', 'admin' => 'Admins'] as $key => $label)
            <a href="{{ route('admin.users', $key ? ['role' => $key] : []) }}" class="rounded-md px-3 py-1.5 font-medium {{ $role === $key ? 'bg-white text-brand-700 shadow-sm' : 'text-stone-600 hover:text-stone-900' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

<div class="mt-6 overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3 font-medium">User</th>
                    <th class="px-4 py-3 font-medium">Role</th>
                    <th class="px-4 py-3 font-medium text-center">Jobs</th>
                    <th class="px-4 py-3 font-medium text-center">Applications</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-stone-50/60">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <div>
                                    <span class="block font-medium text-stone-900">{{ $user->name }}@if($user->id === auth()->id())<span class="ml-1 text-xs font-normal text-stone-400">(you)</span>@endif</span>
                                    <span class="block text-xs text-stone-500">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.users.role', $user) }}">@csrf
                                <select name="role" onchange="this.form.submit()" class="rounded-md border border-stone-300 bg-white px-2 py-1 text-xs capitalize">
                                    @foreach (['seeker','provider','admin'] as $r)
                                        <option value="{{ $r }}" @selected($user->role === $r)>{{ $r }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-center text-stone-600">{{ $user->job_posts_count }}</td>
                        <td class="px-4 py-3 text-center text-stone-600">{{ $user->applications_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end">
                                <form method="POST" action="{{ route('admin.users.delete', $user) }}" onsubmit="return confirm('Delete {{ $user->name }} and all their records?')">@csrf @method('DELETE')
                                    <button class="rounded-md px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 disabled:opacity-40" @disabled($user->id === auth()->id())>Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $users->links() }}</div>
@endsection
