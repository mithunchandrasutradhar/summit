<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Speaker;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpeakerController extends Controller
{
    public function index(Request $request, string $locale): View
    {
        $speakers = Speaker::published()
            ->when($request->filled('country'), fn ($q) => $q->where('country', $request->string('country')))
            ->when($request->filled('expertise'), fn ($q) => $q->whereJsonContains('expertise', $request->string('expertise')->toString()))
            ->orderByDesc('is_featured')
            ->orderBy('order')
            ->get();

        $countries = Speaker::published()->whereNotNull('country')->distinct()->pluck('country')->sort()->values();
        $expertiseTags = Speaker::published()->pluck('expertise')->flatten(1)->filter()->unique()->sort()->values();

        return view('speakers.index', compact('speakers', 'countries', 'expertiseTags'));
    }

    public function show(string $locale, string $slug): View
    {
        $speaker = Speaker::published()
            ->with(['districts', 'campusPrograms'])
            ->where('slug', $slug)
            ->firstOrFail();

        $sessions = $speaker->belongsToMany(\App\Models\Session::class, 'session_speaker')
            ->published()
            ->orderBy('date')
            ->get();

        return view('speakers.show', compact('speaker', 'sessions'));
    }
}
