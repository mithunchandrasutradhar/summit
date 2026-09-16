<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $generalSettings = app(\App\Settings\GeneralSettings::class);
        $pageTitle = trim(($__env->yieldContent('title') ?: config('app.name')));
        $pageDescription = trim(($__env->yieldContent('meta_description') ?: 'Freelancer Summit Bangladesh 2026 — the national platform for freelancers, digital professionals and the AI-powered digital economy of Bangladesh.'));
        $ogImageUrl = $__env->yieldContent('og_image')
            ?: ($generalSettings->default_og_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($generalSettings->default_og_image) : null);
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    @php
        $pathWithoutLocale = \Illuminate\Support\Str::after(request()->path(), '/');
        if ($pathWithoutLocale === request()->path()) {
            $pathWithoutLocale = '';
        }
    @endphp
    @foreach (config('app.supported_locales') as $altLocale)
        <link rel="alternate" hreflang="{{ $altLocale }}" href="{{ url('/'.$altLocale.'/'.$pathWithoutLocale) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ url('/'.config('app.locale').'/'.$pathWithoutLocale) }}">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ app()->getLocale() }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    @if ($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="{{ $ogImageUrl ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    @if ($ogImageUrl)
        <meta name="twitter:image" content="{{ $ogImageUrl }}">
    @endif

    <link rel="icon" href="data:,">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('structured_data')

    @if ($generalSettings->gtm_container_id)
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{{ $generalSettings->gtm_container_id }}');
        </script>
    @elseif ($generalSettings->ga_measurement_id)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $generalSettings->ga_measurement_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $generalSettings->ga_measurement_id }}');
        </script>
    @endif
</head>
<body class="min-h-screen bg-white font-sans text-slate-900 antialiased">
    @if ($generalSettings->gtm_container_id)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $generalSettings->gtm_container_id }}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-brand-600 focus:px-4 focus:py-2 focus:text-white">
        {{ __('Skip to content') }}
    </a>

    <header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/90 backdrop-blur-md" x-data="{ mobileOpen: false, openMenu: null }" @keydown.escape="openMenu = null">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ lroute('home') }}" class="group flex items-center gap-2.5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-base font-extrabold text-white shadow-sm">FS</span>
                <span class="leading-tight">
                    <span class="block text-sm font-extrabold tracking-tight text-ink-900 sm:text-base">{{ __('Freelancer Summit') }}</span>
                    <span class="block text-xs font-semibold text-brand-600">{{ __('Bangladesh 2026') }}</span>
                </span>
            </a>

            <nav class="hidden lg:flex lg:items-center lg:gap-1" aria-label="{{ __('Primary') }}">
                <a href="{{ lroute('home') }}" class="rounded-md px-3 py-2 text-sm font-semibold text-ink-700 transition hover:bg-slate-50 hover:text-brand-600">{{ __('Home') }}</a>
                <a href="{{ lroute('pages.show', ['slug' => 'about']) }}" class="rounded-md px-3 py-2 text-sm font-semibold text-ink-700 transition hover:bg-slate-50 hover:text-brand-600">{{ __('About') }}</a>

                @php
                    $navGroups = [
                        'summit' => [
                            'label' => __('Summit'),
                            'links' => [
                                ['route' => 'national-journey.index', 'label' => __('National Journey')],
                                ['route' => 'grand-summit.index', 'label' => __('Grand Summit')],
                                ['route' => 'agenda.index', 'label' => __('Agenda')],
                                ['route' => 'speakers.index', 'label' => __('Speakers')],
                            ],
                        ],
                        'involved' => [
                            'label' => __('Get Involved'),
                            'links' => [
                                ['route' => 'awards.index', 'label' => __('Awards')],
                                ['route' => 'sponsors.index', 'label' => __('Sponsors')],
                                ['route' => 'exhibition.index', 'label' => __('Exhibition')],
                                ['route' => 'forum.index', 'label' => __('BACCO Forum')],
                            ],
                        ],
                        'explore' => [
                            'label' => __('Explore'),
                            'links' => [
                                ['route' => 'news.index', 'label' => __('News & Updates')],
                                ['route' => 'success-stories.index', 'label' => __('Success Stories')],
                                ['route' => 'media.index', 'label' => __('Media Gallery')],
                                ['route' => 'partners.index', 'label' => __('Partners')],
                            ],
                        ],
                    ];
                @endphp

                @foreach ($navGroups as $key => $group)
                    <div class="relative" @click.outside="openMenu === '{{ $key }}' && (openMenu = null)">
                        <button
                            type="button"
                            class="flex items-center gap-1 rounded-md px-3 py-2 text-sm font-semibold text-ink-700 transition hover:bg-slate-50 hover:text-brand-600"
                            @click="openMenu = (openMenu === '{{ $key }}' ? null : '{{ $key }}')"
                            :aria-expanded="(openMenu === '{{ $key }}').toString()"
                        >
                            {{ $group['label'] }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition" :class="openMenu === '{{ $key }}' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <div
                            x-show="openMenu === '{{ $key }}'"
                            x-cloak
                            x-transition.origin.top
                            class="absolute left-0 top-full z-10 mt-1 w-56 overflow-hidden rounded-xl border border-slate-100 bg-white p-1.5 shadow-xl shadow-slate-900/10"
                        >
                            @foreach ($group['links'] as $link)
                                <a href="{{ lroute($link['route']) }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink-700 transition hover:bg-brand-50 hover:text-brand-700">{{ $link['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <div class="hidden items-center gap-1 rounded-full bg-slate-100 p-0.5 text-xs sm:flex" aria-label="{{ __('Language') }}">
                    @foreach (config('app.supported_locales') as $altLocale)
                        <a
                            href="{{ url('/'.$altLocale.'/') }}"
                            class="rounded-full px-2.5 py-1 font-semibold transition {{ app()->getLocale() === $altLocale ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500 hover:text-brand-600' }}"
                            @if(app()->getLocale() === $altLocale) aria-current="true" @endif
                        >{{ strtoupper($altLocale) }}</a>
                    @endforeach
                </div>

                <a href="{{ lroute('register.index') }}" class="hidden items-center gap-1.5 rounded-full bg-gradient-to-r from-brand-600 to-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-brand-600/30 transition hover:shadow-md hover:shadow-brand-600/40 sm:inline-flex">
                    {{ __('Register') }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>

                <button
                    type="button"
                    class="rounded-md p-2 text-ink-700 hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand-600 lg:hidden"
                    @click="mobileOpen = !mobileOpen"
                    :aria-expanded="mobileOpen.toString()"
                    aria-controls="mobile-menu"
                >
                    <span class="sr-only">{{ __('Toggle menu') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <nav
            id="mobile-menu"
            x-show="mobileOpen"
            x-cloak
            x-transition
            class="space-y-4 border-t border-slate-100 px-4 py-4 lg:hidden"
            aria-label="{{ __('Primary') }}"
        >
            <div class="space-y-1">
                <a href="{{ lroute('home') }}" class="block rounded-md px-3 py-2 text-sm font-semibold text-ink-700 hover:bg-slate-50">{{ __('Home') }}</a>
                <a href="{{ lroute('pages.show', ['slug' => 'about']) }}" class="block rounded-md px-3 py-2 text-sm font-semibold text-ink-700 hover:bg-slate-50">{{ __('About') }}</a>
            </div>

            @foreach ($navGroups as $group)
                <div>
                    <p class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400">{{ $group['label'] }}</p>
                    <div class="mt-1 space-y-1">
                        @foreach ($group['links'] as $link)
                            <a href="{{ lroute($link['route']) }}" class="block rounded-md px-3 py-2 text-sm font-medium text-ink-700 hover:bg-slate-50">{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <a href="{{ lroute('register.index') }}" class="block rounded-full bg-gradient-to-r from-brand-600 to-brand-500 px-4 py-3 text-center text-sm font-semibold text-white shadow-sm">{{ __('Register for Summit') }}</a>
        </nav>
    </header>

    <main id="main-content">
        @yield('content')
    </main>

    <footer class="mt-24 bg-ink-950 text-ink-200">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
                <div>
                    <a href="{{ lroute('home') }}" class="flex items-center gap-2.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-base font-extrabold text-white">FS</span>
                        <span class="leading-tight">
                            <span class="block text-sm font-extrabold text-white">{{ __('Freelancer Summit') }}</span>
                            <span class="block text-xs font-semibold text-brand-400">{{ __('Bangladesh 2026') }}</span>
                        </span>
                    </a>
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-ink-300">{{ __('A national campaign for freelancers, digital professionals and the AI-powered digital economy of Bangladesh.') }}</p>
                    <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-ink-400">{{ __('Organized by') }}</p>
                    <p class="mt-1 text-sm text-ink-300">{{ __('Bangladesh Association of Contact Center & Outsourcing (BACCO) / DoICT') }}</p>

                    @php
                        $settings = app(\App\Settings\GeneralSettings::class);
                        $socialIcons = [
                            'social_facebook' => ['label' => 'Facebook', 'path' => 'M13.5 21v-7.5h2.5l.5-3h-3V8.5c0-.87.24-1.46 1.5-1.46H16.5V4.36c-.26-.03-1.15-.11-2.19-.11-2.17 0-3.66 1.32-3.66 3.75V10.5h-2.5v3h2.5V21h2.85Z'],
                            'social_twitter' => ['label' => 'Twitter / X', 'path' => 'M4 4l16 16M20 4 4 20'],
                            'social_linkedin' => ['label' => 'LinkedIn', 'path' => 'M4.5 9h2.75v10.5H4.5V9Zm1.38-4.5a1.63 1.63 0 1 1 0 3.26 1.63 1.63 0 0 1 0-3.26ZM10 9h2.64v1.44h.04c.37-.7 1.27-1.44 2.6-1.44 2.79 0 3.3 1.83 3.3 4.22v5.78h-2.75v-5.13c0-1.22-.02-2.8-1.71-2.8-1.71 0-1.97 1.34-1.97 2.71v5.22H10V9Z'],
                            'social_youtube' => ['label' => 'YouTube', 'path' => 'M21 12s0-3.15-.4-4.64a2.5 2.5 0 0 0-1.76-1.77C17.35 5.2 12 5.2 12 5.2s-5.35 0-6.84.39a2.5 2.5 0 0 0-1.76 1.77C3 8.85 3 12 3 12s0 3.15.4 4.64c.22.82.9 1.46 1.76 1.68C6.65 18.7 12 18.7 12 18.7s5.35 0 6.84-.38a2.5 2.5 0 0 0 1.76-1.68C21 15.15 21 12 21 12ZM10.2 15V9l5.2 3-5.2 3Z'],
                            'social_instagram' => ['label' => 'Instagram', 'path' => 'M12 8.4a3.6 3.6 0 1 0 0 7.2 3.6 3.6 0 0 0 0-7.2ZM12 3.5c-2.31 0-2.6.01-3.51.05-.9.04-1.52.19-2.06.4a4.15 4.15 0 0 0-1.5.98 4.15 4.15 0 0 0-.98 1.5c-.21.54-.36 1.16-.4 2.06C3.51 9.4 3.5 9.69 3.5 12s.01 2.6.05 3.51c.04.9.19 1.52.4 2.06.21.55.5 1.04.98 1.5.46.48.95.77 1.5.98.54.21 1.16.36 2.06.4.91.04 1.2.05 3.51.05s2.6-.01 3.51-.05c.9-.04 1.52-.19 2.06-.4a4.15 4.15 0 0 0 1.5-.98c.48-.46.77-.95.98-1.5.21-.54.36-1.16.4-2.06.04-.91.05-1.2.05-3.51s-.01-2.6-.05-3.51c-.04-.9-.19-1.52-.4-2.06a4.15 4.15 0 0 0-.98-1.5 4.15 4.15 0 0 0-1.5-.98c-.54-.21-1.16-.36-2.06-.4-.91-.04-1.2-.05-3.51-.05Zm0 3.02a5.48 5.48 0 1 1 0 10.96 5.48 5.48 0 0 1 0-10.96Zm4.66-.75a1.28 1.28 0 1 1 0 2.56 1.28 1.28 0 0 1 0-2.56Z'],
                        ];
                    @endphp
                    @if (array_filter(array_map(fn ($f) => $settings->{$f}, array_keys($socialIcons))))
                        <div class="mt-6 flex items-center gap-3">
                            @foreach ($socialIcons as $field => $icon)
                                @if ($settings->{$field})
                                    <a href="{{ $settings->{$field} }}" target="_blank" rel="noopener" aria-label="{{ $icon['label'] }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/5 text-ink-300 transition hover:bg-brand-600 hover:text-white">
                                        @if ($field === 'social_twitter')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" d="{{ $icon['path'] }}" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="{{ $icon['path'] }}" />
                                            </svg>
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <p class="text-sm font-bold text-white">{{ __('Summit') }}</p>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><a href="{{ lroute('national-journey.index') }}" class="text-ink-300 transition hover:text-brand-400">{{ __('National Journey') }}</a></li>
                        <li><a href="{{ lroute('grand-summit.index') }}" class="text-ink-300 transition hover:text-brand-400">{{ __('Grand Summit') }}</a></li>
                        <li><a href="{{ lroute('agenda.index') }}" class="text-ink-300 transition hover:text-brand-400">{{ __('Agenda') }}</a></li>
                        <li><a href="{{ lroute('speakers.index') }}" class="text-ink-300 transition hover:text-brand-400">{{ __('Speakers') }}</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-sm font-bold text-white">{{ __('Explore') }}</p>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><a href="{{ lroute('news.index') }}" class="text-ink-300 transition hover:text-brand-400">{{ __('News & Updates') }}</a></li>
                        <li><a href="{{ lroute('success-stories.index') }}" class="text-ink-300 transition hover:text-brand-400">{{ __('Success Stories') }}</a></li>
                        <li><a href="{{ lroute('media.index') }}" class="text-ink-300 transition hover:text-brand-400">{{ __('Media Gallery') }}</a></li>
                        <li><a href="{{ lroute('partners.index') }}" class="text-ink-300 transition hover:text-brand-400">{{ __('Partners') }}</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-sm font-bold text-white">{{ __('Legal') }}</p>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><a href="{{ lroute('pages.show', ['slug' => 'contact']) }}" class="text-ink-300 transition hover:text-brand-400">{{ __('Contact') }}</a></li>
                        <li><a href="{{ lroute('pages.show', ['slug' => 'privacy-policy']) }}" class="text-ink-300 transition hover:text-brand-400">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ lroute('pages.show', ['slug' => 'terms']) }}" class="text-ink-300 transition hover:text-brand-400">{{ __('Terms') }}</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-14 flex flex-col gap-4 border-t border-white/10 pt-8 text-xs text-ink-400 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ now()->year }} {{ __('Freelancer Summit Bangladesh. All rights reserved.') }}</p>
                <div class="flex items-center gap-1.5">
                    @foreach (config('app.supported_locales') as $altLocale)
                        <a href="{{ url('/'.$altLocale.'/') }}" class="rounded px-2 py-1 font-semibold transition {{ app()->getLocale() === $altLocale ? 'text-brand-400' : 'text-ink-400 hover:text-ink-200' }}">{{ strtoupper($altLocale) }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
