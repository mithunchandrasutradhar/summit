<?php

namespace App\Models;

use App\Services\Notifications\NotificationDispatcher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AwardNomination extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'is_public_shortlisted', 'is_public_winner'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public const STATUSES = ['submitted', 'under_review', 'shortlisted', 'winner', 'rejected', 'incomplete'];

    /**
     * Status → email template key, sent whenever a nomination transitions
     * into that status (see booted() below).
     */
    public const STATUS_NOTIFICATION_TEMPLATES = [
        'under_review' => 'award_under_review',
        'shortlisted' => 'award_shortlisted',
        'winner' => 'award_winner',
        'rejected' => 'award_rejected',
        'incomplete' => 'award_incomplete',
    ];

    protected $fillable = [
        'category_id',
        'reference_no',
        'nominee_email',
        'declaration_accepted_at',
        'status',
        'is_public_shortlisted',
        'is_public_winner',
        'decided_at',
        'field_values',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    protected function casts(): array
    {
        return [
            'declaration_accepted_at' => 'datetime',
            'decided_at' => 'datetime',
            'is_public_shortlisted' => 'boolean',
            'is_public_winner' => 'boolean',
            'field_values' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (self $nomination) {
            if (! $nomination->isDirty('status')) {
                return;
            }

            $nomination->decided_at = now();
            $nomination->is_public_shortlisted = $nomination->status === 'shortlisted' || $nomination->is_public_shortlisted;
            $nomination->is_public_winner = $nomination->status === 'winner';
        });

        static::updated(function (self $nomination) {
            if (! $nomination->wasChanged('status')) {
                return;
            }

            $templateKey = self::STATUS_NOTIFICATION_TEMPLATES[$nomination->status] ?? null;

            if (! $templateKey) {
                return;
            }

            app(NotificationDispatcher::class)->notify(
                $nomination,
                $templateKey,
                $nomination->nominee_email,
                ['name' => $nomination->name() ?? '', 'reference_no' => $nomination->reference_no],
            );
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('supporting_documents');
    }

    public function category()
    {
        return $this->belongsTo(AwardCategory::class, 'category_id');
    }

    public function reviews()
    {
        return $this->hasMany(AwardNominationReview::class, 'nomination_id');
    }

    public function name(): ?string
    {
        return $this->field_values['nominee_name'] ?? null;
    }

    public function scopePubliclyShortlisted($query)
    {
        return $query->where('is_public_shortlisted', true);
    }

    public function scopePubliclyWon($query)
    {
        return $query->where('is_public_winner', true);
    }
}
