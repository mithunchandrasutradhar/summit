<?php

use App\Models\Registration;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['super_admin', 'admin', 'content_editor', 'sponsorship_manager', 'exhibition_manager', 'awards_jury'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

it('allows super_admin and admin to access registrations', function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    Registration::factory()->create();

    $this->get('/admin/registrations')->assertOk();
})->with(['super_admin', 'admin']);

it('denies registrations access to every other admin role', function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->get('/admin/registrations')->assertForbidden();
})->with(['content_editor', 'sponsorship_manager', 'exhibition_manager', 'awards_jury']);

it('allows only super_admin to manage Filament Shield roles', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super_admin');
    $this->actingAs($superAdmin);

    $this->get('/admin/shield/roles')->assertOk();
});

it('denies every non-super_admin role access to Filament Shield roles', function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->get('/admin/shield/roles')->assertForbidden();
})->with(['admin', 'content_editor', 'sponsorship_manager', 'exhibition_manager', 'awards_jury']);

it('allows only super_admin to manage admin users', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super_admin');
    $this->actingAs($superAdmin);

    $this->get('/admin/users')->assertOk();
    $this->get('/admin/users/create')->assertOk();
});

it('denies every non-super_admin role access to admin user management', function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->get('/admin/users')->assertForbidden();
})->with(['admin', 'content_editor', 'sponsorship_manager', 'exhibition_manager', 'awards_jury']);
