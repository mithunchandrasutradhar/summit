<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Top UTM Sources') }}
        </x-slot>

        @php $sources = $this->getSources(); @endphp

        @if (empty($sources))
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No UTM-tagged submissions yet.') }}</p>
        @else
            <ul class="divide-y divide-gray-100 dark:divide-white/10">
                @foreach ($sources as $source => $total)
                    <li class="flex items-center justify-between py-2 text-sm">
                        <span class="font-medium text-gray-950 dark:text-white">{{ $source }}</span>
                        <span class="text-gray-500 dark:text-gray-400">{{ $total }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
