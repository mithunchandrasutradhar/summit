<?php

namespace App\Models;

use App\Concerns\HasSeoMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasFactory, HasSeoMeta, HasSlug, HasTranslations;

    public array $translatable = ['title', 'body'];

    protected $fillable = [
        'title',
        'slug',
        'body',
        'is_published',
        'seo_title',
        'seo_description',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (self $page) => $page->getTranslation('title', 'en'))
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate()
            ->preventOverwrite(); // an explicitly-set slug (e.g. the fixed /privacy-policy, /terms URLs) is respected rather than re-derived from the title
    }
}
