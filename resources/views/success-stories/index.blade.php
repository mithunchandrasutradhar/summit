@extends('layouts.app')

@section('title', __('Success Stories').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('Success Stories') }}</h1>
        <p class="mt-4 max-w-2xl text-slate-600">{{ __('Meet the Bangladeshi freelancers and entrepreneurs building careers in the global digital economy.') }}</p>

        <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($stories as $story)
                <a href="{{ lroute('success-stories.show', ['slug' => $story->slug]) }}" class="group block overflow-hidden rounded-lg border border-slate-100 shadow-sm hover:shadow-md">
                    @if ($story->photoUrl('medium'))
                        <x-responsive-image
                            :thumb="$story->photoUrl('thumb')"
                            :medium="$story->photoUrl('medium')"
                            sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                            class="h-48 w-full object-cover"
                        />
                    @else
                        <div class="h-48 w-full bg-brand-50"></div>
                    @endif
                    <div class="p-5">
                        <h2 class="text-lg font-bold text-slate-900 group-hover:text-brand-700">{{ $story->freelancer_name }}</h2>
                        @if ($story->marketplace)
                            <p class="text-xs font-semibold uppercase tracking-wide text-brand-600">{{ $story->marketplace }}</p>
                        @endif
                        <p class="mt-2 text-sm text-slate-600">{{ Str::limit($story->headline, 100) }}</p>
                    </div>
                </a>
            @empty
                <p class="col-span-full text-slate-500">{{ __('No success stories published yet.') }}</p>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $stories->links() }}
        </div>
    </section>
@endsection
