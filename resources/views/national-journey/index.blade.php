@extends('layouts.app')

@section('title', __('National Journey').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('National Journey') }}</h1>
        <p class="mt-4 max-w-2xl text-slate-600">{{ __('From 8 divisions to 100 campuses, the road to the Grand Summit runs through every corner of Bangladesh.') }}</p>

        @if ($stats->isNotEmpty())
            <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
                @foreach ([
                    'divisions_covered' => __('Divisions Covered'),
                    'districts_covered' => __('Districts Covered'),
                    'institutions_activated' => __('Institutions Activated'),
                    'participants_reached' => __('Participants Reached'),
                ] as $key => $label)
                    <div class="rounded-lg bg-slate-50 p-5 text-center">
                        <p class="text-3xl font-extrabold text-brand-700">{{ number_format($stats[$key] ?? 0) }}</p>
                        <p class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($divisions as $division)
                <a href="{{ lroute('divisions.show', ['slug' => $division->slug]) }}" class="group block overflow-hidden rounded-lg border border-slate-100 shadow-sm hover:shadow-md">
                    @if ($division->imageUrl('medium'))
                        <x-responsive-image
                            :thumb="$division->imageUrl('thumb')"
                            :medium="$division->imageUrl('medium')"
                            sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw"
                            class="h-32 w-full object-cover"
                        />
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

        <div class="mt-14 flex flex-wrap gap-4">
            <a href="{{ lroute('districts.index') }}" class="rounded-md border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-brand-600 hover:text-brand-700">
                {{ __('District Roadshows') }} &rarr;
            </a>
            <a href="{{ lroute('campus-programs.index') }}" class="rounded-md border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-brand-600 hover:text-brand-700">
                {{ __('Campus Programs') }} &rarr;
            </a>
        </div>
    </section>
@endsection
