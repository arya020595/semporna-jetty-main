<?php

namespace App\Http\Controllers\CompanyProfile;

use App\Actions\CompanyProfile\CreateBoat;
use App\Actions\CompanyProfile\CreateBoatman;
use App\Actions\CompanyProfile\CreateBoatmanOthers;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyProfile\FormStep1Request;
use App\Http\Resources\CompanyProfile\CompanyResource;
use App\Models\Boatman;
use App\Policies\NationalityPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class Step1Controller extends Controller
{
    public function edit(Request $request)
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();
        $company = $user->company->load("boat.fileable", "boat.boatman.fileable");

        $isAddNewBoat = $request->mode == "add-boat";
        return Inertia::render('CompanyProfile/Edit', [
            "title" => "Company Profile - Edit",
            "additional" => [
                "company" => (new CompanyResource($company))->toArray($request),
                "urlSubmit" => route("panel.company-profile.step1.update"),
                "urlSubmitBoat" => route("panel.company-profile.boat.update"),
                "urlDeleteBoat" => route("panel.company-profile.boat.delete", ['boat' => ':boatId']),
                "urlIndex" => route("panel.company-profile.index"),
                "isAddNewBoat" => $isAddNewBoat,
            ]
        ]);
    }

    public function update(FormStep1Request $request, CreateBoat $actionCreateBoat, CreateBoatman $actionCreateBoatman, CreateBoatmanOthers $actionCreateBoatmanOther)
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();
        /**
         * @var \App\Models\Company
         */
        $company = $user->company;

        DB::transaction(function () use ($company, $request, $actionCreateBoat, $actionCreateBoatman, $actionCreateBoatmanOther) {
            $arrData = $request->validated();

            $company->update([
                "registration_no" => $request->registration_no
            ]);

            // if (!isset($arrData["boat"]) || !$arrData["boat"]) {
            //     return $company;
            // }

            if (isset($arrData["boat"]) && $arrData["boat"]) {
                $boat = $actionCreateBoat->execute($company, $arrData["boat"]);

                $arrBoatman = $arrData["boat"]["boatman"] ?? [];
                $boatmanIds = [];
                foreach ($arrBoatman as $item) {
                    $boatman = $actionCreateBoatman->execute(
                        $boat,
                        $item,
                        Boatman::TYPE_BOATMAN
                    );
                    $boatmanIds[] = $boatman->id;
                }

                $boat->boatman()
                    ->where("type", Boatman::TYPE_BOATMAN)
                    ->whereNotIn("id", $boatmanIds)
                    ->delete();

                // assistant
                $arrAssistant = $arrData["boat"]["asst"] ?? [];
                $assistantIds = [];
                foreach ($arrAssistant as $item) {
                    $assistant = $actionCreateBoatman->execute(
                        $boat,
                        $item,
                        Boatman::TYPE_ASSISTANT
                    );
                    $assistantIds[] = $assistant->id;
                }

                $boat->boatman()
                    ->where("type", Boatman::TYPE_ASSISTANT)
                    ->whereNotIn("id", $assistantIds)
                    ->delete();
            }

            // Handle independent boatmen (Instructor, Divemaster, Guide)
            $arrBoatmanInstructor = $arrData["instructor"] ?? [];
            $boatmanIds = [];
            foreach ($arrBoatmanInstructor as $item) {
                $boatman = $actionCreateBoatmanOther->execute(
                    $company,
                    $item,
                    Boatman::TYPE_INSTRUCTOR
                );
                if ($boatman) {
                    $boatmanIds[] = $boatman->id;
                }
            }
            $query = $company->boatman()->where("type", Boatman::TYPE_INSTRUCTOR);
            if (!empty($boatmanIds)) {
                $query->whereNotIn("id", $boatmanIds);
            }
            $query->delete();

            $arrBoatmanDivemaster = $arrData["divemaster"] ?? [];
            $boatmanIds = [];
            foreach ($arrBoatmanDivemaster as $item) {
                $boatman = $actionCreateBoatmanOther->execute(
                    $company,
                    $item,
                    Boatman::TYPE_DIVEMASTER
                );
                if ($boatman) {
                    $boatmanIds[] = $boatman->id;
                }
            }
            $query = $company->boatman()->where("type", Boatman::TYPE_DIVEMASTER);
            if (!empty($boatmanIds)) {
                $query->whereNotIn("id", $boatmanIds);
            }
            $query->delete();

            $arrBoatmanGuide = $arrData["guide"] ?? [];
            $boatmanIds = [];
            foreach ($arrBoatmanGuide as $item) {
                $boatman = $actionCreateBoatmanOther->execute(
                    $company,
                    $item,
                    Boatman::TYPE_GUIDE
                );
                if ($boatman) {
                    $boatmanIds[] = $boatman->id;
                }
            }
            $query = $company->boatman()->where("type", Boatman::TYPE_GUIDE);
            if (!empty($boatmanIds)) {
                $query->whereNotIn("id", $boatmanIds);
            }
            $query->delete();

            $company->update([
                "is_completed" => 1
            ]);

            return $company;
        });

        return redirect()->route("panel.company-profile.index")
            ->with("message", [
                "status" => "success",
                "message" => "Data Updated!"
            ]);
    }
}
