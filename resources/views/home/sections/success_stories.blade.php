@php
    $stories = \App\Models\SuccessStory::published()->where('is_featured', true)->latest('published_at')->take(3)->get();
    $content = $section->content ?? [];
@endphp

@if ($stories->isNotEmpty())
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-700">{{ __('Real Stories') }}</span>
                    <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-ink-900 sm:text-3xl">{{ $content['heading'] ?? __('Success Stories') }}</h2>
                </div>
                <a href="{{ lroute('success-stories.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700">
                    {{ __('View all') }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>
            </div>
            <div class="mt-10 grid gap-8 sm:grid-cols-3">
                @foreach ($stories as $story)
                    <a href="{{ lroute('success-stories.show', ['slug' => $story->slug]) }}" class="group block overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="relative">
                            @if ($story->photoUrl('medium'))
                                <x-responsive-image
                                    :thumb="$story->photoUrl('thumb')"
                                    :medium="$story->photoUrl('medium')"
                                    sizes="(min-width: 640px) 33vw, 100vw"
                                    class="h-48 w-full object-cover"
                                />
                            @else
                                <div class="h-48 w-full bg-gradient-to-br from-brand-100 to-brand-50"></div>
                            @endif
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute bottom-3 right-3 h-8 w-8 text-white/80 drop-shadow" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4V3h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983V3h9.983z"/>
                            </svg>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-ink-900 group-hover:text-brand-700">{{ $story->freelancer_name }}</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ Str::limit($story->headline, 80) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
