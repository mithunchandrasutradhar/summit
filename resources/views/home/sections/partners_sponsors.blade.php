@php
    $partners = \App\Models\Partner::published()->orderBy('order')->get();
@endphp

@if ($partners->isNotEmpty())
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <h2 class="text-center text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ __('Partners & Sponsors') }}</h2>
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
            <a href="{{ lroute('partners.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">{{ __('View all partners') }} &rarr;</a>
        </div>
    </section>
@endif
