<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Session;
use Illuminate\View\View;
use Spatie\SchemaOrg\EventAttendanceModeEnumeration;
use Spatie\SchemaOrg\EventStatusType;
use Spatie\SchemaOrg\Schema;

class GrandSummitController extends Controller
{
    public function index(string $locale): View
    {
        $event = Event::current();

        $announcements = Announcement::currentlyActive()
            ->where(fn ($q) => $q->whereNull('event_id')->orWhere('event_id', $event?->id))
            ->latest()
            ->get();

        $highlightSessions = Session::published()
            ->forCurrentEvent()
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(4)
            ->get();

        $eventSchema = $event ? $this->eventSchema($event) : null;

        return view('grand-summit.index', compact('event', 'announcements', 'highlightSessions', 'eventSchema'));
    }

    protected function eventSchema(Event $event): string
    {
        $schema = Schema::event()
            ->name($event->name)
            ->description($event->description)
            ->eventAttendanceMode(EventAttendanceModeEnumeration::OfflineEventAttendanceMode)
            ->eventStatus(EventStatusType::EventScheduled)
            ->startDate($event->date_start?->toIso8601String())
            ->endDate($event->date_end?->toIso8601String());

        if ($event->venue_name) {
            $place = Schema::place()->name($event->venue_name);

            if ($event->venue_address) {
                $place = $place->address($event->venue_address);
            }

            $schema = $schema->location($place);
        }

        return $schema->toScript();
    }
}
