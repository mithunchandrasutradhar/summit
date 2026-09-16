<?php

namespace App\Livewire\Forms;

use App\Models\SponsorshipEnquiry;
use App\Models\User;
use App\Services\Notifications\NotificationDispatcher;
use App\Support\ReferenceNumber;
use Livewire\Attributes\On;
use Livewire\Component;

class SponsorshipEnquiryForm extends Component
{
    public ?SponsorshipEnquiry $enquiry = null;

    #[On('dynamic-form-submitted')]
    public function handleSubmission(string $formKey, array $system, array $custom): void
    {
        if ($formKey !== 'sponsorship_enquiry') {
            return;
        }

        $enquiry = SponsorshipEnquiry::create([
            'reference_no' => ReferenceNumber::generate('sponsorship_enquiries', 'SPN'),
            'email' => $system['email'] ?? null,
            'sponsor_tier_id' => $system['sponsor_tier_id'] ?? null,
            'callback_requested_at' => $system['callback_requested_at'] ?? null,
            'preferred_contact_time' => $system['preferred_contact_time'] ?? null,
            'field_values' => $custom,
        ]);

        $dispatcher = app(NotificationDispatcher::class);

        $dispatcher->notify(
            $enquiry,
            'sponsorship_enquiry_confirmation',
            $enquiry->email,
            [
                'name' => $enquiry->contactPerson() ?? '',
                'reference_no' => $enquiry->reference_no,
            ],
        );

        $dispatcher->notifyStaff(
            $enquiry,
            'sponsorship_enquiry_new_lead_internal',
            User::withAnyRole(['sponsorship_manager', 'admin', 'super_admin']),
            [
                'company_name' => $enquiry->companyName() ?? '',
                'contact_person' => $enquiry->contactPerson() ?? '',
                'reference_no' => $enquiry->reference_no,
                'admin_url' => route('filament.admin.resources.sponsorship-enquiries.edit', $enquiry),
            ],
        );

        $this->enquiry = $enquiry;
    }

    public function render()
    {
        return view('livewire.forms.sponsorship-enquiry-form');
    }
}
