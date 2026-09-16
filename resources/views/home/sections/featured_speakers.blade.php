@php
    $speakers = \App\Models\Speaker::published()->where('is_featured', true)->orderBy('order')->take(8)->get();
@endphp

@if ($speakers->isNotEmpty())
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between">
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ __('Featured Speakers') }}</h2>
            <a href="{{ lroute('speakers.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">{{ __('View all') }} &rarr;</a>
        </div>
        <div class="mt-10 grid grid-cols-2 gap-6 sm:grid-cols-4">
            @foreach ($speakers as $speaker)
                <a href="{{ lroute('speakers.show', ['slug' => $speaker->slug]) }}" class="group block text-center">
                    @if ($speaker->photoUrl('thumb'))
                        <img src="{{ $speaker->photoUrl('thumb') }}" alt="" class="mx-auto h-28 w-28 rounded-full object-cover">
                    @else
                        <div class="mx-auto h-28 w-28 rounded-full bg-brand-50"></div>
                    @endif
                    <p class="mt-3 font-semibold text-slate-900 group-hover:text-brand-700">{{ $speaker->name }}</p>
                    @if ($speaker->organization)
                        <p class="text-xs text-slate-500">{{ $speaker->organization }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    </section>
@endif
