<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gate Check-In — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/checkin.js'])
</head>
<body class="min-h-screen bg-slate-900 p-4 text-white">
    <div class="mx-auto max-w-md text-center">
        <h1 class="text-xl font-bold">{{ __('Gate Check-In') }}</h1>
        <p class="mt-2 text-sm text-slate-300">{{ __('Point the camera at an attendee\'s QR e-ticket.') }}</p>

        <div id="qr-reader" class="mt-6 overflow-hidden rounded-lg" data-checkin-base-url="{{ url('/checkin') }}"></div>
        <p id="qr-error" class="mt-4 text-sm text-red-400"></p>
    </div>
</body>
</html>
