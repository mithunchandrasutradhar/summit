@php
    $partners = \App\Models\Partner::published()
        ->whereIn('category', ['organizer', 'government'])
        ->orderBy('order')
        ->get();
    $content = $section->content ?? [];
@endphp

@if ($partners->isNotEmpty())
    <section class="border-y border-slate-100 bg-white py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-center text-xs font-bold uppercase tracking-widest text-slate-400">{{ $content['heading'] ?? __('Organized By') }}</p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-x-12 gap-y-6">
                @foreach ($partners as $partner)
                    <a href="{{ $partner->website ?: '#' }}" target="_blank" rel="noopener" class="grayscale transition hover:grayscale-0">
                        @if ($partner->logoUrl('thumb'))
                            <img src="{{ $partner->logoUrl('thumb') }}" alt="{{ $partner->name }}" class="h-10 w-auto">
                        @else
                            <span class="text-sm font-semibold text-slate-600">{{ $partner->name }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
