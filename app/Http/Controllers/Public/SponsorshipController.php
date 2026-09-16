<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\SponsorTier;
use Illuminate\View\View;

class SponsorshipController extends Controller
{
    public function sponsors(string $locale): View
    {
        $tiers = SponsorTier::forCurrentEvent()->orderBy('order')->get();

        $sponsors = Sponsor::published()
            ->with('tier')
            ->orderBy('order')
            ->get()
            ->groupBy(fn (Sponsor $sponsor) => $sponsor->tier?->name);

        return view('sponsorship.sponsors', compact('tiers', 'sponsors'));
    }

    public function opportunity(string $locale): View
    {
        $tiers = SponsorTier::active()->forCurrentEvent()->orderBy('order')->get();

        return view('sponsorship.opportunity', compact('tiers'));
    }
}
