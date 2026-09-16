@extends('layouts.app')

@section('title', __('Payment Successful').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-xl px-4 py-24 text-center sm:px-6 lg:px-8">
        <div class="rounded-xl border border-green-200 bg-green-50 p-10">
            <h1 class="text-2xl font-bold text-green-900">{{ __('Payment Successful!') }}</h1>
            <p class="mt-3 text-sm text-green-800">
                {{ __('Thank you — your payment has been received and your confirmation email is on its way.') }}
            </p>
        </div>
        <a href="{{ lroute('home') }}" class="mt-8 inline-block text-sm font-medium text-brand-600 hover:text-brand-700">
            {{ __('Return to homepage') }} &rarr;
        </a>
    </section>
@endsection
