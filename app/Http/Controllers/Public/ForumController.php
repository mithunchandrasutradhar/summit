<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\View\View;

class ForumController extends Controller
{
    public function index(string $locale): View
    {
        $faqs = Faq::active()->ofGroup('forum')->orderBy('order')->get();

        return view('forum.index', compact('faqs'));
    }

    public function register(string $locale): View
    {
        return view('forum.register');
    }
}
