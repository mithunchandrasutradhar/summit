@php
    $event = \App\Models\Event::current();
    $content = $section->content ?? [];
@endphp

<section class="mx-auto max-w-7xl px-4 py-20 text-center sm:px-6 sm:py-28 lg:px-8">
    @if ($event?->countdown_target_at)
        <p class="text-sm font-semibold uppercase tracking-wide text-brand-600">
            {{ $event->countdown_target_at->translatedFormat('d F Y') }}
            @if ($event->venue_address) &middot; {{ $event->venue_address }} @endif
        </p>
    @endif

    <h1 class="mx-auto mt-4 max-w-4xl text-4xl font-extrabold tracking-tight text-slate-900 sm:text-6xl">
        {{ $content['heading'] ?? __('Freelancer Summit Bangladesh 2026') }}
    </h1>

    <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-600">
        {{ $content['subheading'] ?? __('The national platform for freelancers, aspiring freelancers, digital professionals and the AI-powered digital economy of Bangladesh.') }}
    </p>

    @if ($event?->countdown_target_at && $event->countdown_target_at->isFuture())
        <div
            x-data="{
                target: new Date('{{ $event->countdown_target_at->toIso8601String() }}').getTime(),
                days: 0, hours: 0, minutes: 0, seconds: 0,
                tick() {
                    const diff = Math.max(0, this.target - Date.now());
                    this.days = Math.floor(diff / 86400000);
                    this.hours = Math.floor((diff % 86400000) / 3600000);
                    this.minutes = Math.floor((diff % 3600000) / 60000);
                    this.seconds = Math.floor((diff % 60000) / 1000);
                },
                init() { this.tick(); setInterval(() => this.tick(), 1000); }
            }"
            class="mx-auto mt-10 grid max-w-md grid-cols-4 gap-3"
            aria-label="{{ __('Countdown to the summit') }}"
        >
            <template x-for="[label, value] in [['{{ __('Days') }}', days], ['{{ __('Hours') }}', hours], ['{{ __('Minutes') }}', minutes], ['{{ __('Seconds') }}', seconds]]">
                <div class="rounded-lg bg-slate-50 py-4">
                    <p class="text-2xl font-bold text-slate-900" x-text="String(value).padStart(2, '0')"></p>
                    <p class="text-xs uppercase tracking-wide text-slate-500" x-text="label"></p>
                </div>
            </template>
        </div>
    @endif

    <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
        <a href="#" class="rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            {{ __('Register for Summit') }}
        </a>
        <a href="{{ lroute('news.index') }}" class="rounded-md border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-brand-600 hover:text-brand-700">
            {{ __('Latest News') }}
        </a>
    </div>
</section>
