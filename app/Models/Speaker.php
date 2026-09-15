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

class Speaker extends Model implements HasMedia
{
    use HasFactory, HasSeoMeta, HasSlug, HasTranslations, InteractsWithMedia;

    public array $translatable = ['bio'];

    protected $fillable = [
        'name',
        'slug',
        'designation',
        'organization',
        'country',
        'bio',
        'expertise',
        'social_links',
        'is_featured',
        'is_published',
        'order',
        'seo_title',
        'seo_description',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'expertise' => 'array',
            'social_links' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate()
            ->preventOverwrite();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function photoUrl(string $conversion = ''): ?string
    {
        return $this->getFirstMediaUrl('photo', $conversion) ?: null;
    }

    public function districts()
    {
        return $this->morphedByMany(District::class, 'activity', 'activity_speaker')->withPivot('role');
    }

    public function campusPrograms()
    {
        return $this->morphedByMany(CampusProgram::class, 'activity', 'activity_speaker')->withPivot('role');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
