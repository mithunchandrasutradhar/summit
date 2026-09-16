<?php

use App\Models\AwardNomination;
use App\Models\ExhibitorApplication;
use App\Models\ForumMember;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\SponsorshipEnquiry;
use Spatie\Activitylog\Models\Activity;

it('logs a status change on each of the audit-sensitive submission models', function (string $model, array $attributes) {
    $record = $model::factory()->create($attributes);

    Activity::query()->delete(); // clear whatever the factory's own creation logged

    $record->update(['status' => $record::STATUSES[array_search($record->status, $record::STATUSES) + 1] ?? $record::STATUSES[0]]);

    expect(Activity::where('subject_type', $model)->where('subject_id', $record->id)->exists())->toBeTrue();
})->with([
    'registration' => [Registration::class, ['status' => 'registered']],
    'award nomination' => [AwardNomination::class, ['status' => 'submitted']],
    'sponsorship enquiry' => [SponsorshipEnquiry::class, ['status' => 'new']],
    'exhibitor application' => [ExhibitorApplication::class, ['status' => 'new']],
    'forum member' => [ForumMember::class, ['status' => 'submitted']],
]);

it('logs a payment status change', function () {
    $payment = Payment::factory()->create(['status' => 'pending']);

    Activity::query()->delete();

    $payment->update(['status' => 'paid']);

    expect(Activity::where('subject_type', Payment::class)->where('subject_id', $payment->id)->exists())->toBeTrue();
});

it('does not log a save that does not change any tracked attribute', function () {
    $registration = Registration::factory()->create(['status' => 'registered']);

    Activity::query()->delete();

    $registration->update(['email' => 'changed@example.com']);

    expect(Activity::where('subject_type', Registration::class)->where('subject_id', $registration->id)->exists())->toBeFalse();
});
