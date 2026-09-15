@extends('layouts.app')

@section('title', __('Campus Programs').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Campus Programs') }}</h1>
        <p class="mt-4 max-w-2xl text-slate-600">{{ __('100 universities and polytechnics across Bangladesh, hosting seminars, workshops and AI awareness sessions.') }}</p>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($campusPrograms as $campus)
                <a href="{{ lroute('campus-programs.show', ['slug' => $campus->slug]) }}" class="block rounded-lg border border-slate-100 p-5 shadow-sm hover:shadow-md">
                    <span class="inline-block rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-700">{{ ucfirst($campus->type) }}</span>
                    <h2 class="mt-2 font-bold text-slate-900">{{ $campus->institution_name }}</h2>
                    @if ($campus->district)
                        <p class="mt-1 text-sm text-slate-500">{{ $campus->district->name }}</p>
                    @endif
                    @if ($campus->event_date)
                        <p class="text-sm text-slate-500">{{ $campus->event_date->translatedFormat('d M, Y') }}</p>
                    @endif
                </a>
            @empty
                <p class="col-span-full text-slate-500">{{ __('Campus programs will be announced soon.') }}</p>
            @endforelse
        </div>
    </section>
@endsection
