<?php

use App\Models\EmailTemplate;
use App\Models\NotificationLog;
use App\Models\Registration;
use App\Services\Notifications\NotificationDispatcher;
use Illuminate\Support\Facades\Mail;

it('renders template placeholders and queues the email', function () {
    Mail::fake();

    EmailTemplate::factory()->create([
        'key' => 'test_email',
        'subject' => 'Hello {name}',
        'body' => 'Your ref is {reference_no}.',
    ]);

    $registration = Registration::factory()->create();

    app(NotificationDispatcher::class)->notify(
        $registration,
        'test_email',
        'jane@example.com',
        ['name' => 'Jane', 'reference_no' => 'REG-2026-000001'],
    );

    Mail::assertQueued(\App\Mail\TemplatedMail::class, function ($mail) {
        return $mail->renderedSubject === 'Hello Jane'
            && $mail->renderedBody === 'Your ref is REG-2026-000001.';
    });

    expect(NotificationLog::where('notifiable_id', $registration->id)->where('status', 'sent')->exists())->toBeTrue();
});

it('logs a failure when no template exists for the given key', function () {
    Mail::fake();

    $registration = Registration::factory()->create();

    app(NotificationDispatcher::class)->notify($registration, 'missing_template', 'jane@example.com');

    expect(NotificationLog::where('notifiable_id', $registration->id)->where('status', 'failed')->exists())->toBeTrue();
    Mail::assertNothingQueued();
});

it('does not send sms when the phone is missing, even if sms is enabled', function () {
    $registration = Registration::factory()->create();
    EmailTemplate::factory()->create(['key' => 'test_email2']);

    app(NotificationDispatcher::class)->notify($registration, 'test_email2', 'jane@example.com', [], null);

    expect(NotificationLog::where('notifiable_id', $registration->id)->where('channel', 'sms')->exists())->toBeFalse();
});
