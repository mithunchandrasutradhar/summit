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

it('surfaces an is_filterable custom registration field as a real, working table filter', function () {
    $definition = FormDefinition::factory()->create(['key' => 'summit_registration']);
    FormField::factory()->create([
        'form_definition_id' => $definition->id,
        'field_key' => 'company',
        'label' => ['en' => 'Company', 'bn' => 'Company'],
        'is_filterable' => true,
        'is_system' => false,
    ]);

    $matching = \App\Models\Registration::factory()->create(['field_values' => ['name' => 'Jane', 'company' => 'Acme Corp']]);
    $other = \App\Models\Registration::factory()->create(['field_values' => ['name' => 'Sam', 'company' => 'Globex']]);

    // The dynamic column renders the custom field's value.
    $this->get('/admin/registrations')->assertOk()->assertSee('Acme Corp')->assertSee('Globex');

    // The dynamic filter actually narrows the query.
    $response = $this->get('/admin/registrations?tableFilters[field_company][value]=Acme');
    $response->assertOk();
    $response->assertSee($matching->reference_no);
    $response->assertDontSee($other->reference_no);
});
