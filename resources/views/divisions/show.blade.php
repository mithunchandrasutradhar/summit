@extends('layouts.app')

@section('title', $division->name.' — '.config('app.name'))
@section('meta_description', $division->description)

@section('content')
    <article class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <a href="{{ lroute('divisions.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; {{ __('Divisional Summits') }}</a>

        <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $division->name }}</h1>

        @if ($division->imageUrl('medium'))
            <x-responsive-image
                :medium="$division->imageUrl('medium')"
                :large="$division->imageUrl('large')"
                sizes="(min-width: 1024px) 800px, 100vw"
                class="mt-8 h-64 w-full rounded-lg object-cover"
            />
        @endif

        @if ($division->description)
            <div class="prose prose-slate mt-8 max-w-none">
                {!! nl2br(e($division->description)) !!}
            </div>
        @endif

        <h2 class="mt-12 text-xl font-bold text-slate-900">{{ __('District Roadshows in this Division') }}</h2>
        <div class="mt-6 grid gap-6 sm:grid-cols-2">
            @forelse ($districts as $district)
                <a href="{{ lroute('districts.show', ['slug' => $district->slug]) }}" class="block rounded-lg border border-slate-100 p-5 shadow-sm hover:shadow-md">
                    <h3 class="font-bold text-slate-900">{{ $district->name }}</h3>
                    @if ($district->event_date)
                        <p class="mt-1 text-sm text-slate-500">{{ $district->event_date->translatedFormat('d M, Y') }}</p>
                    @endif
                    @if ($district->venue)
                        <p class="mt-1 text-sm text-slate-500">{{ $district->venue }}</p>
                    @endif
                </a>
            @empty
                <p class="text-slate-500">{{ __('District roadshows for this division will be announced soon.') }}</p>
            @endforelse
        </div>
    </article>
@endsection
