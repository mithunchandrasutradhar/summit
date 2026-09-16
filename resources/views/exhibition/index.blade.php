@extends('layouts.app')

@section('title', __('Exhibition').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Exhibition') }}</h1>
        <p class="mt-4 max-w-2xl text-slate-600">
            {{ __('Showcase your products and services to thousands of freelancers, entrepreneurs and digital professionals.') }}
        </p>

        <div class="mt-6 flex flex-wrap gap-4">
            <a href="{{ lroute('exhibition.apply') }}" class="inline-block rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                {{ __('Apply for a Booth') }}
            </a>
        </div>

        @if ($booths->isNotEmpty())
            <p class="mt-10 text-sm text-slate-500">
                {{ __(':available of :total booths available.', ['available' => $booths->where('status', 'available')->count(), 'total' => $booths->count()]) }}
            </p>
        @endif

        @if ($exhibitors->isNotEmpty())
            <div class="mt-6">
                <h2 class="text-xl font-bold text-slate-900">{{ __('Confirmed Exhibitors') }}</h2>
                <div class="mt-6 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($exhibitors as $exhibitor)
                        <div class="rounded-lg border border-slate-200 bg-white p-5 text-center">
                            @if ($exhibitor->logoUrl('thumb'))
                                <img src="{{ $exhibitor->logoUrl('thumb') }}" alt="{{ $exhibitor->company_name }}" class="mx-auto max-h-14 w-auto">
                            @else
                                <span class="text-sm font-semibold text-slate-700">{{ $exhibitor->company_name }}</span>
                            @endif
                            @if ($exhibitor->booth)
                                <p class="mt-2 text-xs text-slate-500">{{ $exhibitor->booth->name }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
