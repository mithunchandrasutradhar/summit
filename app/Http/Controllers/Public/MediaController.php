<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\MediaCoverage;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(): View
    {
        $galleries = Gallery::with('related')
            ->latest()
            ->paginate(12, ['*'], 'galleries_page');

        $coverage = MediaCoverage::orderByDesc('published_at')->get();

        return view('media.index', compact('galleries', 'coverage'));
    }
}
