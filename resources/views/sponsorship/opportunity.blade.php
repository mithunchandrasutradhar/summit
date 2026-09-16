@extends('layouts.app')

@section('title', __('Sponsorship Opportunities').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Sponsorship Opportunities') }}</h1>
        <p class="mt-4 max-w-2xl text-slate-600">
            {{ __('Partner with Freelancer Summit Bangladesh 2026 and connect your brand with the nation\'s freelance and digital economy.') }}
        </p>

        @if ($tiers->isNotEmpty())
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($tiers as $tier)
                    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="font-bold text-slate-900">{{ $tier->name }}</h3>
                        <p class="mt-1 text-lg font-extrabold text-brand-600">
                            {{ __('BDT :amount', ['amount' => number_format((float) $tier->price)]) }}
                        </p>
                        @if ($tier->benefits)
                            <p class="mt-2 text-sm text-slate-600">{{ Str::limit($tier->benefits, 120) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-12 max-w-2xl">
            <h2 class="text-xl font-bold text-slate-900">{{ __('Submit a Sponsorship Enquiry') }}</h2>
            <div class="mt-6">
                <livewire:forms.sponsorship-enquiry-form />
            </div>
        </div>
    </section>
@endsection
