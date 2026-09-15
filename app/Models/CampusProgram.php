<?php

namespace App\Models;

use App\Concerns\HasSeoMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class CampusProgram extends Model
{
    use HasFactory, HasSeoMeta, HasSlug, HasTranslations;

    public const TYPES = ['university', 'polytechnic'];

    public const STATUSES = ['upcoming', 'completed'];

    public array $translatable = ['institution_name', 'description'];

    protected $fillable = [
        'district_id',
        'event_id',
        'institution_name',
        'slug',
        'type',
        'event_date',
        'venue',
        'description',
        'organizer_partner',
        'ambassador_name',
        'ambassador_contact',
        'status',
        'order',
        'seo_title',
        'seo_description',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (self $campus) => $campus->getTranslation('institution_name', 'en'))
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate()
            ->preventOverwrite();
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function speakers()
    {
        return $this->morphToMany(Speaker::class, 'activity', 'activity_speaker')->withPivot('role');
    }

    public function activationLeads()
    {
        return $this->morphMany(ActivationLead::class, 'activatable');
    }

    public function galleries()
    {
        return $this->morphMany(Gallery::class, 'related');
    }

    public function scopeForCurrentEvent($query)
    {
        return $query->whereHas('event', fn ($q) => $q->where('is_current', true));
    }
}
