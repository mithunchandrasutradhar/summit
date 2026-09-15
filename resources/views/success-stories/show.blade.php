@extends('layouts.app')

@section('title', $story->freelancer_name.' — '.config('app.name'))

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <a href="{{ lroute('success-stories.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; {{ __('Back to Success Stories') }}</a>

        <div class="mt-6 flex items-center gap-4">
            @if ($story->photoUrl())
                <img src="{{ $story->photoUrl() }}" alt="" class="h-16 w-16 rounded-full object-cover">
            @endif
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">{{ $story->freelancer_name }}</h1>
                @if ($story->marketplace)
                    <p class="text-sm font-semibold uppercase tracking-wide text-brand-600">{{ $story->marketplace }}</p>
                @endif
            </div>
        </div>

        <h2 class="mt-8 text-xl font-bold text-slate-900">{{ $story->headline }}</h2>

        <div class="prose prose-slate mt-4 max-w-none">
            {!! nl2br(e($story->story)) !!}
        </div>

        @if ($story->video_url)
            <div class="mt-8 aspect-video overflow-hidden rounded-lg">
                <iframe src="{{ $story->video_url }}" class="h-full w-full" allowfullscreen loading="lazy"></iframe>
            </div>
        @endif
    </article>
@endsection
