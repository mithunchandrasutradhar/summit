<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class SponsorTier extends Model
{
    use HasFactory, HasSlug, HasTranslations;

    public array $translatable = ['name', 'benefits'];

    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'price',
        'benefits',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (self $tier) => $tier->getTranslation('name', 'en'))
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate()
            ->preventOverwrite();
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function enquiries()
    {
        return $this->hasMany(SponsorshipEnquiry::class, 'sponsor_tier_id');
    }

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class, 'tier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCurrentEvent($query)
    {
        return $query->whereHas('event', fn ($q) => $q->where('is_current', true));
    }

    /**
     * Convention used by DynamicFormRenderer's relation_select fields — see
     * AttendeeType::selectableOptions() for why this exists.
     */
    public static function selectableOptions(): Collection
    {
        return static::active()->forCurrentEvent()->orderBy('order')->get();
    }
}
