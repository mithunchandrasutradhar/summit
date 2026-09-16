@extends('layouts.app')

@section('title', __('Speakers & Experts').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Speakers & Experts') }}</h1>

        <form method="GET" class="mt-8 grid gap-4 rounded-lg border border-slate-200 bg-slate-50 p-5 sm:grid-cols-3">
            <div>
                <label for="filter-country" class="block text-xs font-medium text-slate-600">{{ __('Country') }}</label>
                <select name="country" id="filter-country" onchange="this.form.submit()" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">{{ __('All countries') }}</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter-expertise" class="block text-xs font-medium text-slate-600">{{ __('Expertise') }}</label>
                <select name="expertise" id="filter-expertise" onchange="this.form.submit()" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">{{ __('All expertise') }}</option>
                    @foreach ($expertiseTags as $tag)
                        <option value="{{ $tag }}" @selected(request('expertise') === $tag)>{{ $tag }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <a href="{{ lroute('speakers.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">{{ __('Clear filters') }}</a>
            </div>
        </form>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($speakers as $speaker)
                <a href="{{ lroute('speakers.show', ['slug' => $speaker->slug]) }}" class="group block overflow-hidden rounded-lg border border-slate-100 shadow-sm hover:shadow-md">
                    @if ($speaker->photoUrl('medium'))
                        <x-responsive-image
                            :thumb="$speaker->photoUrl('thumb')"
                            :medium="$speaker->photoUrl('medium')"
                            sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw"
                            class="h-48 w-full object-cover"
                        />
                    @else
                        <div class="h-48 w-full bg-brand-50"></div>
                    @endif
                    <div class="p-4">
                        @if ($speaker->is_featured)
                            <span class="text-xs font-semibold uppercase tracking-wide text-brand-600">{{ __('Featured') }}</span>
                        @endif
                        <h2 class="mt-1 font-bold text-slate-900 group-hover:text-brand-700">{{ $speaker->name }}</h2>
                        @if ($speaker->designation || $speaker->organization)
                            <p class="mt-1 text-xs text-slate-500">{{ trim(($speaker->designation ?? '').(($speaker->designation && $speaker->organization) ? ', ' : '').($speaker->organization ?? '')) }}</p>
                        @endif
                    </div>
                </a>
            @empty
                <p class="col-span-full text-slate-500">{{ __('Speaker announcements are coming soon.') }}</p>
            @endforelse
        </div>
    </section>
@endsection
