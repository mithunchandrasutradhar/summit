@php
    $posts = \App\Models\NewsPost::published()->latest('published_at')->take(3)->get();
@endphp

@if ($posts->isNotEmpty())
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between">
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ __('Latest News') }}</h2>
            <a href="{{ lroute('news.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">{{ __('View all') }} &rarr;</a>
        </div>
        <div class="mt-10 grid gap-8 sm:grid-cols-3">
            @foreach ($posts as $post)
                <a href="{{ lroute('news.show', ['slug' => $post->slug]) }}" class="group block overflow-hidden rounded-lg border border-slate-100 shadow-sm hover:shadow-md">
                    @if ($post->coverImageUrl('medium'))
                        <x-responsive-image
                            :thumb="$post->coverImageUrl('thumb')"
                            :medium="$post->coverImageUrl('medium')"
                            sizes="(min-width: 640px) 33vw, 100vw"
                            class="h-40 w-full object-cover"
                        />
                    @else
                        <div class="h-40 w-full bg-brand-50"></div>
                    @endif
                    <div class="p-5">
                        <p class="text-xs text-slate-400">{{ $post->published_at?->format('d M, Y') }}</p>
                        <h3 class="mt-1 font-bold text-slate-900 group-hover:text-brand-700">{{ $post->title }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif
