<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistrictController extends Controller
{
    public function index(string $locale): View
    {
        $districts = District::with('division')
            ->forCurrentEvent()
            ->orderBy('order')
            ->get();

        return view('districts.index', compact('districts'));
    }

    public function show(string $locale, string $slug): View
    {
        $district = District::with(['division', 'speakers', 'galleries.media'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('districts.show', compact('district'));
    }

    public function storeLead(Request $request, string $locale, string $slug): RedirectResponse
    {
        $district = District::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'type' => 'required|in:attendee_interest,campus_ambassador',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:2000',
        ]);

        $district->activationLeads()->create($data);

        return back()->with('lead_submitted', true);
    }
}
