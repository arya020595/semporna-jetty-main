<?php

namespace App\Http\Controllers\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyProfile\CompanyResource;
use App\Policies\CompanyProfilePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompanyController extends Controller
{
    protected CompanyProfilePolicy $policy;

    public function __construct(CompanyProfilePolicy $policy)
    {
        $this->policy = $policy;
    }

    public function index(Request $request)
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();
        if (!$this->policy->view($user)) {
            abort(403);
        }

        $company = $user->company->load([
            "boat.fileable",
            "boat.boatman.fileable",
            "boatmanInstructor",
            "boatmanDivemaster",
            "boatmanGuide"
        ]);

        if (!$company->is_completed) {
            return redirect()->route("panel.company-profile.step1.edit");
        }

        return Inertia::render('CompanyProfile/Index', [
            "title" => "Company Profile",
            "additional" => [
                "company" => (new CompanyResource($company))->toArray($request),
                // "urlShow" => route("panel.company-profile.show"),
                "urlEdit" => route("panel.company-profile.step1.edit"),
            ]
        ]);
    }

    // public function show(Request $request)
    // {
    //     /**
    //      * @var \App\Models\User
    //      */
    //     $user = Auth::user();
    //     if (!$this->policy->view($user)) {
    //         abort(403);
    //     }

    //     $company = $user->company->load("boat.fileable", "boat.boatman.fileable");

    //     if (!$company->is_completed) {
    //         return redirect()->route("panel.company-profile.step1.edit");
    //     }

    //     return Inertia::render('CompanyProfile/Show', [
    //         "title" => "Company Profile - Show",
    //         "additional" => [
    //             "currentStep" => $request->step ?? 1,
    //             "company" => (new CompanyResource($company))->toArray($request),
    //             "urlEdit" => route("panel.company-profile.step1.edit"),
    //             "urlBack" => route("panel.company-profile.index"),
    //         ]
    //     ]);
    // }
}
