<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ExhibitionBooth;
use App\Models\Exhibitor;
use Illuminate\View\View;

class ExhibitionController extends Controller
{
    public function index(string $locale): View
    {
        $exhibitors = Exhibitor::published()->with('booth')->orderBy('company_name')->get();
        $booths = ExhibitionBooth::forCurrentEvent()->orderBy('booth_no')->get();

        return view('exhibition.index', compact('exhibitors', 'booths'));
    }

    public function apply(string $locale): View
    {
        return view('exhibition.apply');
    }
}
