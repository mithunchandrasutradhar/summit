<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AwardCategory;
use App\Models\AwardNomination;
use Illuminate\View\View;

class AwardController extends Controller
{
    public function index(string $locale): View
    {
        $categories = AwardCategory::active()->forCurrentEvent()->orderBy('order')->get();

        return view('awards.index', compact('categories'));
    }

    public function categories(string $locale): View
    {
        $categories = AwardCategory::active()->forCurrentEvent()->orderBy('order')->get();

        return view('awards.categories', compact('categories'));
    }

    public function nominate(string $locale): View
    {
        return view('awards.nominate');
    }

    public function shortlisted(string $locale): View
    {
        $nominations = AwardNomination::with('category')
            ->publiclyShortlisted()
            ->orderBy('category_id')
            ->get()
            ->groupBy(fn (AwardNomination $n) => $n->category?->name);

        return view('awards.shortlisted', compact('nominations'));
    }

    public function winners(string $locale): View
    {
        $nominations = AwardNomination::with('category')
            ->publiclyWon()
            ->orderBy('category_id')
            ->get()
            ->groupBy(fn (AwardNomination $n) => $n->category?->name);

        return view('awards.winners', compact('nominations'));
    }
}
