@extends('layouts.app')

@section('title', __('Apply for a Booth').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-2xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Apply for a Booth') }}</h1>
        <p class="mt-4 text-slate-600">{{ __('Reserve your exhibition space at Freelancer Summit Bangladesh 2026.') }}</p>

        <div class="mt-10">
            <livewire:forms.exhibitor-application-form />
        </div>
    </section>
@endsection
