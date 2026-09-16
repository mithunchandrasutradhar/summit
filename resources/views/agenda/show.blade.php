@extends('layouts.app')

@section('title', $session->resolvedSeoTitle($session->title).' — '.config('app.name'))
@section('meta_description', $session->resolvedSeoDescription($session->description))
@section('og_image', $session->resolvedOgImageUrl())

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <a href="{{ lroute('agenda.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; {{ __('Agenda') }}</a>

        <span class="mt-4 inline-block rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-700">
            {{ ucwords(str_replace('_', ' ', $session->type)) }}
        </span>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $session->title }}</h1>

        <div class="mt-6 flex flex-wrap gap-6 text-sm text-slate-600">
            @if ($session->date)
                <div>{{ $session->date->translatedFormat('d F Y') }}</div>
            @endif
            @if ($session->start_time)
                <div>
                    {{ \Illuminate\Support\Carbon::parse($session->start_time)->format('h:i A') }}
                    @if ($session->end_time) &ndash; {{ \Illuminate\Support\Carbon::parse($session->end_time)->format('h:i A') }} @endif
                </div>
            @endif
            @if ($session->hall)
                <div>{{ $session->hall->name }}</div>
            @endif
            @if ($session->track)
                <div>{{ __('Track') }}: {{ $session->track }}</div>
            @endif
        </div>

        @if ($session->startsAt())
            <a href="{{ lroute('agenda.ics', ['slug' => $session->slug]) }}" class="mt-6 inline-flex items-center gap-2 rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-brand-600 hover:text-brand-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                {{ __('Add to Calendar') }}
            </a>
        @endif

        @if ($session->description)
            <div class="prose prose-slate mt-8 max-w-none">
                {!! nl2br(e($session->description)) !!}
            </div>
        @endif

        @if ($session->speakers->isNotEmpty())
            <div class="mt-10">
                @include('partials.speaker-list', ['speakers' => $session->speakers])
            </div>
        @endif
    </article>
@endsection
