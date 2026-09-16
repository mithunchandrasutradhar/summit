@php
    $photos = $galleries->flatMap(fn ($gallery) => $gallery->getMedia('photos'));
@endphp

@if ($photos->isNotEmpty())
    <div>
        <h2 class="text-lg font-bold text-slate-900">{{ __('Photo Gallery') }}</h2>
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach ($photos->take(8) as $photo)
                <x-responsive-image
                    :thumb="$photo->getUrl('thumb')"
                    :medium="$photo->getUrl('medium')"
                    sizes="(min-width: 640px) 25vw, 50vw"
                    class="h-32 w-full rounded-lg object-cover"
                />
            @endforeach
        </div>
    </div>
@endif
