@extends('layouts.app')

@section('title', $post->resolvedSeoTitle($post->title).' — '.config('app.name'))
@section('meta_description', $post->resolvedSeoDescription($post->excerpt))
@section('og_image', $post->resolvedOgImageUrl($post->coverImageUrl('medium')))

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <a href="{{ lroute('news.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">&larr; {{ __('Back to News') }}</a>

        <span class="mt-6 block text-xs font-semibold uppercase tracking-wide text-brand-600">
            {{ $post->category === 'press_release' ? __('Press Release') : __('News') }}
        </span>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $post->title }}</h1>
        <p class="mt-3 text-sm text-slate-400">{{ $post->published_at?->format('d M, Y') }}</p>

        @if ($post->coverImageUrl('medium'))
            <x-responsive-image
                :medium="$post->coverImageUrl('medium')"
                :large="$post->coverImageUrl('large')"
                sizes="(min-width: 1024px) 800px, 100vw"
                class="mt-8 w-full rounded-lg object-cover"
            />
        @endif

        <div class="prose prose-slate mt-8 max-w-none">
            {!! $post->body !!}
        </div>
    </article>
@endsection
