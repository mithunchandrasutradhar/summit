@extends('layouts.app')

@section('title', __('Award Categories').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Award Categories') }}</h1>

        <div class="mt-10 space-y-8">
            @forelse ($categories as $category)
                <div class="rounded-lg border border-slate-100 p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900">{{ $category->name }}</h2>

                    @if ($category->description)
                        <p class="mt-3 text-slate-600">{{ $category->description }}</p>
                    @endif

                    @if ($category->eligibility)
                        <p class="mt-3 text-sm text-slate-700"><span class="font-semibold">{{ __('Eligibility') }}:</span> {{ $category->eligibility }}</p>
                    @endif

                    @if ($category->criteria)
                        <p class="mt-1 text-sm text-slate-700"><span class="font-semibold">{{ __('Criteria') }}:</span> {{ $category->criteria }}</p>
                    @endif

                    @if ($category->submission_deadline)
                        <p class="mt-3 text-xs font-medium uppercase tracking-wide text-brand-600">
                            {{ __('Submission Deadline') }}: {{ $category->submission_deadline->translatedFormat('d F Y') }}
                        </p>
                    @endif

                    <a href="{{ lroute('awards.nominate') }}" class="mt-4 inline-block text-sm font-semibold text-brand-600 hover:text-brand-700">
                        {{ __('Nominate in this category') }} &rarr;
                    </a>
                </div>
            @empty
                <p class="text-slate-500">{{ __('Award categories will be announced soon.') }}</p>
            @endforelse
        </div>
    </section>
@endsection
