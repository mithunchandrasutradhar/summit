<?php

use App\Models\AttendeeType;
use App\Models\EmailTemplate;
use App\Models\Registration;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('super_admin');
    $this->actingAs($this->admin);
});

it('renders the attendee types list, create and edit pages', function () {
    $type = AttendeeType::factory()->create();

    $this->get('/admin/attendee-types')->assertOk();
    $this->get('/admin/attendee-types/create')->assertOk();
    $this->get("/admin/attendee-types/{$type->id}/edit")->assertOk();
});

it('renders the email templates list and edit pages', function () {
    $template = EmailTemplate::factory()->create();

    $this->get('/admin/email-templates')->assertOk();
    $this->get("/admin/email-templates/{$template->id}/edit")->assertOk();
});

it('renders the registrations list and edit pages', function () {
    $registration = Registration::factory()->create();

    $this->get('/admin/registrations')->assertOk();
    $this->get("/admin/registrations/{$registration->id}/edit")->assertOk();
});
