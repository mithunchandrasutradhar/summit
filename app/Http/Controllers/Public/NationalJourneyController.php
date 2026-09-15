<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CampaignStat;
use App\Models\Division;
use App\Models\Event;
use Illuminate\View\View;

class NationalJourneyController extends Controller
{
    public function index(string $locale): View
    {
        $event = Event::current();

        $divisions = Division::with(['districts' => fn ($q) => $q->forCurrentEvent()->orderBy('order')])
            ->orderBy('order')
            ->get();

        $stats = $event
            ? CampaignStat::where('event_id', $event->id)->pluck('value', 'key')
            : collect();

        return view('national-journey.index', compact('divisions', 'stats', 'event'));
    }
}
