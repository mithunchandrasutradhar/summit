<?php

use App\Models\Registration;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('super_admin');
});

it('redirects an unauthenticated gate scanner request to login', function () {
    $this->get('/checkin')->assertRedirect();
});

it('checks in a valid registration and marks the second scan as already checked in', function () {
    $registration = Registration::factory()->create(['qr_token' => 'test-token-123']);

    $this->actingAs($this->admin)
        ->get('/checkin/test-token-123')
        ->assertOk()
        ->assertSee('Checked In');

    expect($registration->fresh()->checked_in_at)->not->toBeNull();

    $this->actingAs($this->admin)
        ->get('/checkin/test-token-123')
        ->assertOk()
        ->assertSee('Already Checked In');
});

it('shows a not-found result for an unknown token', function () {
    $this->actingAs($this->admin)
        ->get('/checkin/does-not-exist')
        ->assertOk()
        ->assertSee('Ticket Not Found');
});

it('shows a cancelled result for a cancelled registration', function () {
    Registration::factory()->create(['qr_token' => 'cancelled-token', 'status' => 'cancelled']);

    $this->actingAs($this->admin)
        ->get('/checkin/cancelled-token')
        ->assertOk()
        ->assertSee('Registration Cancelled');
});
