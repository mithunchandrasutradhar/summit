<div>
    @if ($enquiry)
        <div class="rounded-xl border border-green-200 bg-green-50 p-8 text-center">
            <h2 class="text-xl font-bold text-green-900">{{ __('Enquiry Submitted!') }}</h2>
            <p class="mt-2 text-sm text-green-800">
                {{ __('Reference number') }}: <span class="font-mono font-semibold">{{ $enquiry->reference_no }}</span>
            </p>
            <p class="mt-1 text-sm text-green-700">{{ __('Our sponsorship team will contact you shortly at :email.', ['email' => $enquiry->email]) }}</p>
        </div>
    @else
        <livewire:dynamic-form-renderer form-key="sponsorship_enquiry" />
    @endif
</div>
