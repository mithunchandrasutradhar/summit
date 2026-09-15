<?php

namespace App\Models;

use App\Concerns\HasSeoMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Session extends Model
{
    use HasFactory, HasSeoMeta, HasSlug, HasTranslations;

    protected $table = 'agenda_sessions';

    public const TYPES = [
        'keynote', 'seminar', 'workshop', 'career_talk', 'panel',
        'ai_awareness', 'success_story', 'marketplace', 'freelancer_to_entrepreneur',
        'award', 'networking',
    ];

    public array $translatable = ['title', 'description'];

    protected $fillable = [
        'event_id',
        'title',
        'slug',
        'description',
        'type',
        'track',
        'hall_id',
        'date',
        'start_time',
        'end_time',
        'is_published',
        'order',
        'seo_title',
        'seo_description',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (self $session) => $session->getTranslation('title', 'en'))
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate()
            ->preventOverwrite();
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function speakers()
    {
        return $this->belongsToMany(Speaker::class, 'session_speaker')->withPivot('role');
    }

    public function moderators()
    {
        return $this->speakers()->wherePivot('role', 'moderator');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForCurrentEvent($query)
    {
        return $query->whereHas('event', fn ($q) => $q->where('is_current', true));
    }

    public function startsAt(): ?\Illuminate\Support\Carbon
    {
        if (! $this->date || ! $this->start_time) {
            return null;
        }

        return $this->date->copy()->setTimeFromTimeString($this->start_time);
    }

    public function endsAt(): ?\Illuminate\Support\Carbon
    {
        if (! $this->date || ! $this->end_time) {
            return null;
        }

        return $this->date->copy()->setTimeFromTimeString($this->end_time);
    }
}
