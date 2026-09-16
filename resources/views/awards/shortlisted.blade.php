@extends('layouts.app')

@section('title', __('Shortlisted Nominees').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Shortlisted Nominees') }}</h1>

        <div class="mt-10 space-y-10">
            @forelse ($nominations as $categoryName => $group)
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $categoryName }}</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($group as $nomination)
                            <div class="rounded-lg border border-slate-100 p-4 text-center shadow-sm">
                                <p class="font-semibold text-slate-900">{{ $nomination->name() }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-slate-500">{{ __('The shortlist will be announced soon.') }}</p>
            @endforelse
        </div>
    </section>
@endsection
