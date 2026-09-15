<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Session;
use Illuminate\View\View;

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

        return view('grand-summit.index', compact('event', 'announcements', 'highlightSessions'));
    }
}
