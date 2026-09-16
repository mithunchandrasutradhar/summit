<?php

namespace App\Livewire\Forms;

use App\Models\ExhibitorApplication;
use App\Models\User;
use App\Services\Notifications\NotificationDispatcher;
use App\Support\ReferenceNumber;
use Livewire\Attributes\On;
use Livewire\Component;

class ExhibitorApplicationForm extends Component
{
    public ?ExhibitorApplication $application = null;

    #[On('dynamic-form-submitted')]
    public function handleSubmission(string $formKey, array $system, array $custom): void
    {
        if ($formKey !== 'exhibitor_application') {
            return;
        }

        $application = ExhibitorApplication::create([
            'reference_no' => ReferenceNumber::generate('exhibitor_applications', 'EXH'),
            'email' => $system['email'] ?? null,
            'preferred_booth_id' => $system['preferred_booth_id'] ?? null,
            'field_values' => $custom,
        ]);

        $dispatcher = app(NotificationDispatcher::class);

        $dispatcher->notify(
            $application,
            'exhibitor_application_confirmation',
            $application->email,
            [
                'name' => $application->contactPerson() ?? '',
                'reference_no' => $application->reference_no,
            ],
        );

        $dispatcher->notifyStaff(
            $application,
            'exhibitor_application_new_lead_internal',
            User::withAnyRole(['exhibition_manager', 'admin', 'super_admin']),
            [
                'organization_name' => $application->organizationName() ?? '',
                'contact_person' => $application->contactPerson() ?? '',
                'reference_no' => $application->reference_no,
                'admin_url' => route('filament.admin.resources.exhibitor-applications.edit', $application),
            ],
        );

        $this->application = $application;
    }

    public function render()
    {
        return view('livewire.forms.exhibitor-application-form');
    }
}
