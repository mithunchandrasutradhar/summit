<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-ink-900 via-ink-900 to-primary-900 p-6 shadow-sm sm:p-8">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -right-10 -top-10 h-56 w-56 rounded-full bg-primary-500/20 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary-300">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
                <h2 class="mt-1 text-xl font-extrabold text-white sm:text-2xl">
                    {{ __('Welcome back, :name', ['name' => explode(' ', auth()->user()->name)[0] ?? auth()->user()->name]) }}
                </h2>
                <p class="mt-1 text-sm text-ink-300">
                    {{ __('Here\'s what\'s happening across Freelancer Summit Bangladesh 2026.') }}
                </p>
            </div>

            @php $pending = $this->getPendingReviewCount(); @endphp
            <div class="flex items-center gap-2 self-start rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white backdrop-blur sm:self-auto">
                <span class="flex h-2 w-2 rounded-full {{ $pending > 0 ? 'bg-amber-400' : 'bg-emerald-400' }}"></span>
                {{ trans_choice(':count item awaiting review|:count items awaiting review', $pending, ['count' => $pending]) }}
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
