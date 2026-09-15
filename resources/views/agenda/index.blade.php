@extends('layouts.app')

@section('title', __('Agenda').' — '.config('app.name'))

@section('content')
    @include('partials.announcements-banner')

    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Agenda') }}</h1>
        <p class="mt-4 max-w-2xl text-slate-600">{{ __('Keynotes, workshops, panels and award sessions across the Grand Summit.') }}</p>

        <div class="mt-10">
            <livewire:agenda />
        </div>
    </section>
@endsection
