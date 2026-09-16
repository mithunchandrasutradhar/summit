<?php

use App\Filament\Resources\SponsorshipEnquiryResource\Pages\ListSponsorshipEnquiries;
use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use App\Models\Event;
use App\Models\ExhibitionBooth;
use App\Models\Exhibitor;
use App\Models\ExhibitorApplication;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Models\Sponsor;
use App\Models\SponsorshipEnquiry;
use App\Models\SponsorTier;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['super_admin', 'admin', 'sponsorship_manager', 'exhibition_manager'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }

    $this->event = Event::factory()->create(['is_current' => true]);
});

it('renders the sponsor tiers, sponsors and sponsorship enquiries admin pages for a sponsorship manager', function () {
    $manager = User::factory()->create();
    $manager->assignRole('sponsorship_manager');
    $this->actingAs($manager);

    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id]);
    $sponsor = Sponsor::factory()->create(['tier_id' => $tier->id]);
    $enquiry = SponsorshipEnquiry::factory()->create(['sponsor_tier_id' => $tier->id]);
    $payment = Payment::factory()->create(['payable_type' => SponsorshipEnquiry::class, 'payable_id' => $enquiry->id]);

    $this->get('/admin/sponsor-tiers')->assertOk();
    $this->get('/admin/sponsor-tiers/create')->assertOk();
    $this->get("/admin/sponsor-tiers/{$tier->id}/edit")->assertOk();

    $this->get('/admin/sponsors')->assertOk();
    $this->get("/admin/sponsors/{$sponsor->id}/edit")->assertOk();

    $this->get('/admin/sponsorship-enquiries')->assertOk();
    $this->get("/admin/sponsorship-enquiries/{$enquiry->id}/edit")->assertOk()->assertSee($payment->gateway_txn_id);
});

it('denies sponsorship resources to an exhibition manager', function () {
    $manager = User::factory()->create();
    $manager->assignRole('exhibition_manager');
    $this->actingAs($manager);

    $this->get('/admin/sponsor-tiers')->assertForbidden();
    $this->get('/admin/sponsorship-enquiries')->assertForbidden();
    $this->get('/admin/sponsors')->assertForbidden();
});

it('renders the exhibition booths, exhibitors and exhibitor applications admin pages for an exhibition manager', function () {
    $manager = User::factory()->create();
    $manager->assignRole('exhibition_manager');
    $this->actingAs($manager);

    $booth = ExhibitionBooth::factory()->create(['event_id' => $this->event->id]);
    $application = ExhibitorApplication::factory()->create(['preferred_booth_id' => $booth->id]);
    $exhibitor = Exhibitor::factory()->create(['application_id' => $application->id, 'booth_id' => $booth->id]);
    Payment::factory()->create(['payable_type' => ExhibitorApplication::class, 'payable_id' => $application->id]);

    $this->get('/admin/exhibition-booths')->assertOk();
    $this->get('/admin/exhibition-booths/create')->assertOk();
    $this->get("/admin/exhibition-booths/{$booth->id}/edit")->assertOk();

    $this->get('/admin/exhibitors')->assertOk();
    $this->get("/admin/exhibitors/{$exhibitor->id}/edit")->assertOk();

    $this->get('/admin/exhibitor-applications')->assertOk();
    $this->get("/admin/exhibitor-applications/{$application->id}/edit")->assertOk();
});

it('denies exhibition resources to a sponsorship manager', function () {
    $manager = User::factory()->create();
    $manager->assignRole('sponsorship_manager');
    $this->actingAs($manager);

    $this->get('/admin/exhibition-booths')->assertForbidden();
    $this->get('/admin/exhibitor-applications')->assertForbidden();
    $this->get('/admin/exhibitors')->assertForbidden();
});

it('sends a sponsorship payment link via the enquiry list table action', function () {
    Mail::fake();

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    $this->actingAs($admin);

    EmailTemplate::factory()->create(['key' => 'sponsorship_payment_link']);

    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id]);
    $enquiry = SponsorshipEnquiry::factory()->create(['sponsor_tier_id' => $tier->id]);

    Livewire::test(ListSponsorshipEnquiries::class)
        ->callTableAction('sendPaymentLink', $enquiry);

    Mail::assertQueued(TemplatedMail::class);
    expect(NotificationLog::where('notifiable_type', SponsorshipEnquiry::class)
        ->where('notifiable_id', $enquiry->id)
        ->where('template_key', 'sponsorship_payment_link')
        ->where('status', 'sent')
        ->exists())->toBeTrue();
});
