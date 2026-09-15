{{--
    Shared local-activity lead capture form, embedded on district and campus
    program pages. $action must be the full submission URL; $ambassador (bool)
    toggles whether the "I want to be a Campus Ambassador" option is shown.
--}}
@php $ambassador ??= false; @endphp

<div class="rounded-xl border border-slate-200 bg-slate-50 p-6 sm:p-8" id="register-interest">
    <h2 class="text-xl font-bold text-slate-900">{{ __('Register Your Interest') }}</h2>
    <p class="mt-2 text-sm text-slate-600">{{ __('Let us know you\'re coming — we\'ll keep you updated on the schedule and details.') }}</p>

    @if (session('lead_submitted'))
        <div class="mt-4 rounded-md bg-green-50 p-4 text-sm text-green-800" role="status">
            {{ __('Thanks! We\'ve received your details and will be in touch.') }}
        </div>
    @else
        <form method="POST" action="{{ $action }}" class="mt-6 grid gap-4 sm:grid-cols-2">
            @csrf
            <div class="sm:col-span-2">
                <label for="lead_name" class="block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
                <input type="text" name="name" id="lead_name" required value="{{ old('name') }}"
                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="lead_email" class="block text-sm font-medium text-slate-700">{{ __('Email') }}</label>
                <input type="email" name="email" id="lead_email" required value="{{ old('email') }}"
                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="lead_phone" class="block text-sm font-medium text-slate-700">{{ __('Phone') }}</label>
                <input type="text" name="phone" id="lead_phone" value="{{ old('phone') }}"
                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            @if ($ambassador)
                <div class="sm:col-span-2">
                    <label for="lead_type" class="block text-sm font-medium text-slate-700">{{ __('I want to...') }}</label>
                    <select name="type" id="lead_type" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="attendee_interest">{{ __('Attend this program') }}</option>
                        <option value="campus_ambassador">{{ __('Become a Campus Ambassador') }}</option>
                    </select>
                </div>
            @else
                <input type="hidden" name="type" value="attendee_interest">
            @endif

            <div class="sm:col-span-2">
                <label for="lead_message" class="block text-sm font-medium text-slate-700">{{ __('Message (optional)') }}</label>
                <textarea name="message" id="lead_message" rows="3"
                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('message') }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <button type="submit" class="rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    {{ __('Submit') }}
                </button>
            </div>
        </form>
    @endif
</div>
