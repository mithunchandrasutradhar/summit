@extends('layouts.app')

@section('title', __('Join the BACCO Freelancer Forum').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-2xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Join the BACCO Freelancer Forum') }}</h1>
        <p class="mt-4 text-slate-600">{{ __('Become part of a long-term community for freelancers, aspiring freelancers and agencies.') }}</p>

        <div class="mt-10">
            <livewire:forms.forum-membership-form />
        </div>
    </section>
@endsection
