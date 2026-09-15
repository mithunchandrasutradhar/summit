<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', 'Freelancer Summit Bangladesh 2026 — the national platform for freelancers, digital professionals and the AI-powered digital economy of Bangladesh.')">

    @php
        $pathWithoutLocale = \Illuminate\Support\Str::after(request()->path(), '/');
        if ($pathWithoutLocale === request()->path()) {
            $pathWithoutLocale = '';
        }
    @endphp
    @foreach (config('app.supported_locales') as $altLocale)
        <link rel="alternate" hreflang="{{ $altLocale }}" href="{{ url('/'.$altLocale.'/'.$pathWithoutLocale) }}">
    @endforeach

    <link rel="icon" href="data:,">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-slate-900 antialiased">

    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-brand-600 focus:px-4 focus:py-2 focus:text-white">
        {{ __('Skip to content') }}
    </a>

    <header class="border-b border-slate-100 bg-white/95 backdrop-blur sticky top-0 z-40" x-data="{ mobileOpen: false }">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ lroute('home') }}" class="flex items-center gap-2 font-bold text-brand-700">
                <span class="text-lg leading-tight">
                    {{ __('Freelancer Summit') }}<br class="hidden sm:block">
                    <span class="text-sm font-medium text-slate-500">{{ __('Bangladesh 2026') }}</span>
                </span>
            </a>

            <nav class="hidden lg:flex lg:items-center lg:gap-6" aria-label="{{ __('Primary') }}">
                <a href="{{ lroute('home') }}" class="text-sm font-medium text-slate-700 hover:text-brand-600">{{ __('Home') }}</a>
                <a href="{{ lroute('pages.show', ['slug' => 'about']) }}" class="text-sm font-medium text-slate-700 hover:text-brand-600">{{ __('About') }}</a>
                <a href="{{ lroute('national-journey.index') }}" class="text-sm font-medium text-slate-700 hover:text-brand-600">{{ __('National Journey') }}</a>
                <a href="{{ lroute('grand-summit.index') }}" class="text-sm font-medium text-slate-700 hover:text-brand-600">{{ __('Grand Summit') }}</a>
                <a href="{{ lroute('agenda.index') }}" class="text-sm font-medium text-slate-700 hover:text-brand-600">{{ __('Agenda') }}</a>
                <a href="{{ lroute('speakers.index') }}" class="text-sm font-medium text-slate-700 hover:text-brand-600">{{ __('Speakers') }}</a>
                <a href="{{ lroute('news.index') }}" class="text-sm font-medium text-slate-700 hover:text-brand-600">{{ __('News') }}</a>
                <a href="{{ lroute('success-stories.index') }}" class="text-sm font-medium text-slate-700 hover:text-brand-600">{{ __('Success Stories') }}</a>
                <a href="{{ lroute('media.index') }}" class="text-sm font-medium text-slate-700 hover:text-brand-600">{{ __('Media') }}</a>
                <a href="{{ lroute('partners.index') }}" class="text-sm font-medium text-slate-700 hover:text-brand-600">{{ __('Partners') }}</a>
            </nav>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-1 text-sm" aria-label="{{ __('Language') }}">
                    @foreach (config('app.supported_locales') as $altLocale)
                        <a
                            href="{{ url('/'.$altLocale.'/') }}"
                            class="rounded px-2 py-1 {{ app()->getLocale() === $altLocale ? 'bg-brand-50 font-semibold text-brand-700' : 'text-slate-500 hover:text-brand-600' }}"
                            @if(app()->getLocale() === $altLocale) aria-current="true" @endif
                        >{{ strtoupper($altLocale) }}</a>
                    @endforeach
                </div>

                <a href="#" class="hidden rounded-md bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 sm:inline-block">
                    {{ __('Register for Summit') }}
                </a>

                <button
                    type="button"
                    class="lg:hidden rounded-md p-2 text-slate-700 hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand-600"
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
            class="lg:hidden border-t border-slate-100 px-4 py-3 space-y-2"
            aria-label="{{ __('Primary') }}"
        >
            <a href="{{ lroute('home') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">{{ __('Home') }}</a>
            <a href="{{ lroute('pages.show', ['slug' => 'about']) }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">{{ __('About') }}</a>
            <a href="{{ lroute('national-journey.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">{{ __('National Journey') }}</a>
            <a href="{{ lroute('grand-summit.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">{{ __('Grand Summit') }}</a>
            <a href="{{ lroute('agenda.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">{{ __('Agenda') }}</a>
            <a href="{{ lroute('speakers.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">{{ __('Speakers') }}</a>
            <a href="{{ lroute('news.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">{{ __('News') }}</a>
            <a href="{{ lroute('success-stories.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">{{ __('Success Stories') }}</a>
            <a href="{{ lroute('media.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">{{ __('Media') }}</a>
            <a href="{{ lroute('partners.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">{{ __('Partners') }}</a>
            <a href="#" class="block rounded-md bg-brand-600 px-3 py-2 text-center text-sm font-semibold text-white">{{ __('Register for Summit') }}</a>
        </nav>
    </header>

    <main id="main-content">
        @yield('content')
    </main>

    <footer class="mt-20 border-t border-slate-100 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p class="font-bold text-brand-700">{{ __('Freelancer Summit Bangladesh 2026') }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ __('A national campaign for freelancers, digital professionals and the AI-powered digital economy of Bangladesh.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ __('Organizer') }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ __('Bangladesh Association of Contact Center & Outsourcing (BACCO) / DoICT') }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ __('Explore') }}</p>
                    <ul class="mt-2 space-y-1 text-sm text-slate-600">
                        <li><a href="{{ lroute('news.index') }}" class="hover:text-brand-700">{{ __('News & Updates') }}</a></li>
                        <li><a href="{{ lroute('success-stories.index') }}" class="hover:text-brand-700">{{ __('Success Stories') }}</a></li>
                        <li><a href="{{ lroute('media.index') }}" class="hover:text-brand-700">{{ __('Media Gallery') }}</a></li>
                        <li><a href="{{ lroute('partners.index') }}" class="hover:text-brand-700">{{ __('Partners') }}</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ __('Legal') }}</p>
                    <ul class="mt-2 space-y-1 text-sm text-slate-600">
                        <li><a href="{{ lroute('pages.show', ['slug' => 'contact']) }}" class="hover:text-brand-700">{{ __('Contact') }}</a></li>
                        <li><a href="{{ lroute('pages.show', ['slug' => 'privacy-policy']) }}" class="hover:text-brand-700">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ lroute('pages.show', ['slug' => 'terms']) }}" class="hover:text-brand-700">{{ __('Terms') }}</a></li>
                    </ul>
                </div>
            </div>
            <p class="mt-10 border-t border-slate-200 pt-6 text-xs text-slate-500">
                &copy; {{ now()->year }} {{ __('Freelancer Summit Bangladesh. All rights reserved.') }}
            </p>
        </div>
    </footer>

</body>
</html>
