@extends('layouts.app')

@section('title', __('Nominate for Freelancer Awards').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-2xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Nominate for Freelancer Awards') }}</h1>
        <p class="mt-4 text-slate-600">{{ __('Recognize an outstanding freelancer, entrepreneur or digital professional.') }}</p>

        <div class="mt-10">
            <livewire:forms.award-nomination-form />
        </div>
    </section>
@endsection
