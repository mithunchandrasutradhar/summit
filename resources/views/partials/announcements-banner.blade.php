@if ($announcements->isNotEmpty())
    <div class="space-y-2" role="alert" aria-live="polite">
        @foreach ($announcements as $announcement)
            <div class="{{ $announcement->severity === 'urgent' ? 'bg-red-50 text-red-800' : 'bg-brand-50 text-brand-800' }} px-4 py-3 text-sm">
                <div class="mx-auto flex max-w-7xl items-start gap-2 sm:px-6 lg:px-8">
                    <span class="font-semibold">{{ $announcement->title }}:</span>
                    <span>{{ $announcement->message }}</span>
                </div>
            </div>
        @endforeach
    </div>
@endif
