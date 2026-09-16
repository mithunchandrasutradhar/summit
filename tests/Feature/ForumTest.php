<?php

use App\Livewire\Forms\ForumMembershipForm;
use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use App\Models\Faq;
use App\Models\ForumMember;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    EmailTemplate::factory()->create(['key' => 'forum_membership_confirmation']);
    EmailTemplate::factory()->create(['key' => 'forum_membership_approved']);
    EmailTemplate::factory()->create(['key' => 'forum_membership_rejected']);
});

it('serves the forum landing and registration pages', function () {
    Faq::factory()->create([
        'group' => 'forum',
        'question' => ['en' => 'Is there a membership fee?', 'bn' => 'Is there a membership fee?'],
    ]);

    $this->get('/en/forum')->assertOk()->assertSee('Is there a membership fee?');
    $this->get('/en/forum/register')->assertOk();
});

it('completes a forum membership application: creates the record, generates a reference number, attaches the uploaded photo, and queues a confirmation email', function () {
    Mail::fake();
    Storage::fake('local');

    Storage::disk('local')->put('pending-form-uploads/photo-test.jpg', 'fake-image-content');

    Livewire::test(ForumMembershipForm::class)
        ->call('handleSubmission', 'forum_membership', [
            'email' => 'member@example.com',
            'consent_accepted_at' => now()->toDateTimeString(),
        ], [
            'full_name' => 'Jane Freelancer',
            'freelancer_category' => 'freelancer',
            'photo' => 'pending-form-uploads/photo-test.jpg',
        ]);

    $member = ForumMember::where('email', 'member@example.com')->first();

    expect($member)->not->toBeNull();
    expect($member->reference_no)->toStartWith('FRM-');
    expect($member->name())->toBe('Jane Freelancer');
    expect($member->category())->toBe('freelancer');
    expect($member->field_values)->not->toHaveKey('photo');
    expect($member->getMedia('photo'))->toHaveCount(1);
    expect(Storage::disk('local')->exists('pending-form-uploads/photo-test.jpg'))->toBeFalse();

    Mail::assertQueued(TemplatedMail::class);
    expect(NotificationLog::where('notifiable_type', ForumMember::class)
        ->where('notifiable_id', $member->id)
        ->where('status', 'sent')
        ->exists())->toBeTrue();
});

it('fires the approved and rejected notifications when status changes', function () {
    Mail::fake();

    $approved = ForumMember::factory()->create(['status' => 'submitted']);
    $approved->update(['status' => 'approved']);

    expect(NotificationLog::where('notifiable_id', $approved->id)
        ->where('template_key', 'forum_membership_approved')
        ->where('status', 'sent')
        ->exists())->toBeTrue();

    $rejected = ForumMember::factory()->create(['status' => 'submitted']);
    $rejected->update(['status' => 'rejected']);

    expect(NotificationLog::where('notifiable_id', $rejected->id)
        ->where('template_key', 'forum_membership_rejected')
        ->where('status', 'sent')
        ->exists())->toBeTrue();
});

it('does not re-fire a notification when saving without a status change', function () {
    Mail::fake();

    $member = ForumMember::factory()->create(['status' => 'approved']);
    NotificationLog::query()->delete();

    $member->update(['email' => 'changed@example.com']);

    expect(NotificationLog::where('template_key', 'forum_membership_approved')->exists())->toBeFalse();
});
