<?php

use App\Livewire\Forms\SponsorshipEnquiryForm;
use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use App\Models\Event;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Models\Sponsor;
use App\Models\SponsorshipEnquiry;
use App\Models\SponsorTier;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->event = Event::factory()->create(['is_current' => true]);
    EmailTemplate::factory()->create(['key' => 'sponsorship_enquiry_confirmation']);
    EmailTemplate::factory()->create(['key' => 'sponsorship_payment_link']);
    EmailTemplate::factory()->create(['key' => 'sponsorship_confirmed']);
    EmailTemplate::factory()->create(['key' => 'sponsorship_enquiry_new_lead_internal']);
});

it('serves the sponsors and sponsorship opportunity pages', function () {
    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id]);
    Sponsor::factory()->create(['tier_id' => $tier->id]);

    $this->get('/en/sponsors')->assertOk();
    $this->get('/en/sponsorship-opportunity')->assertOk()->assertSee($tier->name);
});

it('completes a sponsorship enquiry: creates the record, generates a reference number, and queues a confirmation email', function () {
    Mail::fake();

    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id]);

    Livewire::test(SponsorshipEnquiryForm::class)
        ->call('handleSubmission', 'sponsorship_enquiry', [
            'email' => 'sponsor@example.com',
            'sponsor_tier_id' => $tier->id,
        ], [
            'company_name' => 'Acme Co',
            'contact_person' => 'Jane Doe',
            'phone' => '+8801700000000',
        ]);

    $enquiry = SponsorshipEnquiry::where('email', 'sponsor@example.com')->first();

    expect($enquiry)->not->toBeNull();
    expect($enquiry->reference_no)->toStartWith('SPN-');
    expect($enquiry->sponsor_tier_id)->toBe($tier->id);
    expect($enquiry->companyName())->toBe('Acme Co');
    expect($enquiry->contactPerson())->toBe('Jane Doe');

    Mail::assertQueued(TemplatedMail::class);
    expect(NotificationLog::where('notifiable_type', SponsorshipEnquiry::class)
        ->where('notifiable_id', $enquiry->id)
        ->where('status', 'sent')
        ->exists())->toBeTrue();
});

it('alerts sponsorship managers by email when a new enquiry comes in', function () {
    Mail::fake();

    Role::firstOrCreate(['name' => 'sponsorship_manager', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'exhibition_manager', 'guard_name' => 'web']);
    $manager = User::factory()->create(['email' => 'manager@example.com']);
    $manager->assignRole('sponsorship_manager');
    $unrelatedStaff = User::factory()->create();
    $unrelatedStaff->assignRole('exhibition_manager');

    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id]);

    Livewire::test(SponsorshipEnquiryForm::class)
        ->call('handleSubmission', 'sponsorship_enquiry', [
            'email' => 'sponsor2@example.com',
            'sponsor_tier_id' => $tier->id,
        ], [
            'company_name' => 'Beta Inc',
            'contact_person' => 'Sam Roe',
        ]);

    $enquiry = SponsorshipEnquiry::where('email', 'sponsor2@example.com')->first();

    Mail::assertQueued(TemplatedMail::class, fn ($mail) => $mail->hasTo('manager@example.com'));

    expect(NotificationLog::where('notifiable_type', SponsorshipEnquiry::class)
        ->where('notifiable_id', $enquiry->id)
        ->where('template_key', 'sponsorship_enquiry_new_lead_internal')
        ->where('status', 'sent')
        ->exists())->toBeTrue();
});

it('initiates a sponsorship payment, redirects to the gateway, and confirms the sponsor on a valid IPN callback', function () {
    Mail::fake();

    Http::fake([
        '*/gwprocess/v4/api.php' => Http::response(['GatewayPageURL' => 'https://sandbox.sslcommerz.com/EasyCheckOut/abc123']),
    ]);

    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id, 'price' => 100000]);
    $enquiry = SponsorshipEnquiry::factory()->create(['sponsor_tier_id' => $tier->id, 'status' => 'confirmed']);

    $url = URL::signedRoute('payments.sponsorship.initiate', ['locale' => 'en', 'enquiry' => $enquiry->id]);

    $this->get($url)->assertRedirect('https://sandbox.sslcommerz.com/EasyCheckOut/abc123');

    $payment = Payment::where('payable_type', SponsorshipEnquiry::class)->where('payable_id', $enquiry->id)->first();

    expect($payment)->not->toBeNull();
    expect($payment->status)->toBe('pending');
    expect((float) $payment->amount)->toBe(100000.0);

    Http::fake([
        '*/validator/api/validationserverAPI.php*' => Http::response([
            'status' => 'VALID',
            'tran_id' => $payment->gateway_txn_id,
            'amount' => '100000.00',
            'currency' => 'BDT',
        ]),
    ]);

    $this->post(route('payments.sslcommerz.ipn'), [
        'tran_id' => $payment->gateway_txn_id,
        'val_id' => 'fakevalid123',
        'status' => 'VALID',
    ])->assertOk();

    expect($payment->fresh()->status)->toBe('paid');
    expect(Sponsor::where('enquiry_id', $enquiry->id)->where('tier_id', $tier->id)->exists())->toBeTrue();
    expect($enquiry->fresh()->status)->toBe('closed');
    Mail::assertQueued(TemplatedMail::class);
});

it('does not confirm a sponsorship payment when the gateway validation amount does not match', function () {
    Http::fake([
        '*/gwprocess/v4/api.php' => Http::response(['GatewayPageURL' => 'https://sandbox.sslcommerz.com/EasyCheckOut/abc999']),
    ]);

    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id, 'price' => 100000]);
    $enquiry = SponsorshipEnquiry::factory()->create(['sponsor_tier_id' => $tier->id, 'status' => 'confirmed']);

    $url = URL::signedRoute('payments.sponsorship.initiate', ['locale' => 'en', 'enquiry' => $enquiry->id]);
    $this->get($url);

    $payment = Payment::where('payable_type', SponsorshipEnquiry::class)->where('payable_id', $enquiry->id)->first();

    // A val_id that really was VALIDATED, but for a different (smaller) amount —
    // e.g. replayed from an unrelated cheap transaction against this tran_id.
    Http::fake([
        '*/validator/api/validationserverAPI.php*' => Http::response([
            'status' => 'VALID',
            'tran_id' => $payment->gateway_txn_id,
            'amount' => '10.00',
            'currency' => 'BDT',
        ]),
    ]);

    $this->post(route('payments.sslcommerz.ipn'), [
        'tran_id' => $payment->gateway_txn_id,
        'val_id' => 'mismatched-amount',
    ])->assertOk();

    expect($payment->fresh()->status)->toBe('failed');
    expect(Sponsor::where('enquiry_id', $enquiry->id)->exists())->toBeFalse();
});

it('leaves a sponsorship payment pending (not failed) when the gateway validation API is unreachable', function () {
    Http::fake([
        '*/gwprocess/v4/api.php' => Http::response(['GatewayPageURL' => 'https://sandbox.sslcommerz.com/EasyCheckOut/abc888']),
        '*/validator/api/validationserverAPI.php*' => Http::response([], 500),
    ]);

    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id, 'price' => 100000]);
    $enquiry = SponsorshipEnquiry::factory()->create(['sponsor_tier_id' => $tier->id, 'status' => 'confirmed']);

    $url = URL::signedRoute('payments.sponsorship.initiate', ['locale' => 'en', 'enquiry' => $enquiry->id]);
    $this->get($url);

    $payment = Payment::where('payable_type', SponsorshipEnquiry::class)->where('payable_id', $enquiry->id)->first();

    $this->post(route('payments.sslcommerz.ipn'), [
        'tran_id' => $payment->gateway_txn_id,
        'val_id' => 'whatever',
    ])->assertOk();

    expect($payment->fresh()->status)->toBe('pending');
});

it('marks the payment failed and does not create a sponsor when gateway verification fails', function () {
    Http::fake([
        '*/gwprocess/v4/api.php' => Http::response(['GatewayPageURL' => 'https://sandbox.sslcommerz.com/EasyCheckOut/xyz']),
        '*/validator/api/validationserverAPI.php*' => Http::response(['status' => 'INVALID']),
    ]);

    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id, 'price' => 50000]);
    $enquiry = SponsorshipEnquiry::factory()->create(['sponsor_tier_id' => $tier->id]);

    $url = URL::signedRoute('payments.sponsorship.initiate', ['locale' => 'en', 'enquiry' => $enquiry->id]);
    $this->get($url);

    $payment = Payment::where('payable_id', $enquiry->id)->where('payable_type', SponsorshipEnquiry::class)->first();

    $this->post(route('payments.sslcommerz.ipn'), [
        'tran_id' => $payment->gateway_txn_id,
        'val_id' => 'bad',
    ])->assertOk();

    expect($payment->fresh()->status)->toBe('failed');
    expect(Sponsor::where('enquiry_id', $enquiry->id)->exists())->toBeFalse();
});

it('rejects an unsigned sponsorship payment initiation link', function () {
    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id]);
    $enquiry = SponsorshipEnquiry::factory()->create(['sponsor_tier_id' => $tier->id]);

    $this->get("/en/sponsorship-opportunity/pay/{$enquiry->id}")->assertForbidden();
});
