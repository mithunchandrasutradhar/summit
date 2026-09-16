@extends('layouts.app')

@section('title', __('Media Gallery').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Media Gallery') }}</h1>
        <p class="mt-4 max-w-2xl text-slate-600">{{ __('Photos and videos from across the national campaign — divisional summits, district roadshows, campus programs and the Grand Summit.') }}</p>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($galleries as $gallery)
                <div class="overflow-hidden rounded-lg border border-slate-100 shadow-sm">
                    @if ($gallery->getFirstMedia('photos'))
                        <x-responsive-image
                            :thumb="$gallery->getFirstMediaUrl('photos', 'thumb')"
                            :medium="$gallery->getFirstMediaUrl('photos', 'medium')"
                            sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                            class="h-48 w-full object-cover"
                        />
                    @else
                        <div class="flex h-48 w-full items-center justify-center bg-brand-50 text-brand-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5V6a1.5 1.5 0 011.5-1.5h15A1.5 1.5 0 0121 6v10.5M3 16.5l4.72-4.72a1.5 1.5 0 012.12 0l1.72 1.72m0 0l3-3a1.5 1.5 0 012.12 0L21 15M3 16.5V18a1.5 1.5 0 001.5 1.5h15A1.5 1.5 0 0021 18v-3" />
                            </svg>
                        </div>
                    @endif
                    <div class="p-5">
                        <h2 class="text-lg font-bold text-slate-900">{{ $gallery->title }}</h2>
                        @if ($gallery->related)
                            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-brand-600">{{ $gallery->related->name ?? '' }}</p>
                        @endif
                        @if ($gallery->description)
                            <p class="mt-2 text-sm text-slate-600">{{ Str::limit($gallery->description, 100) }}</p>
                        @endif
                        <p class="mt-3 text-xs text-slate-400">
                            {{ __(':count photos', ['count' => $gallery->getMedia('photos')->count()]) }}
                            @if ($gallery->getMedia('videos')->count())
                                &middot; {{ __(':count videos', ['count' => $gallery->getMedia('videos')->count()]) }}
                            @endif
                        </p>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-slate-500">{{ __('No galleries published yet.') }}</p>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $galleries->links() }}
        </div>

        @if ($coverage->isNotEmpty())
            <div class="mt-20">
                <h2 class="text-xl font-bold text-slate-900">{{ __('As Featured In') }}</h2>
                <div class="mt-6 grid grid-cols-2 gap-8 sm:grid-cols-4">
                    @foreach ($coverage as $item)
                        <a href="{{ $item->url }}" target="_blank" rel="noopener" class="flex flex-col items-center gap-2 text-center">
                            @if ($item->sourceLogoUrl('thumb'))
                                <img src="{{ $item->sourceLogoUrl('thumb') }}" alt="{{ $item->source_name }}" class="max-h-10 w-auto grayscale transition hover:grayscale-0">
                            @else
                                <span class="text-sm font-medium text-slate-700">{{ $item->source_name }}</span>
                            @endif
                            <span class="text-xs text-slate-500">{{ Str::limit($item->title, 40) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
