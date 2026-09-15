<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\View\View;

class PartnerController extends Controller
{
    public function index(): View
    {
        $partnersByCategory = Partner::published()
            ->orderBy('order')
            ->get()
            ->groupBy('category');

        return view('partners.index', compact('partnersByCategory'));
    }
}
