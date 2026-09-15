@extends('layouts.app')

@section('title', $page->resolvedSeoTitle($page->title).' — '.config('app.name'))
@section('meta_description', $page->resolvedSeoDescription())

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $page->title }}</h1>
        <div class="prose prose-slate mt-8 max-w-none">
            {!! $page->body !!}
        </div>
    </article>
@endsection
