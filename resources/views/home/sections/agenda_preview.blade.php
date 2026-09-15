@php
    $sessions = \App\Models\Session::published()->forCurrentEvent()->orderBy('date')->orderBy('start_time')->take(4)->get();
@endphp

@if ($sessions->isNotEmpty())
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ __('Agenda Preview') }}</h2>
                <a href="{{ lroute('agenda.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">{{ __('Explore Agenda') }} &rarr;</a>
            </div>
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($sessions as $session)
                    <a href="{{ lroute('agenda.show', ['slug' => $session->slug]) }}" class="block rounded-lg border border-slate-100 bg-white p-4 shadow-sm hover:shadow-md">
                        <span class="text-xs font-semibold uppercase tracking-wide text-brand-600">{{ ucwords(str_replace('_', ' ', $session->type)) }}</span>
                        <h3 class="mt-1 font-semibold text-slate-900">{{ $session->title }}</h3>
                        @if ($session->date)
                            <p class="mt-1 text-xs text-slate-500">
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
