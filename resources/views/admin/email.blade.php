@extends('layouts.app')
@section('title', 'Email settings')

@section('content')
<h1 class="text-2xl font-bold text-stone-900">Email settings</h1>
<p class="mt-1 text-sm text-stone-600">How the portal sends notification emails, and the templates it uses.</p>

<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
    {{-- Current configuration --}}
    <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <h2 class="font-semibold text-stone-900">Current configuration</h2>
        <dl class="mt-3 divide-y divide-stone-100 text-sm">
            @php
                $rows = [
                    'Mailer / driver' => $config['driver'],
                    'From address' => $config['from_address'],
                    'From name' => $config['from_name'],
                    'SMTP host' => $config['host'] ?: '—',
                    'SMTP port' => $config['port'] ?: '—',
                    'SMTP username' => $config['username'] ? preg_replace('/(?<=.).(?=.*@|.{3})/', '•', $config['username']) : '—',
                    'Encryption' => $config['encryption'] ?: '—',
                ];
            @endphp
            @foreach ($rows as $label => $value)
                <div class="flex items-center justify-between py-2">
                    <dt class="text-stone-500">{{ $label }}</dt>
                    <dd class="font-medium text-stone-800">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>

        @if ($config['driver'] === 'log')
            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                Emails are currently written to <code>storage/logs/laravel.log</code> (the <strong>log</strong> mailer), so no mail actually leaves the server. To send real email, set <code>MAIL_MAILER=smtp</code> and the SMTP values in the <code>.env</code> file, then run <code>php artisan config:clear</code>.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.email.test') }}" class="mt-4 flex items-end gap-2">@csrf
            <label class="block flex-1">
                <span class="mb-1 block text-xs font-medium text-stone-600">Send a test email to</span>
                <input type="email" name="email" required value="{{ auth()->user()->email }}" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            </label>
            <button class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Send test</button>
        </form>
    </section>

    {{-- Templates --}}
    <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <h2 class="font-semibold text-stone-900">Notification templates</h2>
        <p class="mt-1 text-xs text-stone-500">Rendered from <code>resources/views/emails/application.blade.php</code>.</p>
        <div class="mt-3 space-y-3">
            @foreach ($templates as $t)
                <div class="rounded-xl border border-stone-200 p-4">
                    <div class="flex items-center justify-between">
                        <span class="font-medium capitalize text-stone-800">{{ $t['event'] }}</span>
                        <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-700 ring-1 ring-brand-100">to {{ $t['audience'] }}</span>
                    </div>
                    <p class="mt-1 text-sm text-stone-600">{{ $t['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
