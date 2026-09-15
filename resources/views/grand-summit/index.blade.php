@extends('layouts.app')

@section('title', __('Grand Summit').' — '.config('app.name'))
@section('meta_description', $event?->description)

@section('content')
    @include('partials.announcements-banner')

    <section class="mx-auto max-w-5xl px-4 py-16 text-center sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Grand Summit') }}</h1>

        @if ($event)
            <div class="mt-6 flex flex-wrap items-center justify-center gap-6 text-sm text-slate-600">
                @if ($event->date_start)
                    <div>
                        {{ $event->date_start->translatedFormat('d F Y') }}
                        @if ($event->date_end && ! $event->date_start->isSameDay($event->date_end))
                            &ndash; {{ $event->date_end->translatedFormat('d F Y') }}
                        @endif
                    </div>
                @endif
                @if ($event->venue_name)
                    <div>{{ $event->venue_name }}</div>
                @endif
                @if ($event->venue_address)
                    <div>{{ $event->venue_address }}</div>
                @endif
            </div>

            @if ($event->description)
                <p class="mx-auto mt-8 max-w-2xl text-slate-600">{{ $event->description }}</p>
            @endif
        @endif

        <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ lroute('agenda.index') }}" class="rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                {{ __('Explore Agenda') }}
            </a>
            <a href="{{ lroute('speakers.index') }}" class="rounded-md border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-brand-600 hover:text-brand-700">
                {{ __('Meet the Speakers') }}
            </a>
        </div>
    </section>

    @if ($highlightSessions->isNotEmpty())
        <section class="bg-slate-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl font-bold text-slate-900">{{ __('Agenda Highlights') }}</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($highlightSessions as $session)
                        <a href="{{ lroute('agenda.show', ['slug' => $session->slug]) }}" class="block rounded-lg border border-slate-100 bg-white p-4 shadow-sm hover:shadow-md">
                            <span class="text-xs font-semibold uppercase tracking-wide text-brand-600">{{ ucwords(str_replace('_', ' ', $session->type)) }}</span>
                            <h3 class="mt-1 font-semibold text-slate-900">{{ $session->title }}</h3>
                            @if ($session->date)
                                <p class="mt-1 text-xs text-slate-500">{{ $session->date->translatedFormat('d M') }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
