<?php

use App\Models\Announcement;
use App\Models\Hall;
use App\Models\Session;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('super_admin');
    $this->actingAs($this->admin);
});

dataset('grand_summit_resources', [
    'halls' => [fn () => Hall::factory()->create(), '/admin/halls'],
    'sessions' => [fn () => Session::factory()->create(), '/admin/sessions'],
    'announcements' => [fn () => Announcement::factory()->create(), '/admin/announcements'],
]);

it('renders the list, create and edit pages for each grand summit resource', function (Closure $makeRecord, string $basePath) {
    $record = $makeRecord();

    $this->get($basePath)->assertOk();
    $this->get("{$basePath}/create")->assertOk();
    $this->get("{$basePath}/{$record->id}/edit")->assertOk();
})->with('grand_summit_resources');
