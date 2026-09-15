@extends('layouts.app')

@section('title', __('News & Updates').' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('News & Updates') }}</h1>

        <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($posts as $post)
                <a href="{{ lroute('news.show', ['slug' => $post->slug]) }}" class="group block overflow-hidden rounded-lg border border-slate-100 shadow-sm hover:shadow-md">
                    @if ($post->coverImageUrl())
                        <img src="{{ $post->coverImageUrl() }}" alt="" class="h-48 w-full object-cover">
                    @else
                        <div class="h-48 w-full bg-brand-50"></div>
                    @endif
                    <div class="p-5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-brand-600">
                            {{ $post->category === 'press_release' ? __('Press Release') : __('News') }}
                        </span>
                        <h2 class="mt-2 text-lg font-bold text-slate-900 group-hover:text-brand-700">{{ $post->title }}</h2>
                        @if ($post->excerpt)
                            <p class="mt-2 text-sm text-slate-600">{{ Str::limit($post->excerpt, 120) }}</p>
                        @endif
                        <p class="mt-4 text-xs text-slate-400">{{ $post->published_at?->format('d M, Y') }}</p>
                    </div>
                </a>
            @empty
                <p class="col-span-full text-slate-500">{{ __('No news posted yet — check back soon.') }}</p>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    </section>
@endsection
