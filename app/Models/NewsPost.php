<?php

namespace App\Models;

use App\Concerns\HasSeoMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class NewsPost extends Model implements HasMedia
{
    use HasFactory, HasSeoMeta, HasSlug, HasTranslations, InteractsWithMedia;

    public array $translatable = ['title', 'excerpt', 'body'];

    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'body',
        'author_id',
        'is_featured',
        'published_at',
        'seo_title',
        'seo_description',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (self $post) => $post->getTranslation('title', 'en'))
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover_image')->singleFile();
    }

    public function coverImageUrl(string $conversion = ''): ?string
    {
        return $this->getFirstMediaUrl('cover_image', $conversion) ?: null;
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
