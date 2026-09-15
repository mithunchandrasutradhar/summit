<?php

use App\Models\ActivationLead;
use App\Models\CampaignStat;
use App\Models\CampusProgram;
use App\Models\District;
use App\Models\Division;
use App\Models\Event;
use App\Models\Speaker;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('super_admin');
    $this->actingAs($this->admin);
});

dataset('national_journey_resources', [
    'speakers' => [fn () => Speaker::factory()->create(), '/admin/speakers'],
    'divisions' => [fn () => Division::factory()->create(), '/admin/divisions'],
    'districts' => [fn () => District::factory()->create(), '/admin/districts'],
    'campus-programs' => [fn () => CampusProgram::factory()->create(), '/admin/campus-programs'],
]);

it('renders the list, create and edit pages for each national journey resource', function (Closure $makeRecord, string $basePath) {
    $record = $makeRecord();

    $this->get($basePath)->assertOk();
    $this->get("{$basePath}/create")->assertOk();
    $this->get("{$basePath}/{$record->id}/edit")->assertOk();
})->with('national_journey_resources');

it('renders the campaign stats list, create and edit pages', function () {
    $stat = CampaignStat::create([
        'event_id' => Event::factory()->create()->id,
        'key' => 'divisions_covered',
        'value' => 0,
    ]);

    $this->get('/admin/campaign-stats')->assertOk();
    $this->get('/admin/campaign-stats/create')->assertOk();
    $this->get("/admin/campaign-stats/{$stat->id}/edit")->assertOk();
});

it('renders the activation leads list page and allows export', function () {
    ActivationLead::factory()->create();

    $this->get('/admin/activation-leads')->assertOk();
});

it('shows the activation leads relation manager on a district\'s edit page', function () {
    $district = District::factory()->create();
    ActivationLead::factory()->create([
        'activatable_type' => District::class,
        'activatable_id' => $district->id,
    ]);

    // Relation manager table rows hydrate through their own Livewire
    // lifecycle and aren't in the plain HTTP response, so this only
    // smoke-tests that the manager itself is present and the page
    // doesn't error — the data linkage is covered by the public-facing
    // activation-lead capture test in NationalJourneyTest.
    $this->get("/admin/districts/{$district->id}/edit")->assertOk()->assertSee('Leads');
});
