@extends('layouts.app')

@section('title', __('District Roadshows').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('District Roadshows') }}</h1>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($districts as $district)
                <a href="{{ lroute('districts.show', ['slug' => $district->slug]) }}" class="block rounded-lg border border-slate-100 p-5 shadow-sm hover:shadow-md">
                    <p class="text-xs font-semibold uppercase tracking-wide text-brand-600">{{ $district->division->name ?? '' }}</p>
                    <h2 class="mt-1 font-bold text-slate-900">{{ $district->name }}</h2>
                    @if ($district->event_date)
                        <p class="mt-2 text-sm text-slate-500">{{ $district->event_date->translatedFormat('d M, Y') }}</p>
                    @endif
                    @if ($district->venue)
                        <p class="text-sm text-slate-500">{{ $district->venue }}</p>
                    @endif
                    <span class="mt-3 inline-block rounded-full px-2 py-0.5 text-xs font-medium {{ $district->status === 'completed' ? 'bg-slate-100 text-slate-600' : 'bg-brand-50 text-brand-700' }}">
                        {{ ucfirst($district->status) }}
                    </span>
                </a>
            @empty
                <p class="col-span-full text-slate-500">{{ __('District roadshows will be announced soon.') }}</p>
            @endforelse
        </div>
    </section>
@endsection
