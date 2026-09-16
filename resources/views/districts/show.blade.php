@extends('layouts.app')

@section('title', $district->resolvedSeoTitle($district->name.' Roadshow').' — '.config('app.name'))
@section('meta_description', $district->resolvedSeoDescription($district->description))
@section('og_image', $district->resolvedOgImageUrl())

@section('content')
    <article class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <a href="{{ lroute('districts.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; {{ __('District Roadshows') }}</a>

        <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-brand-600">{{ $district->division->name ?? '' }}</p>
        <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $district->name }}</h1>

        <div class="mt-6 flex flex-wrap gap-6 text-sm text-slate-600">
            @if ($district->event_date)
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    {{ $district->event_date->translatedFormat('d F Y') }}
                </div>
            @endif
            @if ($district->venue)
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    {{ $district->venue }}
                </div>
            @endif
            @if ($district->organizer_partner)
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4" /></svg>
                    {{ $district->organizer_partner }}
                </div>
            @endif
        </div>

        @if ($district->description)
            <div class="prose prose-slate mt-8 max-w-none">
                {!! nl2br(e($district->description)) !!}
            </div>
        @endif

        <div class="mt-10">
            @include('partials.speaker-list', ['speakers' => $district->speakers])
        </div>

        <div class="mt-10">
            @include('partials.gallery-strip', ['galleries' => $district->galleries])
        </div>

        <div class="mt-12">
            @include('partials.activation-lead-form', ['action' => lroute('districts.leads.store', ['slug' => $district->slug])])
        </div>
    </article>
@endsection
