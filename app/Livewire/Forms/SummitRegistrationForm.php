<?php

namespace App\Livewire\Forms;

use App\Models\Event;
use App\Models\Registration;
use App\Services\Notifications\NotificationDispatcher;
use App\Support\ReferenceNumber;
use Livewire\Attributes\On;
use Livewire\Component;

class SummitRegistrationForm extends Component
{
    public ?Registration $registration = null;

    #[On('dynamic-form-submitted')]
    public function handleSubmission(string $formKey, array $system, array $custom): void
    {
        if ($formKey !== 'summit_registration') {
            return;
        }

        $event = Event::current();

        $registration = Registration::create([
            'event_id' => $event?->id,
            'reference_no' => ReferenceNumber::generate('registrations', 'REG'),
            'email' => $system['email'] ?? null,
            'mobile' => $system['mobile'] ?? null,
            'attendee_type_id' => $system['attendee_type_id'] ?? null,
            'consent_accepted_at' => $system['consent_accepted_at'] ?? null,
            'field_values' => $custom,
        ]);

        app(NotificationDispatcher::class)->notify(
            $registration,
            'registration_confirmation',
            $registration->email,
            [
                'name' => $registration->name() ?? '',
                'reference_no' => $registration->reference_no,
            ],
            $registration->mobile,
        );

        $this->registration = $registration;
    }

    public function render()
    {
        return view('livewire.forms.summit-registration-form');
    }
}
