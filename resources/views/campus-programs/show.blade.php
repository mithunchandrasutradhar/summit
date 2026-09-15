@extends('layouts.app')

@section('title', $campusProgram->resolvedSeoTitle($campusProgram->institution_name).' — '.config('app.name'))
@section('meta_description', $campusProgram->resolvedSeoDescription($campusProgram->description))

@section('content')
    <article class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <a href="{{ lroute('campus-programs.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; {{ __('Campus Programs') }}</a>

        <span class="mt-4 inline-block rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-700">{{ ucfirst($campusProgram->type) }}</span>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $campusProgram->institution_name }}</h1>

        <div class="mt-6 flex flex-wrap gap-6 text-sm text-slate-600">
            @if ($campusProgram->event_date)
                <div>{{ $campusProgram->event_date->translatedFormat('d F Y') }}</div>
            @endif
            @if ($campusProgram->venue)
                <div>{{ $campusProgram->venue }}</div>
            @endif
            @if ($campusProgram->district)
                <div>{{ $campusProgram->district->name }}</div>
            @endif
            @if ($campusProgram->organizer_partner)
                <div>{{ $campusProgram->organizer_partner }}</div>
            @endif
        </div>

        @if ($campusProgram->ambassador_name)
            <p class="mt-4 text-sm text-slate-600">
                <span class="font-semibold text-slate-900">{{ __('Campus Ambassador:') }}</span> {{ $campusProgram->ambassador_name }}
            </p>
        @endif

        @if ($campusProgram->description)
            <div class="prose prose-slate mt-8 max-w-none">
                {!! nl2br(e($campusProgram->description)) !!}
            </div>
        @endif

        <div class="mt-10">
            @include('partials.speaker-list', ['speakers' => $campusProgram->speakers])
        </div>

        <div class="mt-10">
            @include('partials.gallery-strip', ['galleries' => $campusProgram->galleries])
        </div>

        <div class="mt-12">
            @include('partials.activation-lead-form', [
                'action' => lroute('campus-programs.leads.store', ['slug' => $campusProgram->slug]),
                'ambassador' => true,
            ])
        </div>
    </article>
@endsection
