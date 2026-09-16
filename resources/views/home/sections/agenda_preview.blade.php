@php
    $sessions = \App\Models\Session::published()->forCurrentEvent()->orderBy('date')->orderBy('start_time')->take(4)->get();
    $content = $section->content ?? [];

    $typeColors = [
        'keynote' => 'bg-brand-100 text-brand-700',
        'workshop' => 'bg-emerald-100 text-emerald-700',
        'panel' => 'bg-sky-100 text-sky-700',
        'seminar' => 'bg-violet-100 text-violet-700',
    ];
@endphp

@if ($sessions->isNotEmpty())
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-ink-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-ink-600">{{ __('Grand Summit') }}</span>
                    <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-ink-900 sm:text-3xl">{{ $content['heading'] ?? __('Agenda Preview') }}</h2>
                </div>
                <a href="{{ lroute('agenda.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700">
                    {{ __('Explore Agenda') }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>
            </div>
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($sessions as $session)
                    <a href="{{ lroute('agenda.show', ['slug' => $session->slug]) }}" class="group relative block overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide {{ $typeColors[$session->type] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ ucwords(str_replace('_', ' ', $session->type)) }}
                        </span>
                        <h3 class="mt-3 font-bold leading-snug text-ink-900 group-hover:text-brand-700">{{ $session->title }}</h3>
                        @if ($session->date)
                            <p class="mt-3 flex items-center gap-1.5 text-xs font-medium text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ $session->date->translatedFormat('d M') }}
                                @if ($session->start_time) &middot; {{ \Illuminate\Support\Carbon::parse($session->start_time)->format('h:i A') }} @endif
                            </p>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
