<?php

use App\Livewire\Forms\SummitRegistrationForm;
use App\Models\AttendeeType;
use App\Models\EmailTemplate;
use App\Models\Event;
use App\Models\NotificationLog;
use App\Models\Registration;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function () {
    $this->event = Event::factory()->create(['is_current' => true]);
    EmailTemplate::factory()->create([
        'key' => 'registration_confirmation',
        'subject' => 'Hi {name}',
        'body' => 'Ref: {reference_no}',
    ]);
});

it('serves the register page', function () {
    $this->get('/en/register')->assertOk()->assertSee('Register for Summit');
});

it('completes a full registration: creates the record, generates a reference number and qr token, and queues a confirmation email', function () {
    Mail::fake();

    $attendeeType = AttendeeType::factory()->create();

    Livewire::test(SummitRegistrationForm::class)
        ->call('handleSubmission', 'summit_registration', [
            'email' => 'jane@example.com',
            'mobile' => '01700000000',
            'attendee_type_id' => $attendeeType->id,
            'consent_accepted_at' => now()->toDateTimeString(),
        ], [
            'name' => 'Jane Doe',
            'organization' => 'Acme Co',
        ]);

    $registration = Registration::where('email', 'jane@example.com')->first();

    expect($registration)->not->toBeNull();
    expect($registration->reference_no)->toStartWith('REG-');
    expect($registration->qr_token)->not->toBeNull();
    expect($registration->attendee_type_id)->toBe($attendeeType->id);
    expect($registration->name())->toBe('Jane Doe');
    expect($registration->field_values)->toHaveKey('organization', 'Acme Co');
    expect($registration->field_values)->not->toHaveKey('email'); // system column, not custom

    Mail::assertQueued(\App\Mail\TemplatedMail::class);

    expect(NotificationLog::where('notifiable_type', Registration::class)
        ->where('notifiable_id', $registration->id)
        ->where('status', 'sent')
        ->exists())->toBeTrue();
});

it('shows a QR code on the confirmation screen after registering', function () {
    Mail::fake();

    Livewire::test(SummitRegistrationForm::class)
        ->call('handleSubmission', 'summit_registration', ['email' => 'jane@example.com'], ['name' => 'Jane Doe'])
        ->assertSee('Reference number', false)
        ->assertSee('svg', false);
});

it('generates unique reference numbers', function () {
    $numbers = collect(range(1, 20))->map(fn () => \App\Support\ReferenceNumber::generate('registrations', 'REG'));

    expect($numbers->unique()->count())->toBe(20);
    expect($numbers->every(fn ($n) => str_starts_with($n, 'REG-'.now()->year.'-')))->toBeTrue();
});

it('sends registration reminders only within the configured window and does not duplicate', function () {
    Mail::fake();
    EmailTemplate::factory()->create(['key' => 'registration_reminder']);

    $this->event->update(['date_start' => now()->addDays(7)->toDateString()]);
    Registration::factory()->create(['event_id' => $this->event->id, 'status' => 'registered']);

    $this->artisan('app:send-registration-reminders')->assertSuccessful();
    expect(NotificationLog::where('template_key', 'registration_reminder')->where('status', 'sent')->count())->toBe(1);

    // Running again the same day must not duplicate the reminder.
    $this->artisan('app:send-registration-reminders')->assertSuccessful();
    expect(NotificationLog::where('template_key', 'registration_reminder')->where('status', 'sent')->count())->toBe(1);
});

it('does not send a reminder outside the configured window', function () {
    Mail::fake();
    EmailTemplate::factory()->create(['key' => 'registration_reminder']);

    $this->event->update(['date_start' => now()->addDays(20)->toDateString()]);
    Registration::factory()->create(['event_id' => $this->event->id, 'status' => 'registered']);

    $this->artisan('app:send-registration-reminders')->assertSuccessful();

    expect(NotificationLog::where('template_key', 'registration_reminder')->exists())->toBeFalse();
});
