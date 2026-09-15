@extends('layouts.app')

@section('title', __('Divisional Summits').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Divisional Summits') }}</h1>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($divisions as $division)
                <a href="{{ lroute('divisions.show', ['slug' => $division->slug]) }}" class="group block overflow-hidden rounded-lg border border-slate-100 shadow-sm hover:shadow-md">
                    @if ($division->imageUrl())
                        <img src="{{ $division->imageUrl() }}" alt="" class="h-32 w-full object-cover">
                    @else
                        <div class="h-32 w-full bg-brand-50"></div>
                    @endif
                    <div class="p-4">
                        <h2 class="font-bold text-slate-900 group-hover:text-brand-700">{{ $division->name }}</h2>
                        <p class="mt-1 text-xs text-slate-500">{{ __(':count districts', ['count' => $division->districts->count()]) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endsection
