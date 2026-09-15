<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Partner extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const CATEGORIES = [
        'organizer',
        'government',
        'strategic',
        'technology',
        'knowledge',
        'media',
    ];

    protected $fillable = [
        'name',
        'website',
        'category',
        'order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function logoUrl(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
