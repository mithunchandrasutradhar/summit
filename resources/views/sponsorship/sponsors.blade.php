@extends('layouts.app')

@section('title', __('Our Sponsors').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Our Sponsors') }}</h1>
        <p class="mt-4 max-w-2xl text-slate-600">{{ __('Freelancer Summit Bangladesh 2026 is proud to be supported by the following partners.') }}</p>

        <div class="mt-6">
            <a href="{{ lroute('sponsorship.opportunity') }}" class="inline-block rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                {{ __('Become a Sponsor') }}
            </a>
        </div>

        @forelse ($sponsors as $tierName => $tierSponsors)
            <div class="mt-12">
                <h2 class="text-xl font-bold text-slate-900">{{ $tierName ?? __('Sponsors') }}</h2>
                <div class="mt-6 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($tierSponsors as $sponsor)
                        <div class="flex items-center justify-center rounded-lg border border-slate-200 bg-white p-6">
                            @if ($sponsor->logoUrl('thumb'))
                                <img src="{{ $sponsor->logoUrl('thumb') }}" alt="{{ $sponsor->name }}" class="max-h-16 w-auto">
                            @else
                                <span class="text-sm font-semibold text-slate-700">{{ $sponsor->name }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="mt-12 text-slate-500">{{ __('Sponsors will be announced soon.') }}</p>
        @endforelse
    </section>
@endsection
