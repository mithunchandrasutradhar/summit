<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check-In Result — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-900 p-4 text-white">
    <div class="mx-auto max-w-sm text-center">
        @switch($status)
            @case('checked_in')
                <div class="text-6xl">&#9989;</div>
                <h1 class="mt-4 text-2xl font-bold text-green-400">{{ __('Checked In') }}</h1>
                <p class="mt-2 text-lg">{{ $registration->name() ?? $registration->email }}</p>
                <p class="text-sm text-slate-400 font-mono">{{ $registration->reference_no }}</p>
                @break

            @case('already_checked_in')
                <div class="text-6xl">&#9888;&#65039;</div>
                <h1 class="mt-4 text-2xl font-bold text-yellow-400">{{ __('Already Checked In') }}</h1>
                <p class="mt-2 text-lg">{{ $registration->name() ?? $registration->email }}</p>
                <p class="text-sm text-slate-400">{{ __('Checked in at') }} {{ $registration->checked_in_at?->format('h:i A') }}</p>
                @break

            @case('cancelled')
                <div class="text-6xl">&#10060;</div>
                <h1 class="mt-4 text-2xl font-bold text-red-400">{{ __('Registration Cancelled') }}</h1>
                <p class="mt-2 text-lg">{{ $registration->name() ?? $registration->email }}</p>
                @break

            @default
                <div class="text-6xl">&#10060;</div>
                <h1 class="mt-4 text-2xl font-bold text-red-400">{{ __('Ticket Not Found') }}</h1>
        @endswitch

        <a href="{{ route('checkin.scanner') }}" class="mt-8 inline-block rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white">
            {{ __('Scan Next') }}
        </a>
    </div>
</body>
</html>
