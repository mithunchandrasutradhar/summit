<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 24px;">
    <p style="font-weight: bold; color: #dd6103;">{{ config('app.name') }}</p>
    <div>{!! $body !!}</div>
    <p style="margin-top: 32px; font-size: 12px; color: #94a3b8;">
        {{ config('app.name') }} &middot; {{ config('app.url') }}
    </p>
</body>
</html>
