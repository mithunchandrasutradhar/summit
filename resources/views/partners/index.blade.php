@extends('layouts.app')

@section('title', __('Partners').' — '.config('app.name'))

@php
    $categoryLabels = [
        'organizer' => __('Organizer'),
        'government' => __('Government & Development Partners'),
        'strategic' => __('Strategic Partners'),
        'technology' => __('Technology Partners'),
        'knowledge' => __('Knowledge Partners'),
        'media' => __('Media Partners'),
    ];
@endphp

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Partners') }}</h1>

        @foreach ($categoryLabels as $key => $label)
            @if ($partnersByCategory->has($key))
                <div class="mt-12">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</h2>
                    <div class="mt-6 grid grid-cols-2 gap-8 sm:grid-cols-3 lg:grid-cols-5">
                        @foreach ($partnersByCategory[$key] as $partner)
                            <a href="{{ $partner->website ?: '#' }}" target="_blank" rel="noopener" class="flex items-center justify-center rounded-lg border border-slate-100 p-4 grayscale transition hover:grayscale-0">
                                @if ($partner->logoUrl('thumb'))
                                    <img src="{{ $partner->logoUrl('thumb') }}" alt="{{ $partner->name }}" class="max-h-16 w-auto">
                                @else
                                    <span class="text-sm font-medium text-slate-700">{{ $partner->name }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        @if ($partnersByCategory->isEmpty())
            <p class="mt-10 text-slate-500">{{ __('Partner logos will appear here as they are confirmed.') }}</p>
        @endif
    </section>
@endsection
