<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaCoverage extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'media_coverage';

    protected $fillable = [
        'title',
        'source_name',
        'url',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('source_logo')->singleFile();
    }

    public function sourceLogoUrl(): ?string
    {
        return $this->getFirstMediaUrl('source_logo') ?: null;
    }
}
