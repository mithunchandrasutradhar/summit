@extends('layouts.app')

@section('title', __('Freelancer Summit Bangladesh 2026'))

@section('content')
    @foreach ($sections as $section)
        @includeIf('home.sections.'.$section->section_key, ['section' => $section])
    @endforeach

    @php $socialWallEmbed = app(\App\Settings\GeneralSettings::class)->social_wall_embed_code; @endphp
    @if ($socialWallEmbed)
        <section class="mx-auto max-w-5xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="text-center">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-700">{{ __('Join the Conversation') }}</span>
                <h2 class="mt-4 text-2xl font-extrabold tracking-tight text-ink-900 sm:text-3xl">{{ __('On Social Media') }}</h2>
            </div>
            <div class="mt-10">
                {!! $socialWallEmbed !!}
            </div>
        </section>
    @endif
@endsection
