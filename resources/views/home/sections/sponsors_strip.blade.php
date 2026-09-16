@php
    $sponsors = \App\Models\Sponsor::published()->orderBy('order')->take(8)->get();
    $content = $section->content ?? [];
@endphp

@if ($sponsors->isNotEmpty())
    <section class="bg-white py-16">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                {{ $content['heading'] ?? __('Our Sponsors') }}
            </h2>

            <div class="mt-10 grid grid-cols-2 items-center gap-6 sm:grid-cols-4 lg:grid-cols-8">
                @foreach ($sponsors as $sponsor)
                    <div class="flex items-center justify-center rounded-lg border border-slate-100 bg-white p-4">
                        @if ($sponsor->logoUrl('thumb'))
                            <img src="{{ $sponsor->logoUrl('thumb') }}" alt="{{ $sponsor->name }}" class="max-h-12 w-auto">
                        @else
                            <span class="text-xs font-semibold text-slate-600">{{ $sponsor->name }}</span>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                <a href="{{ lroute('sponsorship.opportunity') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">
                    {{ __('Become a Sponsor') }} &rarr;
                </a>
            </div>
        </div>
    </section>
@endif
