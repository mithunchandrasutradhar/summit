@extends('layouts.app')

@section('title', __('Register for Summit').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-2xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Register for Summit') }}</h1>
        <p class="mt-4 text-slate-600">{{ __('Join thousands of freelancers, digital professionals and industry leaders at the Grand Summit in Dhaka.') }}</p>

        <div class="mt-10">
            <livewire:forms.summit-registration-form />
        </div>
    </section>
@endsection
