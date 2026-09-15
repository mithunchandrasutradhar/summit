@if ($speakers->isNotEmpty())
    <div>
        <h2 class="text-lg font-bold text-slate-900">{{ __('Speakers & Mentors') }}</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($speakers as $speaker)
                <div class="flex items-center gap-3 rounded-lg border border-slate-100 p-3">
                    @if ($speaker->photoUrl())
                        <img src="{{ $speaker->photoUrl() }}" alt="" class="h-12 w-12 rounded-full object-cover">
                    @else
                        <div class="h-12 w-12 rounded-full bg-brand-50"></div>
                    @endif
                    <div>
                        <p class="font-semibold text-slate-900">{{ $speaker->name }}</p>
                        @if ($speaker->designation || $speaker->organization)
                            <p class="text-xs text-slate-500">{{ trim(($speaker->designation ?? '').(($speaker->designation && $speaker->organization) ? ', ' : '').($speaker->organization ?? '')) }}</p>
                        @endif
                        @if (in_array($speaker->pivot->role ?? null, ['mentor', 'moderator', 'panelist']))
                            <span class="text-xs font-medium uppercase tracking-wide text-brand-600">{{ ucfirst($speaker->pivot->role) }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
