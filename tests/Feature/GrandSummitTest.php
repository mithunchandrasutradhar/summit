<?php

use App\Livewire\Agenda as AgendaComponent;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Hall;
use App\Models\Session;
use App\Models\Speaker;
use Livewire\Livewire;

beforeEach(function () {
    $this->event = Event::factory()->create(['is_current' => true]);
});

it('serves the grand summit hub page with an active announcement', function () {
    Announcement::factory()->create([
        'event_id' => $this->event->id,
        'title' => 'Schedule Update',
        'message' => 'Hall B session moved to Hall A.',
    ]);

    $this->get('/en/grand-summit')
        ->assertOk()
        ->assertSee('Schedule Update')
        ->assertSee('Hall B session moved to Hall A.');
});

it('does not show an inactive or out-of-window announcement', function () {
    Announcement::factory()->create([
        'event_id' => $this->event->id,
        'title' => 'Old Announcement',
        'is_active' => false,
    ]);

    $this->get('/en/grand-summit')->assertOk()->assertDontSee('Old Announcement');
});

it('serves the agenda page and filters sessions by type via the livewire component', function () {
    $hall = Hall::factory()->create(['event_id' => $this->event->id]);
    $keynote = Session::factory()->create(['event_id' => $this->event->id, 'hall_id' => $hall->id, 'type' => 'keynote']);
    $workshop = Session::factory()->create(['event_id' => $this->event->id, 'hall_id' => $hall->id, 'type' => 'workshop']);

    $this->get('/en/agenda')->assertOk();

    Livewire::test(AgendaComponent::class)
        ->assertSee($keynote->title)
        ->assertSee($workshop->title)
        ->set('type', 'keynote')
        ->assertSee($keynote->title)
        ->assertDontSee($workshop->title);
});

it('shows a session detail page and offers an ics download', function () {
    $session = Session::factory()->create([
        'event_id' => $this->event->id,
        'date' => now()->addMonth()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
    ]);

    $this->get("/en/agenda/{$session->slug}")->assertOk()->assertSee($session->title);

    $response = $this->get("/en/agenda/{$session->slug}/calendar.ics");
    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
    expect($response->getContent())->toContain('BEGIN:VCALENDAR');
});

it('lists speakers, filters by country, and shows a profile with linked sessions', function () {
    $bd = Speaker::factory()->create(['country' => 'Bangladesh', 'name' => 'Bangladeshi Speaker']);
    $us = Speaker::factory()->create(['country' => 'United States', 'name' => 'US Speaker']);
    $session = Session::factory()->create(['event_id' => $this->event->id]);
    $session->speakers()->attach($bd->id, ['role' => 'speaker']);

    $this->get('/en/speakers')->assertOk()->assertSee('Bangladeshi Speaker')->assertSee('US Speaker');
    $this->get('/en/speakers?country=Bangladesh')->assertOk()->assertSee('Bangladeshi Speaker')->assertDontSee('US Speaker');

    $this->get("/en/speaker/{$bd->slug}")->assertOk()->assertSee($bd->name)->assertSee($session->title);
});
