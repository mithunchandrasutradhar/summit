@php
    $partners = \App\Models\Partner::published()
        ->whereIn('category', ['organizer', 'government'])
        ->orderBy('order')
        ->get();
@endphp

@if ($partners->isNotEmpty())
    <section class="border-y border-slate-100 bg-slate-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-6">
                @foreach ($partners as $partner)
                    <a href="{{ $partner->website ?: '#' }}" target="_blank" rel="noopener" class="grayscale transition hover:grayscale-0">
                        @if ($partner->logoUrl('thumb'))
                            <img src="{{ $partner->logoUrl('thumb') }}" alt="{{ $partner->name }}" class="h-10 w-auto">
                        @else
                            <span class="text-sm font-medium text-slate-700">{{ $partner->name }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
