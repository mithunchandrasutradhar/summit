<?php

namespace App\Models;

use App\Concerns\HasSeoMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class AwardCategory extends Model
{
    use HasFactory, HasSeoMeta, HasSlug, HasTranslations;

    public array $translatable = ['name', 'description', 'eligibility', 'criteria'];

    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'description',
        'eligibility',
        'criteria',
        'submission_deadline',
        'is_active',
        'order',
        'seo_title',
        'seo_description',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'submission_deadline' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (self $category) => $category->getTranslation('name', 'en'))
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate()
            ->preventOverwrite();
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function nominations()
    {
        return $this->hasMany(AwardNomination::class, 'category_id');
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
    public static function selectableOptions(): \Illuminate\Support\Collection
    {
        return static::active()->forCurrentEvent()->orderBy('order')->get();
    }
}
