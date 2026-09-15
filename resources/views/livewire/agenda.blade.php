<div>
    <div class="grid gap-4 rounded-lg border border-slate-200 bg-slate-50 p-5 sm:grid-cols-4" role="group" aria-label="{{ __('Agenda filters') }}">
        <div>
            <label for="agenda-date" class="block text-xs font-medium text-slate-600">{{ __('Date') }}</label>
            <select wire:model.live="date" id="agenda-date" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">{{ __('All dates') }}</option>
                @foreach ($availableDates as $availableDate)
                    <option value="{{ $availableDate->toDateString() }}">{{ $availableDate->translatedFormat('d M, Y') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="agenda-type" class="block text-xs font-medium text-slate-600">{{ __('Session Type') }}</label>
            <select wire:model.live="type" id="agenda-type" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">{{ __('All types') }}</option>
                @foreach ($types as $typeOption)
                    <option value="{{ $typeOption }}">{{ ucwords(str_replace('_', ' ', $typeOption)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="agenda-track" class="block text-xs font-medium text-slate-600">{{ __('Track') }}</label>
            <select wire:model.live="track" id="agenda-track" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">{{ __('All tracks') }}</option>
                @foreach ($availableTracks as $availableTrack)
                    <option value="{{ $availableTrack }}">{{ $availableTrack }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="button" wire:click="resetFilters" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                {{ __('Clear filters') }}
            </button>
        </div>
    </div>

    <div class="mt-10 space-y-12" wire:loading.class="opacity-50">
        @forelse ($sessionsByDate as $date => $sessions)
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    {{ $date ? \Illuminate\Support\Carbon::parse($date)->translatedFormat('l, d F Y') : __('Date TBA') }}
                </h2>
                <div class="mt-4 space-y-4">
                    @foreach ($sessions as $session)
                        <a href="{{ lroute('agenda.show', ['slug' => $session->slug]) }}" class="block rounded-lg border border-slate-100 p-5 shadow-sm hover:shadow-md">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-700">
                                    {{ ucwords(str_replace('_', ' ', $session->type)) }}
                                </span>
                                @if ($session->start_time)
                                    <span class="text-sm text-slate-500">
                                        {{ \Illuminate\Support\Carbon::parse($session->start_time)->format('h:i A') }}
                                        @if ($session->end_time) &ndash; {{ \Illuminate\Support\Carbon::parse($session->end_time)->format('h:i A') }} @endif
                                    </span>
                                @endif
                            </div>
                            <h3 class="mt-2 font-bold text-slate-900">{{ $session->title }}</h3>
                            <div class="mt-1 flex flex-wrap gap-x-4 text-sm text-slate-500">
                                @if ($session->hall) <span>{{ $session->hall->name }}</span> @endif
                                @if ($session->track) <span>{{ __('Track') }}: {{ $session->track }}</span> @endif
                                @if ($session->speakers->isNotEmpty())
                                    <span>{{ $session->speakers->pluck('name')->join(', ') }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-slate-500">{{ __('The agenda will be published soon.') }}</p>
        @endforelse
    </div>
</div>
