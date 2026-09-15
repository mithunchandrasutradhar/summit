<?php

use App\Models\FormDefinition;
use App\Models\FormField;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('super_admin');
    $this->actingAs($this->admin);
});

it('renders the form definitions list, create and edit pages', function () {
    $definition = FormDefinition::factory()->create();

    $this->get('/admin/form-definitions')->assertOk();
    $this->get('/admin/form-definitions/create')->assertOk();
    $this->get("/admin/form-definitions/{$definition->id}/edit")->assertOk();
});

it('shows the fields relation manager on a form definition\'s edit page', function () {
    $definition = FormDefinition::factory()->create();
    FormField::factory()->create(['form_definition_id' => $definition->id]);

    $this->get("/admin/form-definitions/{$definition->id}/edit")->assertOk()->assertSee('Fields');
});
