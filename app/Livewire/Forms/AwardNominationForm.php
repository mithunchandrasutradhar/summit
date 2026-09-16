<?php

namespace App\Livewire\Forms;

use App\Models\AwardNomination;
use App\Services\Notifications\NotificationDispatcher;
use App\Support\ReferenceNumber;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;

class AwardNominationForm extends Component
{
    public ?AwardNomination $nomination = null;

    #[On('dynamic-form-submitted')]
    public function handleSubmission(string $formKey, array $system, array $custom): void
    {
        if ($formKey !== 'award_nomination') {
            return;
        }

        $pendingFilePath = $custom['supporting_documents'] ?? null;
        unset($custom['supporting_documents']);

        $nomination = AwardNomination::create([
            'category_id' => $system['category_id'] ?? null,
            'reference_no' => ReferenceNumber::generate('award_nominations', 'AWD'),
            'nominee_email' => $system['nominee_email'] ?? null,
            'declaration_accepted_at' => $system['declaration_accepted_at'] ?? null,
            'field_values' => $custom,
        ]);

        if ($pendingFilePath && Storage::disk('local')->exists($pendingFilePath)) {
            $nomination
                ->addMediaFromDisk($pendingFilePath, 'local')
                ->toMediaCollection('supporting_documents');

            Storage::disk('local')->delete($pendingFilePath);
        }

        app(NotificationDispatcher::class)->notify(
            $nomination,
            'award_nomination_confirmation',
            $nomination->nominee_email,
            [
                'name' => $nomination->name() ?? '',
                'reference_no' => $nomination->reference_no,
            ],
        );

        $this->nomination = $nomination;
    }

    public function render()
    {
        return view('livewire.forms.award-nomination-form');
    }
}
