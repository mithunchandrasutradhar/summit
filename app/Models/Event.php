<?php

namespace App\Models;

use App\Concerns\HasSeoMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Event extends Model
{
    use HasFactory, HasSeoMeta, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'date_start',
        'date_end',
        'venue_name',
        'venue_address',
        'map_lat',
        'map_lng',
        'countdown_target_at',
        'description',
        'is_current',
        'seo_title',
        'seo_description',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'date_start' => 'date',
            'date_end' => 'date',
            'countdown_target_at' => 'datetime',
            'is_current' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public static function current(): ?self
    {
        return static::where('is_current', true)->first();
    }
}
