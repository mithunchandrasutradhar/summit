@extends('layouts.app')

@section('title', __('Freelancer Awards').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-16 text-center sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Freelancer Awards') }}</h1>
        <p class="mx-auto mt-4 max-w-2xl text-slate-600">
            {{ __('Celebrating the freelancers, entrepreneurs and digital professionals shaping Bangladesh\'s digital economy.') }}
        </p>

        <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ lroute('awards.nominate') }}" class="rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                {{ __('Nominate for Freelancer Awards') }}
            </a>
            <a href="{{ lroute('awards.categories') }}" class="rounded-md border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-brand-600 hover:text-brand-700">
                {{ __('View Categories') }}
            </a>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-center gap-6 text-sm">
            <a href="{{ lroute('awards.shortlisted') }}" class="font-medium text-brand-600 hover:text-brand-700">{{ __('Shortlisted Nominees') }} &rarr;</a>
            <a href="{{ lroute('awards.winners') }}" class="font-medium text-brand-600 hover:text-brand-700">{{ __('Winner Archive') }} &rarr;</a>
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <section class="bg-slate-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl font-bold text-slate-900">{{ __('Award Categories') }}</h2>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <div class="rounded-lg bg-white p-5 shadow-sm">
                            <h3 class="font-bold text-slate-900">{{ $category->name }}</h3>
                            @if ($category->description)
                                <p class="mt-2 text-sm text-slate-600">{{ Str::limit($category->description, 100) }}</p>
                            @endif
                            @if ($category->submission_deadline)
                                <p class="mt-3 text-xs font-medium text-brand-600">
                                    {{ __('Deadline') }}: {{ $category->submission_deadline->translatedFormat('d M, Y') }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
