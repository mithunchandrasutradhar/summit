@extends('layouts.app')

@section('title', __('BACCO Freelancer Forum').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('BACCO Freelancer Forum') }}</h1>
        <p class="mt-4 max-w-2xl text-slate-600">
            {{ __('A long-term community for freelancers, aspiring freelancers and agencies — connect, learn and grow together beyond the summit itself.') }}
        </p>

        <div class="mt-8">
            <a href="{{ lroute('forum.register') }}" class="inline-block rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                {{ __('Join the Forum') }}
            </a>
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-white p-5">
                <h3 class="font-bold text-slate-900">{{ __('Community') }}</h3>
                <p class="mt-2 text-sm text-slate-600">{{ __('Network with fellow freelancers, agencies and industry mentors year-round.') }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5">
                <h3 class="font-bold text-slate-900">{{ __('Learning') }}</h3>
                <p class="mt-2 text-sm text-slate-600">{{ __('Access skill-building resources, workshops and success stories from across the ecosystem.') }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5">
                <h3 class="font-bold text-slate-900">{{ __('Opportunities') }}</h3>
                <p class="mt-2 text-sm text-slate-600">{{ __('Early access to future summit editions, campus programs and BACCO initiatives.') }}</p>
            </div>
        </div>

        @if ($faqs->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-xl font-bold text-slate-900">{{ __('Frequently Asked Questions') }}</h2>
                <div class="mt-6 space-y-4">
                    @foreach ($faqs as $faq)
                        <details class="rounded-lg border border-slate-200 bg-white p-4">
                            <summary class="cursor-pointer font-semibold text-slate-900">{{ $faq->question }}</summary>
                            <p class="mt-2 text-sm text-slate-600">{{ $faq->answer }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
