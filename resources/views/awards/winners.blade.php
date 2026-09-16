@extends('layouts.app')

@section('title', __('Award Winners').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Award Winners') }}</h1>
        <p class="mt-4 text-slate-600">{{ __('A permanent archive of Freelancer Summit Bangladesh award winners.') }}</p>

        <div class="mt-10 space-y-10">
            @forelse ($nominations as $categoryName => $group)
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $categoryName }}</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($group as $nomination)
                            <div class="rounded-lg border border-brand-100 bg-brand-50 p-4 text-center shadow-sm">
                                <p class="text-2xl">&#127942;</p>
                                <p class="mt-1 font-semibold text-slate-900">{{ $nomination->name() }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-slate-500">{{ __('Winners will be announced at the Grand Summit.') }}</p>
            @endforelse
        </div>
    </section>
@endsection
