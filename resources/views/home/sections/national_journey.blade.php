@php
    $stats = \App\Models\Event::current()
        ? \App\Models\CampaignStat::where('event_id', \App\Models\Event::current()->id)->pluck('value', 'key')
        : collect();
    $content = $section->content ?? [];

    $statMeta = [
        'divisions_covered' => ['label' => __('Divisions Covered'), 'icon' => 'M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006-.001Z'],
        'districts_covered' => ['label' => __('Districts Covered'), 'icon' => 'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z'],
        'institutions_activated' => ['label' => __('Institutions Activated'), 'icon' => 'M12 3 1.5 9l10.5 6 9-5.25M12 3l9 5.25M12 3v6.75M1.5 9v6.75L12 21.75l10.5-6V9'],
        'participants_reached' => ['label' => __('Participants Reached'), 'icon' => 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z'],
    ];
@endphp

<section class="relative bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-ink-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-ink-600">
                {{ __('Nationwide Momentum') }}
            </span>
            <h2 class="mt-5 text-2xl font-extrabold tracking-tight text-ink-900 sm:text-3xl">
                {{ $content['heading'] ?? __('The National Journey') }}
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-slate-600">
                {{ $content['body'] ?? __('8 divisions, districts and 100 educational institutions building momentum nationwide before culminating in the Grand Dhaka Summit.') }}
            </p>
        </div>

        @if ($stats->isNotEmpty())
            <div class="mt-12 grid grid-cols-2 gap-4 sm:grid-cols-4">
                @foreach ($statMeta as $key => $meta)
                    <div class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-6 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-500 to-brand-600 opacity-0 transition group-hover:opacity-100"></div>
                        <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}" />
                            </svg>
                        </div>
                        <p class="mt-4 text-3xl font-extrabold text-ink-900">{{ number_format($stats[$key] ?? 0) }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $meta['label'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-12 text-center">
            <a href="{{ lroute('national-journey.index') }}" class="inline-flex items-center gap-2 rounded-full bg-ink-900 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ink-800">
                {{ __('Explore the National Journey') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>
        </div>
    </div>
</section>
