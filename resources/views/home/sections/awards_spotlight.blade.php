@php
    $categories = \App\Models\AwardCategory::active()->forCurrentEvent()->orderBy('order')->take(3)->get();
    $content = $section->content ?? [];
@endphp

@if ($categories->isNotEmpty())
    <section class="relative overflow-hidden bg-ink-950 py-20">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute left-1/2 top-0 h-72 w-[36rem] -translate-x-1/2 rounded-full bg-brand-600/20 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-500/15 text-brand-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-2.723 0" />
                </svg>
            </div>
            <h2 class="mt-6 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                {{ $content['heading'] ?? __('Freelancer Awards') }}
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-ink-300">
                {{ $content['body'] ?? __('Celebrating the freelancers, entrepreneurs and digital professionals shaping Bangladesh\'s digital economy.') }}
            </p>

            <div class="mt-12 grid gap-6 sm:grid-cols-3">
                @foreach ($categories as $category)
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-left backdrop-blur transition hover:border-white/20 hover:bg-white/[0.08]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                        </svg>
                        <h3 class="mt-3 font-bold text-white">{{ $category->name }}</h3>
                        @if ($category->description)
                            <p class="mt-2 text-sm leading-relaxed text-ink-300">{{ Str::limit($category->description, 90) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-12 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ lroute('awards.nominate') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-brand-600 to-brand-500 px-6 py-3 text-sm font-semibold text-white shadow-glow transition hover:scale-[1.02]">
                    {{ __('Nominate for Freelancer Awards') }}
                </a>
                <a href="{{ lroute('awards.categories') }}" class="inline-flex items-center gap-2 rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white transition hover:border-white/40 hover:bg-white/5">
                    {{ __('View Categories') }}
                </a>
            </div>
        </div>
    </section>
@endif
