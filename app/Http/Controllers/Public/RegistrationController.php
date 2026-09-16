<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(string $locale): View
    {
        return view('register.index');
    }
}
