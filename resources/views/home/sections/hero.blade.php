@php
    $event = \App\Models\Event::current();
    $content = $section->content ?? [];
    $settings = app(\App\Settings\GeneralSettings::class);
@endphp

<section class="relative overflow-hidden bg-ink-950">
    {{-- Decorative glow blobs — purely visual, no layout impact --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -left-24 -top-24 h-96 w-96 rounded-full bg-brand-600/30 blur-3xl"></div>
        <div class="absolute -right-10 top-1/3 h-80 w-80 rounded-full bg-ink-500/40 blur-3xl"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_rgba(255,255,255,0.08),_transparent_60%)]"></div>
        <div class="absolute inset-0 [background-image:linear-gradient(to_right,rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.04)_1px,transparent_1px)] [background-size:56px_56px]"></div>
    </div>

    <div class="relative mx-auto max-w-5xl px-4 py-24 text-center sm:px-6 sm:py-32 lg:px-8">
        @if ($event?->countdown_target_at)
            <p class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-brand-300 backdrop-blur">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6h15a.75.75 0 0 1 .75.75v12a.75.75 0 0 1-.75.75h-15a.75.75 0 0 1-.75-.75v-12A.75.75 0 0 1 4.5 6Z" />
                </svg>
                {{ $event->countdown_target_at->translatedFormat('d F Y') }}
                @if ($event->venue_address) &middot; {{ $event->venue_address }} @endif
            </p>
        @endif

        <h1 class="mx-auto mt-6 max-w-4xl text-4xl font-extrabold tracking-tight text-white sm:text-6xl sm:leading-[1.1]">
            {{ $content['heading'] ?? __('Freelancer Summit Bangladesh 2026') }}
        </h1>

        <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-ink-200">
            {{ $content['subheading'] ?? $settings->site_tagline ?? __('The national platform for freelancers, aspiring freelancers, digital professionals and the AI-powered digital economy of Bangladesh.') }}
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
                class="mx-auto mt-12 grid max-w-lg grid-cols-4 gap-3"
                aria-label="{{ __('Countdown to the summit') }}"
            >
                <template x-for="[label, value] in [['{{ __('Days') }}', days], ['{{ __('Hours') }}', hours], ['{{ __('Minutes') }}', minutes], ['{{ __('Seconds') }}', seconds]]">
                    <div class="rounded-2xl border border-white/10 bg-white/5 py-4 backdrop-blur">
                        <p class="text-2xl font-extrabold text-white sm:text-3xl" x-text="String(value).padStart(2, '0')"></p>
                        <p class="mt-0.5 text-[11px] font-semibold uppercase tracking-wider text-ink-300" x-text="label"></p>
                    </div>
                </template>
            </div>
        @endif

        <div class="mt-12 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ lroute('register.index') }}" class="group inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-brand-600 to-brand-500 px-7 py-3.5 text-sm font-semibold text-white shadow-glow transition hover:scale-[1.02] hover:from-brand-500 hover:to-brand-400">
                {{ __('Register for Summit') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>
            <a href="{{ lroute('news.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/20 px-7 py-3.5 text-sm font-semibold text-white transition hover:border-white/40 hover:bg-white/5">
                {{ __('Latest News') }}
            </a>
        </div>
    </div>

    <div class="relative h-16 bg-gradient-to-b from-transparent to-white"></div>
</section>
