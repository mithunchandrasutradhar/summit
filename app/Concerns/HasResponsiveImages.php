<?php

namespace App\Concerns;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Generates a thumb/medium/large responsive set, all re-encoded to WebP, for
 * every image collection on the model, so public pages can serve
 * appropriately-sized files instead of originals — this matters most for the
 * campus program/district galleries and the various logo/photo grids, which
 * would otherwise dominate page weight on mobile connections. Non-image
 * files (e.g. a PDF in a mixed collection) are skipped automatically by the
 * media library.
 */
trait HasResponsiveImages
{
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->sharpen(10)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(1200)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(1920)
            ->format('webp')
            ->nonQueued();
    }
}
