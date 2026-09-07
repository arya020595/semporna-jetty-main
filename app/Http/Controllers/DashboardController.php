<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\GetTotalByPeriode;
use App\Actions\Dashboard\GetTotalByTemplate;
use App\Http\Controllers\Controller;
use App\Http\Requests\DashboardRequest;
use App\Models\User;
use App\Policies\DashboardPolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $policy;

    public function __construct(DashboardPolicy $policy)
    {
        $this->policy = $policy;
    }

    public function index(GetTotalByTemplate $actionTotalByTemplate, GetTotalByPeriode $actionTotalByPeriode, DashboardRequest $request)
    {
        return Inertia::render('Dashboard', [
            "title" => "Dashboard",
            "additional" => []
        ]);
    }
}
