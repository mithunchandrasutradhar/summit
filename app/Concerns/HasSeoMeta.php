<?php

namespace App\Concerns;

trait HasSeoMeta
{
    /**
     * Resolve the meta title for this record, falling back to a
     * page-specific value (e.g. its own title/name attribute) when
     * no explicit seo_title has been set by an editor.
     */
    public function resolvedSeoTitle(string $fallback): string
    {
        return $this->seo_title ?: $fallback;
    }

    /**
     * Always returns a string (never null) — this is passed directly to
     * single-line `@section('meta_description', ...)` calls, and Blade's
     * startSection() silently reinterprets a literal null as the start of
     * a block-style section (opening an output buffer it never closes),
     * corrupting the rest of the page. See resolvedOgImageUrl() below for
     * the same reasoning.
     */
    public function resolvedSeoDescription(?string $fallback = null): string
    {
        return $this->seo_description ?: ($fallback ?? '');
    }

    /**
     * Always returns a string (never null) — see resolvedSeoDescription()
     * above for why a null return value here would be a real bug, not just
     * a style preference, whenever this is passed to `@section(...)`.
     */
    public function resolvedOgImageUrl(?string $fallback = null): string
    {
        if (! $this->og_image) {
            return $fallback ?? '';
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->og_image);
    }
}
