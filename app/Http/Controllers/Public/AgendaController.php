<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Session;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event as IcsEvent;

class AgendaController extends Controller
{
    public function index(string $locale): View
    {
        $event = Event::current();

        $announcements = Announcement::currentlyActive()
            ->where(fn ($q) => $q->whereNull('event_id')->orWhere('event_id', $event?->id))
            ->latest()
            ->get();

        return view('agenda.index', compact('announcements'));
    }

    public function show(string $locale, string $slug): View
    {
        $session = Session::with(['hall', 'speakers', 'event'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('agenda.show', compact('session'));
    }

    public function ics(string $locale, string $slug): Response
    {
        $session = Session::published()->where('slug', $slug)->firstOrFail();

        $startsAt = $session->startsAt();
        $endsAt = $session->endsAt();

        $calendar = Calendar::create($session->title)
            ->event(function (IcsEvent $event) use ($session, $startsAt, $endsAt) {
                $event->name($session->title)
                    ->description(strip_tags((string) $session->description));

                if ($startsAt) {
                    $event->startsAt($startsAt);
                }

                if ($endsAt) {
                    $event->endsAt($endsAt);
                }

                if ($session->hall) {
                    $event->address($session->hall->name);
                }
            });

        return response($calendar->get(), 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$session->slug.'.ics"',
        ]);
    }
}
