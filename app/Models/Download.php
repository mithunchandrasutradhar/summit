<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Download extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const CATEGORIES = [
        'brochure',
        'agenda',
        'sponsorship_prospectus',
        'award_guidelines',
        'media_kit',
        'presentation',
        'post_event_report',
    ];

    protected $fillable = [
        'title',
        'category',
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
        $this->addMediaCollection('file')->singleFile();
    }

    public function fileUrl(): ?string
    {
        return $this->getFirstMediaUrl('file') ?: null;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
