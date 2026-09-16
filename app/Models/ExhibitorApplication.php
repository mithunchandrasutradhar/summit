<?php

namespace App\Models;

use App\Services\Notifications\NotificationDispatcher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ExhibitorApplication extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'preferred_booth_id', 'assigned_to'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public const STATUSES = ['new', 'contacted', 'confirmed', 'cancelled'];

    protected $fillable = [
        'reference_no',
        'email',
        'preferred_booth_id',
        'status',
        'assigned_to',
        'internal_notes',
        'field_values',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    protected function casts(): array
    {
        return [
            'field_values' => 'array',
        ];
    }

    public function preferredBooth()
    {
        return $this->belongsTo(ExhibitionBooth::class, 'preferred_booth_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function exhibitor()
    {
        return $this->hasOne(Exhibitor::class, 'application_id');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * Admin assigning/changing the proposed booth on this application moves
     * that booth to 'reserved' (holding it while payment is pending) and
     * frees up whatever booth it previously held — the middle step of the
     * available -> reserved -> confirmed lifecycle plan §5 describes.
     * onPaymentConfirmed() above handles the final reserved -> confirmed step.
     * Deliberately only on update, not create: the public application form
     * just records a *preference*, not a reservation — only an admin
     * actually assigning it should hold the booth.
     */
    protected static function booted(): void
    {
        static::updating(function (self $application) {
            if (! $application->isDirty('preferred_booth_id')) {
                return;
            }

            $previousBoothId = $application->getOriginal('preferred_booth_id');

            if ($previousBoothId) {
                ExhibitionBooth::where('id', $previousBoothId)
                    ->where('status', 'reserved')
                    ->update(['status' => 'available']);
            }

            if ($application->preferred_booth_id) {
                ExhibitionBooth::where('id', $application->preferred_booth_id)
                    ->where('status', 'available')
                    ->update(['status' => 'reserved']);
            }
        });
    }

    public function contactPerson(): ?string
    {
        return $this->field_values['contact_person'] ?? null;
    }

    public function organizationName(): ?string
    {
        return $this->field_values['organization_name'] ?? null;
    }

    /**
     * Called by PaymentController once a payment for this application has
     * been verified as paid — confirms the booth and publishes the exhibitor.
     */
    public function onPaymentConfirmed(Payment $payment): void
    {
        if ($this->preferredBooth) {
            $this->preferredBooth->update(['status' => 'confirmed']);
        }

        Exhibitor::create([
            'application_id' => $this->id,
            'company_name' => $this->organizationName() ?? 'Exhibitor',
            'sector' => $this->field_values['sector'] ?? null,
            'booth_id' => $this->preferred_booth_id,
            'is_published' => false,
        ]);

        $this->update(['status' => 'confirmed']);

        app(NotificationDispatcher::class)->notify($this, 'exhibitor_confirmed', $this->email, [
            'name' => $this->contactPerson() ?? '',
            'reference_no' => $this->reference_no,
        ]);
    }
}
