<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CampusProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampusProgramController extends Controller
{
    public function index(string $locale): View
    {
        $campusPrograms = CampusProgram::with('district')
            ->forCurrentEvent()
            ->orderBy('order')
            ->get();

        return view('campus-programs.index', compact('campusPrograms'));
    }

    public function show(string $locale, string $slug): View
    {
        $campusProgram = CampusProgram::with(['district', 'speakers', 'galleries.media'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('campus-programs.show', compact('campusProgram'));
    }

    public function storeLead(Request $request, string $locale, string $slug): RedirectResponse
    {
        $campusProgram = CampusProgram::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'type' => 'required|in:attendee_interest,campus_ambassador',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:2000',
        ]);

        $campusProgram->activationLeads()->create($data);

        return back()->with('lead_submitted', true);
    }
}
