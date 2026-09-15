@php
    $stats = \App\Models\Event::current()
        ? \App\Models\CampaignStat::where('event_id', \App\Models\Event::current()->id)->pluck('value', 'key')
        : collect();
    $content = $section->content ?? [];
@endphp

<section class="bg-slate-50 py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                {{ $content['heading'] ?? __('The National Journey') }}
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-slate-600">
                {{ $content['body'] ?? __('8 divisions, districts and 100 educational institutions building momentum nationwide before culminating in the Grand Dhaka Summit.') }}
            </p>
        </div>

        @if ($stats->isNotEmpty())
            <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
                @foreach ([
                    'divisions_covered' => __('Divisions Covered'),
                    'districts_covered' => __('Districts Covered'),
                    'institutions_activated' => __('Institutions Activated'),
                    'participants_reached' => __('Participants Reached'),
                ] as $key => $label)
                    <div class="rounded-lg bg-white p-5 text-center shadow-sm">
                        <p class="text-3xl font-extrabold text-brand-700">{{ number_format($stats[$key] ?? 0) }}</p>
                        <p class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-10 text-center">
            <a href="{{ lroute('national-journey.index') }}" class="rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                {{ __('Explore the National Journey') }}
            </a>
        </div>
    </div>
</section>
