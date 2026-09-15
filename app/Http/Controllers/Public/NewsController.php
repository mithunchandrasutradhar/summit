<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $posts = NewsPost::published()
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('news.index', compact('posts'));
    }

    // See PageController::show() docblock — {locale} must be declared even though unused.
    public function show(string $locale, string $slug): View
    {
        $post = NewsPost::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('news.show', compact('post'));
    }
}
