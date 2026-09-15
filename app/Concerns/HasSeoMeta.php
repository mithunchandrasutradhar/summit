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

    public function resolvedSeoDescription(?string $fallback = null): ?string
    {
        return $this->seo_description ?: $fallback;
    }

    public function resolvedOgImageUrl(?string $fallback = null): ?string
    {
        return $this->og_image ?: $fallback;
    }
}
