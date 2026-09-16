<?php

namespace App\Models;

use App\Services\Notifications\NotificationDispatcher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ForumMember extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public const STATUSES = ['submitted', 'approved', 'rejected'];

    /**
     * Status → email template key, sent whenever a member transitions into
     * that status (see booted() below).
     */
    public const STATUS_NOTIFICATION_TEMPLATES = [
        'approved' => 'forum_membership_approved',
        'rejected' => 'forum_membership_rejected',
    ];

    protected $fillable = [
        'reference_no',
        'email',
        'consent_accepted_at',
        'status',
        'field_values',
    ];

    protected function casts(): array
    {
        return [
            'consent_accepted_at' => 'datetime',
            'field_values' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::updated(function (self $member) {
            if (! $member->wasChanged('status')) {
                return;
            }

            $templateKey = self::STATUS_NOTIFICATION_TEMPLATES[$member->status] ?? null;

            if (! $templateKey) {
                return;
            }

            app(NotificationDispatcher::class)->notify(
                $member,
                $templateKey,
                $member->email,
                ['name' => $member->name() ?? '', 'reference_no' => $member->reference_no],
            );
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function photoUrl(): ?string
    {
        return $this->getFirstMediaUrl('photo') ?: null;
    }

    public function name(): ?string
    {
        return $this->field_values['full_name'] ?? null;
    }

    public function category(): ?string
    {
        return $this->field_values['freelancer_category'] ?? null;
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
