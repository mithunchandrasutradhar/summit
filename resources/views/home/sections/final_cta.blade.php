@php $content = $section->content ?? []; @endphp

<section class="relative overflow-hidden bg-ink-950">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -left-16 bottom-0 h-72 w-72 rounded-full bg-brand-600/25 blur-3xl"></div>
        <div class="absolute -right-16 top-0 h-72 w-72 rounded-full bg-ink-500/30 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-4xl px-4 py-20 text-center sm:px-6 lg:px-8">
        <h2 class="text-2xl font-extrabold tracking-tight text-white sm:text-4xl">
            {{ $content['heading'] ?? __('Be part of the national freelancing movement.') }}
        </h2>
        <p class="mx-auto mt-4 max-w-xl text-ink-300">
            {{ __('Free to register — join thousands of freelancers, professionals and entrepreneurs from across Bangladesh.') }}
        </p>
        <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ lroute('register.index') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-brand-600 to-brand-500 px-7 py-3.5 text-sm font-semibold text-white shadow-glow transition hover:scale-[1.02]">
                {{ __('Register for Summit') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>
            <a href="{{ lroute('sponsorship.opportunity') }}" class="inline-flex items-center gap-2 rounded-full border border-white/20 px-7 py-3.5 text-sm font-semibold text-white transition hover:border-white/40 hover:bg-white/5">
                {{ __('Become a Sponsor') }}
            </a>
        </div>
    </div>
</section>
