<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Registration extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'checked_in_at', 'attendee_type_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public const STATUSES = ['registered', 'cancelled'];

    protected $fillable = [
        'event_id',
        'reference_no',
        'email',
        'mobile',
        'attendee_type_id',
        'consent_accepted_at',
        'qr_token',
        'checked_in_at',
        'status',
        'field_values',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    protected function casts(): array
    {
        return [
            'consent_accepted_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'field_values' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $registration) {
            $registration->qr_token ??= (string) Str::uuid();
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attendeeType()
    {
        return $this->belongsTo(AttendeeType::class);
    }

    public function name(): ?string
    {
        return $this->field_values['name'] ?? null;
    }

    public function scopeRegistered($query)
    {
        return $query->where('status', 'registered');
    }
}
