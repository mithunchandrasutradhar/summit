<?php

use App\Livewire\Forms\AwardNominationForm;
use App\Models\AwardCategory;
use App\Models\AwardNomination;
use App\Models\EmailTemplate;
use App\Models\Event;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->event = Event::factory()->create(['is_current' => true]);
    EmailTemplate::factory()->create(['key' => 'award_nomination_confirmation']);
    EmailTemplate::factory()->create(['key' => 'award_under_review']);
    EmailTemplate::factory()->create(['key' => 'award_shortlisted']);
    EmailTemplate::factory()->create(['key' => 'award_winner']);
    EmailTemplate::factory()->create(['key' => 'award_rejected']);
    EmailTemplate::factory()->create(['key' => 'award_incomplete']);
});

it('serves the awards landing, categories, shortlisted and winners pages', function () {
    $category = AwardCategory::factory()->create(['event_id' => $this->event->id]);

    $this->get('/en/awards')->assertOk()->assertSee($category->name);
    $this->get('/en/awards/categories')->assertOk()->assertSee($category->name);
    $this->get('/en/awards/nominate')->assertOk();

    $shortlisted = AwardNomination::factory()->create([
        'category_id' => $category->id,
        'is_public_shortlisted' => true,
    ]);
    $this->get('/en/awards/shortlisted')->assertOk()->assertSee($shortlisted->name());

    $winner = AwardNomination::factory()->create([
        'category_id' => $category->id,
        'is_public_winner' => true,
    ]);
    $this->get('/en/awards/winners')->assertOk()->assertSee($winner->name());
});

it('completes a full nomination: creates the record, generates a reference number, attaches the uploaded file, and queues a confirmation email', function () {
    Mail::fake();
    Storage::fake('local');

    $category = AwardCategory::factory()->create(['event_id' => $this->event->id]);

    // Simulate a pending upload already having landed on disk — the file
    // ending up there in the first place is exercised in DynamicFormRendererTest.
    Storage::disk('local')->put('pending-form-uploads/portfolio-test.pdf', 'fake-pdf-content');

    Livewire::test(AwardNominationForm::class)
        ->call('handleSubmission', 'award_nomination', [
            'category_id' => $category->id,
            'nominee_email' => 'jane2@example.com',
            'declaration_accepted_at' => now()->toDateTimeString(),
        ], [
            'nominee_name' => 'Jane Two',
            'supporting_documents' => 'pending-form-uploads/portfolio-test.pdf',
        ]);

    $nomination = AwardNomination::where('nominee_email', 'jane2@example.com')->first();

    expect($nomination)->not->toBeNull();
    expect($nomination->reference_no)->toStartWith('AWD-');
    expect($nomination->category_id)->toBe($category->id);
    expect($nomination->name())->toBe('Jane Two');
    expect($nomination->field_values)->not->toHaveKey('supporting_documents');
    expect($nomination->getMedia('supporting_documents'))->toHaveCount(1);
    expect(Storage::disk('local')->exists('pending-form-uploads/portfolio-test.pdf'))->toBeFalse();

    Mail::assertQueued(\App\Mail\TemplatedMail::class);
    expect(NotificationLog::where('notifiable_type', AwardNomination::class)
        ->where('notifiable_id', $nomination->id)
        ->where('status', 'sent')
        ->exists())->toBeTrue();
});

it('fires the shortlisted/winner/rejected notification and flips public visibility flags when status changes', function () {
    Mail::fake();

    $nomination = AwardNomination::factory()->create(['status' => 'submitted']);

    $nomination->update(['status' => 'shortlisted']);
    expect($nomination->fresh()->is_public_shortlisted)->toBeTrue();
    expect(NotificationLog::where('template_key', 'award_shortlisted')->where('status', 'sent')->exists())->toBeTrue();

    $nomination->update(['status' => 'winner']);
    expect($nomination->fresh()->is_public_winner)->toBeTrue();
    expect(NotificationLog::where('template_key', 'award_winner')->where('status', 'sent')->exists())->toBeTrue();
});

it('also fires notifications for the under_review and incomplete transitions', function () {
    Mail::fake();

    $nomination = AwardNomination::factory()->create(['status' => 'submitted']);

    $nomination->update(['status' => 'under_review']);
    expect(NotificationLog::where('template_key', 'award_under_review')->where('status', 'sent')->exists())->toBeTrue();

    $nomination->update(['status' => 'incomplete']);
    expect(NotificationLog::where('template_key', 'award_incomplete')->where('status', 'sent')->exists())->toBeTrue();
});

it('does not re-fire a notification when saving without a status change', function () {
    Mail::fake();

    $nomination = AwardNomination::factory()->create(['status' => 'shortlisted']);
    NotificationLog::query()->delete(); // clear whatever the creation itself logged, if anything

    $nomination->update(['nominee_email' => 'changed@example.com']);

    expect(NotificationLog::where('template_key', 'award_shortlisted')->exists())->toBeFalse();
});
