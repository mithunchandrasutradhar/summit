<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Division extends Model implements HasMedia
{
    use HasFactory, HasSlug, HasTranslations, InteractsWithMedia;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (self $division) => $division->getTranslation('name', 'en'))
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate()
            ->preventOverwrite();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function imageUrl(string $conversion = ''): ?string
    {
        return $this->getFirstMediaUrl('image', $conversion) ?: null;
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
