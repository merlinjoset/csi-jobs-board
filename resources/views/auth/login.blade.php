@extends('layouts.app')
@section('title', 'Sign in')

@section('content')
<div class="mx-auto max-w-md">
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-2xl font-bold text-stone-900">Sign in</h1>
        <p class="mt-1 text-sm text-stone-600">Welcome back to the CSI Job Portal.</p>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-stone-700">Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-stone-700">Password</span>
                <input type="password" name="password" required class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            </label>
            <label class="flex items-center gap-2 text-sm text-stone-600">
                <input type="checkbox" name="remember" class="rounded border-stone-300"> Remember me
            </label>
            <button class="w-full rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-900">Sign in</button>
        </form>

        <p class="mt-4 text-center text-sm text-stone-600">
            New here? <a href="{{ route('register') }}" class="font-medium text-brand-700 hover:underline">Create an account</a>
        </p>

        <div class="mt-6 rounded-xl border border-dashed border-stone-300 bg-stone-50 p-4 text-xs text-stone-600">
            <p class="font-semibold uppercase tracking-wide text-stone-500">Demo accounts</p>
            <p class="mt-1">Provider: provider@example.com / password</p>
            <p>Seeker: seeker@example.com / password</p>
        </div>
    </div>
</div>
@endsection
