@extends('layouts.app')

@section('title', $speaker->resolvedSeoTitle($speaker->name).' — '.config('app.name'))
@section('meta_description', $speaker->resolvedSeoDescription($speaker->bio))
@section('og_image', $speaker->resolvedOgImageUrl($speaker->photoUrl('medium')))

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <a href="{{ lroute('speakers.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; {{ __('Speakers & Experts') }}</a>

        <div class="mt-6 flex items-center gap-5">
            @if ($speaker->photoUrl('thumb'))
                <img src="{{ $speaker->photoUrl('thumb') }}" alt="" class="h-24 w-24 rounded-full object-cover">
            @else
                <div class="h-24 w-24 rounded-full bg-brand-50"></div>
            @endif
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">{{ $speaker->name }}</h1>
                @if ($speaker->designation || $speaker->organization)
                    <p class="text-sm text-slate-600">{{ trim(($speaker->designation ?? '').(($speaker->designation && $speaker->organization) ? ', ' : '').($speaker->organization ?? '')) }}</p>
                @endif
                @if ($speaker->country)
                    <p class="text-sm text-slate-500">{{ $speaker->country }}</p>
                @endif
            </div>
        </div>

        @if ($speaker->bio)
            <div class="prose prose-slate mt-8 max-w-none">
                {!! nl2br(e($speaker->bio)) !!}
            </div>
        @endif

        @if (!empty($speaker->expertise))
            <div class="mt-6 flex flex-wrap gap-2">
                @foreach ($speaker->expertise as $tag)
                    <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-700">{{ $tag }}</span>
                @endforeach
            </div>
        @endif

        @if (!empty($speaker->social_links))
            <div class="mt-6 flex gap-4">
                @foreach ($speaker->social_links as $platform => $url)
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="text-sm font-medium text-brand-600 hover:text-brand-700">{{ ucfirst($platform) }}</a>
                @endforeach
            </div>
        @endif

        @if ($sessions->isNotEmpty())
            <div class="mt-12">
                <h2 class="text-lg font-bold text-slate-900">{{ __('Sessions') }}</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($sessions as $session)
                        <a href="{{ lroute('agenda.show', ['slug' => $session->slug]) }}" class="block rounded-lg border border-slate-100 p-4 hover:shadow-sm">
                            <p class="font-semibold text-slate-900">{{ $session->title }}</p>
                            @if ($session->date)
                                <p class="text-xs text-slate-500">{{ $session->date->translatedFormat('d M, Y') }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </article>
@endsection
