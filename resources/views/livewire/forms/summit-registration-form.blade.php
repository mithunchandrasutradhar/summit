<div>
    @if ($registration)
        <div class="rounded-xl border border-green-200 bg-green-50 p-8 text-center">
            <h2 class="text-xl font-bold text-green-900">{{ __('You\'re registered!') }}</h2>
            <p class="mt-2 text-sm text-green-800">
                {{ __('Reference number') }}: <span class="font-mono font-semibold">{{ $registration->reference_no }}</span>
            </p>
            <p class="mt-1 text-sm text-green-700">{{ __('A confirmation has been sent to :email.', ['email' => $registration->email]) }}</p>

            <div class="mx-auto mt-6 inline-block rounded-lg bg-white p-4 shadow-sm">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate(url('/checkin/'.$registration->qr_token)) !!}
            </div>
            <p class="mt-2 text-xs text-slate-500">{{ __('Present this QR code at the gate for check-in.') }}</p>
        </div>
    @else
        <livewire:dynamic-form-renderer form-key="summit_registration" />
    @endif
</div>
