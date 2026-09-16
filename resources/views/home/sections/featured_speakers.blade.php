@php
    $speakers = \App\Models\Speaker::published()->where('is_featured', true)->orderBy('order')->take(8)->get();
    $content = $section->content ?? [];
@endphp

@if ($speakers->isNotEmpty())
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-700">{{ __('Meet the Lineup') }}</span>
                <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-ink-900 sm:text-3xl">{{ $content['heading'] ?? __('Featured Speakers') }}</h2>
            </div>
            <a href="{{ lroute('speakers.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700">
                {{ __('View all') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>
        </div>
        <div class="mt-10 grid grid-cols-2 gap-6 sm:grid-cols-4">
            @foreach ($speakers as $speaker)
                <a href="{{ lroute('speakers.show', ['slug' => $speaker->slug]) }}" class="group block text-center">
                    <div class="relative mx-auto h-28 w-28 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 p-[3px] transition group-hover:from-brand-500 group-hover:to-brand-700">
                        <div class="h-full w-full overflow-hidden rounded-full bg-white p-1">
                            @if ($speaker->photoUrl('thumb'))
                                <img src="{{ $speaker->photoUrl('thumb') }}" alt="" class="h-full w-full rounded-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center rounded-full bg-brand-50 text-lg font-bold text-brand-400">{{ Str::of($speaker->name)->substr(0, 1) }}</div>
                            @endif
                        </div>
                    </div>
                    <p class="mt-4 font-bold text-ink-900 group-hover:text-brand-700">{{ $speaker->name }}</p>
                    @if ($speaker->organization)
                        <p class="text-xs text-slate-500">{{ $speaker->organization }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    </section>
@endif
