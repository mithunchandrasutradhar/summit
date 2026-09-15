<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\View\View;

class DivisionController extends Controller
{
    public function index(string $locale): View
    {
        $divisions = Division::with(['districts' => fn ($q) => $q->forCurrentEvent()])
            ->orderBy('order')
            ->get();

        return view('divisions.index', compact('divisions'));
    }

    public function show(string $locale, string $slug): View
    {
        $division = Division::where('slug', $slug)->firstOrFail();

        $districts = $division->districts()
            ->forCurrentEvent()
            ->orderBy('order')
            ->get();

        return view('divisions.show', compact('division', 'districts'));
    }
}
