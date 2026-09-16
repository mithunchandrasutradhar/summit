<?php

use App\Livewire\Forms\ExhibitorApplicationForm;
use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use App\Models\Event;
use App\Models\ExhibitionBooth;
use App\Models\Exhibitor;
use App\Models\ExhibitorApplication;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->event = Event::factory()->create(['is_current' => true]);
    EmailTemplate::factory()->create(['key' => 'exhibitor_application_confirmation']);
    EmailTemplate::factory()->create(['key' => 'exhibition_payment_link']);
    EmailTemplate::factory()->create(['key' => 'exhibitor_confirmed']);
    EmailTemplate::factory()->create(['key' => 'exhibitor_application_new_lead_internal']);
});

it('serves the exhibition and apply pages', function () {
    $booth = ExhibitionBooth::factory()->create(['event_id' => $this->event->id]);
    Exhibitor::factory()->create(['booth_id' => $booth->id]);

    $this->get('/en/exhibition')->assertOk();
    $this->get('/en/exhibition/apply')->assertOk();
});

it('completes an exhibitor application: creates the record, generates a reference number, and queues a confirmation email', function () {
    Mail::fake();

    $booth = ExhibitionBooth::factory()->create(['event_id' => $this->event->id]);

    Livewire::test(ExhibitorApplicationForm::class)
        ->call('handleSubmission', 'exhibitor_application', [
            'email' => 'exhibitor@example.com',
            'preferred_booth_id' => $booth->id,
        ], [
            'organization_name' => 'Acme Studio',
            'contact_person' => 'John Roe',
            'phone' => '+8801700000001',
            'sector' => 'Design',
        ]);

    $application = ExhibitorApplication::where('email', 'exhibitor@example.com')->first();

    expect($application)->not->toBeNull();
    expect($application->reference_no)->toStartWith('EXH-');
    expect($application->preferred_booth_id)->toBe($booth->id);
    expect($application->organizationName())->toBe('Acme Studio');

    Mail::assertQueued(TemplatedMail::class);
    expect(NotificationLog::where('notifiable_type', ExhibitorApplication::class)
        ->where('notifiable_id', $application->id)
        ->where('status', 'sent')
        ->exists())->toBeTrue();
});

it('alerts exhibition managers by email when a new application comes in', function () {
    Mail::fake();

    Role::firstOrCreate(['name' => 'exhibition_manager', 'guard_name' => 'web']);
    $manager = User::factory()->create(['email' => 'exhibition-manager@example.com']);
    $manager->assignRole('exhibition_manager');

    $booth = ExhibitionBooth::factory()->create(['event_id' => $this->event->id]);

    Livewire::test(ExhibitorApplicationForm::class)
        ->call('handleSubmission', 'exhibitor_application', [
            'email' => 'exhibitor2@example.com',
            'preferred_booth_id' => $booth->id,
        ], [
            'organization_name' => 'Beta Studio',
            'contact_person' => 'Alex Roe',
        ]);

    $application = ExhibitorApplication::where('email', 'exhibitor2@example.com')->first();

    Mail::assertQueued(TemplatedMail::class, fn ($mail) => $mail->hasTo('exhibition-manager@example.com'));

    expect(NotificationLog::where('notifiable_type', ExhibitorApplication::class)
        ->where('notifiable_id', $application->id)
        ->where('template_key', 'exhibitor_application_new_lead_internal')
        ->where('status', 'sent')
        ->exists())->toBeTrue();
});

it('initiates an exhibition payment, redirects to the gateway, and confirms the booth and exhibitor on a valid IPN callback', function () {
    Mail::fake();

    Http::fake([
        '*/gwprocess/v4/api.php' => Http::response(['GatewayPageURL' => 'https://sandbox.sslcommerz.com/EasyCheckOut/def456']),
    ]);

    $booth = ExhibitionBooth::factory()->create(['event_id' => $this->event->id, 'price' => 30000, 'status' => 'reserved']);
    $application = ExhibitorApplication::factory()->create(['preferred_booth_id' => $booth->id, 'status' => 'contacted']);

    $url = URL::signedRoute('payments.exhibition.initiate', ['locale' => 'en', 'application' => $application->id]);

    $this->get($url)->assertRedirect('https://sandbox.sslcommerz.com/EasyCheckOut/def456');

    $payment = Payment::where('payable_type', ExhibitorApplication::class)->where('payable_id', $application->id)->first();

    expect($payment)->not->toBeNull();
    expect((float) $payment->amount)->toBe(30000.0);

    Http::fake([
        '*/validator/api/validationserverAPI.php*' => Http::response([
            'status' => 'VALID',
            'tran_id' => $payment->gateway_txn_id,
            'amount' => '30000.00',
            'currency' => 'BDT',
        ]),
    ]);

    $this->post(route('payments.sslcommerz.ipn'), [
        'tran_id' => $payment->gateway_txn_id,
        'val_id' => 'fakevalid456',
    ])->assertOk();

    expect($payment->fresh()->status)->toBe('paid');
    expect($booth->fresh()->status)->toBe('confirmed');
    expect(Exhibitor::where('application_id', $application->id)->where('booth_id', $booth->id)->exists())->toBeTrue();
    expect($application->fresh()->status)->toBe('confirmed');
    Mail::assertQueued(TemplatedMail::class);
});

it('reserves a booth when an admin assigns it to an application, and frees the previous booth', function () {
    $boothA = ExhibitionBooth::factory()->create(['event_id' => $this->event->id, 'status' => 'available']);
    $boothB = ExhibitionBooth::factory()->create(['event_id' => $this->event->id, 'status' => 'available']);

    // preferred_booth_id here reflects the applicant's own stated preference
    // from the public form — not yet an admin assignment, so it must not
    // reserve the booth on its own.
    $application = ExhibitorApplication::factory()->create(['preferred_booth_id' => $boothA->id, 'status' => 'new']);
    expect($boothA->fresh()->status)->toBe('available');

    // An admin actually assigning/confirming that booth is what reserves it.
    $application->update(['preferred_booth_id' => $boothA->id, 'status' => 'contacted']);
    expect($boothA->fresh()->status)->toBe('available'); // unchanged: preferred_booth_id itself didn't change

    $application->update(['preferred_booth_id' => null]);
    $application->update(['preferred_booth_id' => $boothA->id]);
    expect($boothA->fresh()->status)->toBe('reserved');

    $application->update(['preferred_booth_id' => $boothB->id]);
    expect($boothA->fresh()->status)->toBe('available');
    expect($boothB->fresh()->status)->toBe('reserved');
});

it('rejects an unsigned exhibition payment initiation link', function () {
    $booth = ExhibitionBooth::factory()->create(['event_id' => $this->event->id]);
    $application = ExhibitorApplication::factory()->create(['preferred_booth_id' => $booth->id]);

    $this->get("/en/exhibition/pay/{$application->id}")->assertForbidden();
});
