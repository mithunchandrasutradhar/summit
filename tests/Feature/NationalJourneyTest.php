<?php

use App\Models\ActivationLead;
use App\Models\CampusProgram;
use App\Models\District;
use App\Models\Division;
use App\Models\Event;

beforeEach(function () {
    $this->event = Event::factory()->create(['is_current' => true]);
});

it('serves the national journey overview page', function () {
    $this->get('/en/national-journey')->assertOk()->assertSee('National Journey');
});

it('lists and shows a division', function () {
    $division = Division::factory()->create();
    District::factory()->create(['division_id' => $division->id, 'event_id' => $this->event->id]);

    $this->get('/en/divisional-summits')->assertOk()->assertSee($division->name);
    $this->get("/en/divisional-summits/{$division->slug}")->assertOk()->assertSee($division->name);
});

it('lists and shows a district roadshow', function () {
    $district = District::factory()->create(['event_id' => $this->event->id]);

    $this->get('/en/district-roadshows')->assertOk()->assertSee($district->name);
    $this->get("/en/district-roadshows/{$district->slug}")->assertOk()->assertSee($district->name);
});

it('captures an activation lead from a district roadshow page', function () {
    $district = District::factory()->create(['event_id' => $this->event->id]);

    $this->post("/en/district-roadshows/{$district->slug}/leads", [
        'type' => 'attendee_interest',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => '01700000000',
        'message' => 'Looking forward to it!',
    ])->assertRedirect();

    expect(ActivationLead::where('email', 'jane@example.com')->exists())->toBeTrue();

    $lead = ActivationLead::where('email', 'jane@example.com')->first();
    expect($lead->activatable_type)->toBe(District::class);
    expect($lead->activatable_id)->toBe($district->id);
});

it('lists and shows a campus program, and accepts a campus ambassador lead', function () {
    $campus = CampusProgram::factory()->create(['event_id' => $this->event->id]);

    $this->get('/en/campus-programs')->assertOk()->assertSee($campus->institution_name);
    $this->get("/en/campus-programs/{$campus->slug}")->assertOk()->assertSee($campus->institution_name);

    $this->post("/en/campus-programs/{$campus->slug}/leads", [
        'type' => 'campus_ambassador',
        'name' => 'Ambassador Applicant',
        'email' => 'ambassador@example.com',
    ])->assertRedirect();

    $lead = ActivationLead::where('email', 'ambassador@example.com')->first();
    expect($lead)->not->toBeNull();
    expect($lead->type)->toBe('campus_ambassador');
    expect($lead->activatable_type)->toBe(CampusProgram::class);
});

it('recomputes campaign stats via the console command, skipping manually overridden values', function () {
    $division = Division::factory()->create();
    District::factory()->count(2)->create([
        'division_id' => $division->id,
        'event_id' => $this->event->id,
        'status' => 'completed',
        'participants_count' => 100,
    ]);

    \App\Models\CampaignStat::create([
        'event_id' => $this->event->id,
        'key' => 'participants_reached',
        'value' => 99999,
        'is_manual_override' => true,
    ]);

    $this->artisan('app:compute-campaign-stats')->assertSuccessful();

    $stats = \App\Models\CampaignStat::where('event_id', $this->event->id)->pluck('value', 'key');

    expect($stats['divisions_covered'])->toBe(1);
    expect($stats['districts_covered'])->toBe(2);
    expect($stats['participants_reached'])->toBe(99999); // untouched — manually overridden
});
