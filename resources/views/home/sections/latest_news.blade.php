@php
    $posts = \App\Models\NewsPost::published()->latest('published_at')->take(3)->get();
    $content = $section->content ?? [];
@endphp

@if ($posts->isNotEmpty())
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-ink-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-ink-600">{{ __('Stay Updated') }}</span>
                <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-ink-900 sm:text-3xl">{{ $content['heading'] ?? __('Latest News') }}</h2>
            </div>
            <a href="{{ lroute('news.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700">
                {{ __('View all') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>
        </div>
        <div class="mt-10 grid gap-8 sm:grid-cols-3">
            @foreach ($posts as $post)
                <a href="{{ lroute('news.show', ['slug' => $post->slug]) }}" class="group block overflow-hidden rounded-2xl border border-slate-100 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    @if ($post->coverImageUrl('medium'))
                        <x-responsive-image
                            :thumb="$post->coverImageUrl('thumb')"
                            :medium="$post->coverImageUrl('medium')"
                            sizes="(min-width: 640px) 33vw, 100vw"
                            class="h-40 w-full object-cover"
                        />
                    @else
                        <div class="h-40 w-full bg-gradient-to-br from-ink-100 to-slate-50"></div>
                    @endif
                    <div class="p-5">
                        <span class="inline-flex rounded-full bg-brand-50 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide text-brand-700">
                            {{ $post->category === 'press_release' ? __('Press Release') : __('News') }}
                        </span>
                        <p class="mt-2 text-xs text-slate-400">{{ $post->published_at?->format('d M, Y') }}</p>
                        <h3 class="mt-1 font-bold leading-snug text-ink-900 group-hover:text-brand-700">{{ $post->title }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif
