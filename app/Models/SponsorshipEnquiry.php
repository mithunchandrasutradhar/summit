<?php

namespace App\Models;

use App\Services\Notifications\NotificationDispatcher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SponsorshipEnquiry extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'sponsor_tier_id', 'assigned_to'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public const STATUSES = ['new', 'contacted', 'negotiation', 'confirmed', 'closed'];

    protected $fillable = [
        'reference_no',
        'email',
        'sponsor_tier_id',
        'status',
        'assigned_to',
        'internal_notes',
        'callback_requested_at',
        'preferred_contact_time',
        'field_values',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    protected function casts(): array
    {
        return [
            'callback_requested_at' => 'datetime',
            'field_values' => 'array',
        ];
    }

    public function sponsorTier()
    {
        return $this->belongsTo(SponsorTier::class, 'sponsor_tier_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function sponsor()
    {
        return $this->hasOne(Sponsor::class, 'enquiry_id');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function contactPerson(): ?string
    {
        return $this->field_values['contact_person'] ?? null;
    }

    public function companyName(): ?string
    {
        return $this->field_values['company_name'] ?? null;
    }

    /**
     * Called by PaymentController once a payment for this enquiry has been
     * verified as paid — publishes the confirmed sponsor and closes the lead.
     */
    public function onPaymentConfirmed(Payment $payment): void
    {
        Sponsor::create([
            'name' => $this->companyName() ?? 'Sponsor',
            'tier_id' => $this->sponsor_tier_id,
            'enquiry_id' => $this->id,
            'is_published' => false,
            'order' => 0,
        ]);

        $this->update(['status' => 'closed']);

        app(NotificationDispatcher::class)->notify($this, 'sponsorship_confirmed', $this->email, [
            'name' => $this->contactPerson() ?? '',
            'reference_no' => $this->reference_no,
        ]);
    }
}
