@props(['thumb' => null, 'medium' => null, 'large' => null, 'sizes' => '100vw', 'alt' => ''])

@php
    $srcset = collect([
        $thumb ? "{$thumb} 400w" : null,
        $medium ? "{$medium} 1200w" : null,
        $large ? "{$large} 1920w" : null,
    ])->filter()->implode(', ');

    $fallback = $medium ?? $large ?? $thumb;
@endphp

@if ($fallback)
    <img
        src="{{ $fallback }}"
        @if ($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
        alt="{{ $alt }}"
        loading="lazy"
        {{ $attributes }}
    >
@endif
