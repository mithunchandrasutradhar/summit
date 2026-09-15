<?php

namespace App\Models;

use App\Concerns\HasSeoMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class District extends Model
{
    use HasFactory, HasSeoMeta, HasSlug, HasTranslations;

    public const STATUSES = ['upcoming', 'completed'];

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'division_id',
        'event_id',
        'name',
        'slug',
        'description',
        'venue',
        'event_date',
        'organizer_partner',
        'status',
        'participants_count',
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
            ->generateSlugsFrom(fn (self $district) => $district->getTranslation('name', 'en'))
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate()
            ->preventOverwrite();
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function campusPrograms()
    {
        return $this->hasMany(CampusProgram::class);
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
