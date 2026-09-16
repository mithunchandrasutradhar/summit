<?php

namespace App\Livewire\Forms;

use App\Models\ForumMember;
use App\Services\Notifications\NotificationDispatcher;
use App\Support\ReferenceNumber;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;

class ForumMembershipForm extends Component
{
    public ?ForumMember $member = null;

    #[On('dynamic-form-submitted')]
    public function handleSubmission(string $formKey, array $system, array $custom): void
    {
        if ($formKey !== 'forum_membership') {
            return;
        }

        $pendingFilePath = $custom['photo'] ?? null;
        unset($custom['photo']);

        $member = ForumMember::create([
            'reference_no' => ReferenceNumber::generate('forum_members', 'FRM'),
            'email' => $system['email'] ?? null,
            'consent_accepted_at' => $system['consent_accepted_at'] ?? null,
            'field_values' => $custom,
        ]);

        if ($pendingFilePath && Storage::disk('local')->exists($pendingFilePath)) {
            $member
                ->addMediaFromDisk($pendingFilePath, 'local')
                ->toMediaCollection('photo');

            Storage::disk('local')->delete($pendingFilePath);
        }

        app(NotificationDispatcher::class)->notify(
            $member,
            'forum_membership_confirmation',
            $member->email,
            [
                'name' => $member->name() ?? '',
                'reference_no' => $member->reference_no,
            ],
        );

        $this->member = $member;
    }

    public function render()
    {
        return view('livewire.forms.forum-membership-form');
    }
}
