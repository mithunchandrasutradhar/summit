<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Every action in the locale-prefixed route group must declare the
     * {locale} parameter even when unused — Laravel binds controller
     * method parameters to route segments by name, and falls back to
     * positional binding for an unmatched primitive parameter, which
     * would otherwise hand this method the locale value instead of slug.
     */
    public function show(string $locale, string $slug): View
    {
        $page = Page::where('slug', $slug)->where('is_published', true)->firstOrFail();

        return view('pages.show', compact('page'));
    }
}
