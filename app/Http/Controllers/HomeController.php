<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct()
    {
        Auth::check();
        $this->middleware('guest')->except('logout');
    }


    public function index(Request $request)
    {
        return Inertia::render('Home', [
            "title" => "Welcome"
        ]);
    }
}
