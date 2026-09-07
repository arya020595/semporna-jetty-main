<?php

namespace App\Http\Controllers\CompanyProfile;

use App\Actions\CompanyProfile\CreateBoatmanOthers;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyProfile\FormStep2Request;
use App\Http\Resources\CompanyProfile\CompanyResource;
use App\Models\Boatman;
use App\Policies\NationalityPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class Step2Controller extends Controller
{
    protected $nationalityPolicy;

    public function __construct(
        NationalityPolicy $nationalityPolicy
    ) {
        $this->nationalityPolicy = $nationalityPolicy;
    }

    public function edit(Request $request)
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();
        $company = $user->company;

        return Inertia::render('CompanyProfile/EditStep2', [
            "title" => "Company Profile - Edit",
            "additional" => [
                "company" => (new CompanyResource($company))->toArray($request),
                "urlSubmit" => route("panel.company-profile.step2.update"),
                "urlBack" => route("panel.company-profile.step1.edit"),
            ]
        ]);
    }

    public function update(FormStep2Request $request, CreateBoatmanOthers $actionCreateBoatmanOther)
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();
        $company = $user->company;

        DB::transaction(function () use ($company, $request, $actionCreateBoatmanOther) {
            $arrData = $request->validated();

            $arrBoatman = $arrData["instructor"] ?? [];
            $boatmanIds = [];
            foreach ($arrBoatman as $item) {
                $boatman = $actionCreateBoatmanOther->execute(
                    $company,
                    $item,
                    Boatman::TYPE_INSTRUCTOR
                );
                if ($boatman) {
                    $boatmanIds[] = $boatman->id;
                }
            }

            $company->boatman()
                ->where("type", Boatman::TYPE_INSTRUCTOR)
                ->whereNotIn("id", $boatmanIds)
                ->delete();

            // assistant
            $arrBoatman = $arrData["divemaster"] ?? [];
            $boatmanIds = [];
            foreach ($arrBoatman as $item) {
                $boatman = $actionCreateBoatmanOther->execute(
                    $company,
                    $item,
                    Boatman::TYPE_DIVEMASTER
                );
                if ($boatman) {
                    $boatmanIds[] = $boatman->id;
                }
            }

            $company->boatman()
                ->where("type", Boatman::TYPE_DIVEMASTER)
                ->whereNotIn("id", $boatmanIds)
                ->delete();

            $arrBoatman = $arrData["guide"] ?? [];
            $boatmanIds = [];
            foreach ($arrBoatman as $item) {
                $boatman = $actionCreateBoatmanOther->execute(
                    $company,
                    $item,
                    Boatman::TYPE_GUIDE
                );
                if ($boatman) {
                    $boatmanIds[] = $boatman->id;
                }
            }

            $company->boatman()
                ->where("type", Boatman::TYPE_GUIDE)
                ->whereNotIn("id", $boatmanIds)
                ->delete();

            $company->update([
                "is_completed" => 1
            ]);
        });

        return redirect()->route("panel.company-profile.index")
            ->with("message", [
                "status" => "success",
                "message" => "Update Success!"
            ]);
    }
}
