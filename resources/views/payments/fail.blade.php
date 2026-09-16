@extends('layouts.app')

@section('title', __('Payment Failed').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-xl px-4 py-24 text-center sm:px-6 lg:px-8">
        <div class="rounded-xl border border-red-200 bg-red-50 p-10">
            <h1 class="text-2xl font-bold text-red-900">{{ __('Payment Failed') }}</h1>
            <p class="mt-3 text-sm text-red-800">
                {{ __('Something went wrong processing your payment. No amount has been charged. Please try again or contact us for help.') }}
            </p>
        </div>
        <a href="{{ lroute('home') }}" class="mt-8 inline-block text-sm font-medium text-brand-600 hover:text-brand-700">
            {{ __('Return to homepage') }} &rarr;
        </a>
    </section>
@endsection
