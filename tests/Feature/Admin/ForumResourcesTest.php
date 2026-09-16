<?php

use App\Models\ForumMember;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['super_admin', 'admin', 'content_editor'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

it('renders the forum members list and edit pages for an admin', function () {
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    $this->actingAs($admin);

    $member = ForumMember::factory()->create();

    $this->get('/admin/forum-members')->assertOk();
    $this->get("/admin/forum-members/{$member->id}/edit")->assertOk();
});

it('denies forum members access to a content editor', function () {
    $editor = User::factory()->create();
    $editor->assignRole('content_editor');
    $this->actingAs($editor);

    $this->get('/admin/forum-members')->assertForbidden();
});
