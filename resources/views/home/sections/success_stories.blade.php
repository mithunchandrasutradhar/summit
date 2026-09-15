@php
    $stories = \App\Models\SuccessStory::published()->where('is_featured', true)->latest('published_at')->take(3)->get();
@endphp

@if ($stories->isNotEmpty())
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ __('Success Stories') }}</h2>
                <a href="{{ lroute('success-stories.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">{{ __('View all') }} &rarr;</a>
            </div>
            <div class="mt-10 grid gap-8 sm:grid-cols-3">
                @foreach ($stories as $story)
                    <a href="{{ lroute('success-stories.show', ['slug' => $story->slug]) }}" class="group block overflow-hidden rounded-lg bg-white shadow-sm hover:shadow-md">
                        @if ($story->photoUrl())
                            <img src="{{ $story->photoUrl() }}" alt="" class="h-48 w-full object-cover">
                        @else
                            <div class="h-48 w-full bg-brand-50"></div>
                        @endif
                        <div class="p-5">
                            <h3 class="font-bold text-slate-900 group-hover:text-brand-700">{{ $story->freelancer_name }}</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ Str::limit($story->headline, 80) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
