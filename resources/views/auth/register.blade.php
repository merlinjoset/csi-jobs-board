@extends('layouts.app')
@section('title', 'Register')

@section('content')
<div class="mx-auto max-w-md">
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-2xl font-bold text-stone-900">Create your account</h1>
        <p class="mt-1 text-sm text-stone-600">Join as a job seeker or a job provider.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">I am a</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer rounded-xl border border-stone-300 p-3 text-center has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50">
                        <input type="radio" name="role" value="seeker" class="sr-only" {{ old('role', 'seeker') === 'seeker' ? 'checked' : '' }}>
                        <span class="block font-semibold text-stone-800">Job Seeker</span>
                        <span class="block text-xs text-stone-500">Upload a resume, get matched</span>
                    </label>
                    <label class="cursor-pointer rounded-xl border border-stone-300 p-3 text-center has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50">
                        <input type="radio" name="role" value="provider" class="sr-only" {{ old('role') === 'provider' ? 'checked' : '' }}>
                        <span class="block font-semibold text-stone-800">Job Provider</span>
                        <span class="block text-xs text-stone-500">Post jobs, view applicants</span>
                    </label>
                </div>
            </div>

            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-stone-700">Full name</span>
                <input name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-stone-700">Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            </label>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium text-stone-700">Password</span>
                    <input type="password" name="password" required class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium text-stone-700">Confirm</span>
                    <input type="password" name="password_confirmation" required class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                </label>
            </div>

            <button class="w-full rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-900">Create account</button>
        </form>

        <p class="mt-4 text-center text-sm text-stone-600">
            Already have an account? <a href="{{ route('login') }}" class="font-medium text-brand-700 hover:underline">Sign in</a>
        </p>
    </div>
</div>
@endsection
