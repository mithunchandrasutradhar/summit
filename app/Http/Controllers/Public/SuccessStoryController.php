<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\View\View;

class SuccessStoryController extends Controller
{
    public function index(): View
    {
        $stories = SuccessStory::published()
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('success-stories.index', compact('stories'));
    }

    // See PageController::show() docblock — {locale} must be declared even though unused.
    public function show(string $locale, string $slug): View
    {
        $story = SuccessStory::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('success-stories.show', compact('story'));
    }
}
