@extends('layouts.app')

@section('title', __('Payment Cancelled').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-xl px-4 py-24 text-center sm:px-6 lg:px-8">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-10">
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Payment Cancelled') }}</h1>
            <p class="mt-3 text-sm text-slate-600">
                {{ __('You cancelled the payment. No amount has been charged — you can try again whenever you\'re ready.') }}
            </p>
        </div>
        <a href="{{ lroute('home') }}" class="mt-8 inline-block text-sm font-medium text-brand-600 hover:text-brand-700">
            {{ __('Return to homepage') }} &rarr;
        </a>
    </section>
@endsection
