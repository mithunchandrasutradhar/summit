@php
    $sponsors = \App\Models\Sponsor::published()->orderBy('order')->take(8)->get();
    $content = $section->content ?? [];
@endphp

@if ($sponsors->isNotEmpty())
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-500 shadow-sm">{{ __('Backed By') }}</span>
            <h2 class="mt-4 text-2xl font-extrabold tracking-tight text-ink-900 sm:text-3xl">
                {{ $content['heading'] ?? __('Our Sponsors') }}
            </h2>

            <div class="mt-10 grid grid-cols-2 items-stretch gap-4 sm:grid-cols-4 lg:grid-cols-8">
                @foreach ($sponsors as $sponsor)
                    <div class="flex items-center justify-center rounded-xl border border-slate-100 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        @if ($sponsor->logoUrl('thumb'))
                            <img src="{{ $sponsor->logoUrl('thumb') }}" alt="{{ $sponsor->name }}" class="max-h-12 w-auto">
                        @else
                            <span class="text-xs font-semibold text-slate-600">{{ $sponsor->name }}</span>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                <a href="{{ lroute('sponsorship.opportunity') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700">
                    {{ __('Become a Sponsor') }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
@endif
