@php
    $partners = \App\Models\Partner::published()->orderBy('order')->get();
    $content = $section->content ?? [];
@endphp

@if ($partners->isNotEmpty())
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-ink-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-ink-600">{{ __('Our Ecosystem') }}</span>
            <h2 class="mt-4 text-2xl font-extrabold tracking-tight text-ink-900 sm:text-3xl">{{ $content['heading'] ?? __('Partners & Sponsors') }}</h2>
        </div>
        <div class="mt-10 flex flex-wrap items-center justify-center gap-x-10 gap-y-6">
            @foreach ($partners as $partner)
                <a href="{{ $partner->website ?: '#' }}" target="_blank" rel="noopener" class="grayscale transition hover:grayscale-0">
                    @if ($partner->logoUrl('thumb'))
                        <img src="{{ $partner->logoUrl('thumb') }}" alt="{{ $partner->name }}" class="h-12 w-auto">
                    @else
                        <span class="text-sm font-medium text-slate-700">{{ $partner->name }}</span>
                    @endif
                </a>
            @endforeach
        </div>
        <div class="mt-8 text-center">
            <a href="{{ lroute('partners.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700">
                {{ __('View all partners') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>
        </div>
    </section>
@endif
