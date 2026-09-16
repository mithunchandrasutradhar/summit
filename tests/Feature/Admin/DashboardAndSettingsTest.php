<?php

use App\Filament\Resources\RegistrationResource\Pages\ListRegistrations;
use App\Mail\TemplatedMail;
use App\Models\AttendeeType;
use App\Models\AwardCategory;
use App\Models\Event;
use App\Models\EmailTemplate;
use App\Models\NotificationLog;
use App\Models\Registration;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('super_admin');
    $this->actingAs($this->admin);
});

it('renders the admin dashboard with its reporting widgets', function () {
    $event = Event::factory()->create(['is_current' => true]);
    $type = AttendeeType::factory()->create();
    Registration::factory()->create(['attendee_type_id' => $type->id, 'utm_source' => 'facebook']);
    AwardCategory::factory()->create(['event_id' => $event->id]);

    $this->get('/admin')
        ->assertOk()
        ->assertSee('Registrations')
        ->assertSee('Award Nominations')
        ->assertSee('Top UTM Sources')
        ->assertSee('facebook');
});

it('renders and saves the general settings page', function () {
    $this->get('/admin/manage-general-settings')->assertOk();

    Livewire::test(\App\Filament\Pages\ManageGeneralSettings::class)
        ->fillForm(['ga_measurement_id' => 'G-ABC123', 'site_tagline' => 'Empowering freelancers'])
        ->call('save');

    $settings = app(GeneralSettings::class);

    expect($settings->ga_measurement_id)->toBe('G-ABC123');
    expect($settings->site_tagline)->toBe('Empowering freelancers');
});

it('sends a bulk email to selected registrations via the reusable bulk action', function () {
    Mail::fake();

    $template = EmailTemplate::factory()->create(['key' => 'registration_reminder_manual']);
    $type = AttendeeType::factory()->create();
    $registration = Registration::factory()->create(['attendee_type_id' => $type->id]);

    Livewire::test(ListRegistrations::class)
        ->callTableBulkAction('sendBulkEmail', [$registration], data: ['email_template_id' => $template->id]);

    Mail::assertQueued(TemplatedMail::class);
    expect(NotificationLog::where('notifiable_type', Registration::class)
        ->where('notifiable_id', $registration->id)
        ->where('template_key', 'registration_reminder_manual')
        ->where('status', 'sent')
        ->exists())->toBeTrue();
});
