<?php

namespace App\Http\Controllers\CompanyProfile;

use App\Actions\CompanyProfile\CreateBoat;
use App\Actions\CompanyProfile\CreateBoatman;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyProfile\FormBoatRequest;
use App\Models\Boat;
use App\Models\Boatman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoatController extends Controller
{

    public function update(FormBoatRequest $request, CreateBoat $actionCreateBoat, CreateBoatman $actionCreateBoatman)
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();
        /**
         * @var \App\Models\Company
         */
        $company = $user->company;

        DB::transaction(function () use ($company, $request, $actionCreateBoat, $actionCreateBoatman) {
            $arrData = $request->validated();

            $boat = $actionCreateBoat->execute($company, $arrData);

            $arrBoatman = $arrData["boatman"] ?? [];
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
            $arrAssistant = $arrData["asst"] ?? [];
            $assistantIds = [];
            foreach ($arrAssistant as $item) {
                $hasName = isset($item['name']) && trim($item['name']) !== '';
                $hasIcNo = isset($item['ic_no']) && trim($item['ic_no']) !== '';

                // Skip if both name and ic_no are empty
                if (!$hasName && !$hasIcNo) {
                    continue;
                }

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

            return $boat;
        });

        return redirect()->route("panel.company-profile.step1.edit", ["mode" => "add-boat"])
            ->with("message", [
                "status" => "success",
                "message" => "Data Updated!"
            ]);
    }

    public function destroy(Boat $boat)
    {
        // $this->authorize('delete', $boat);

        $title = $boat->number;
        DB::transaction(function () use ($boat) {
            $boat->delete();
        });

        return redirect()->route("panel.company-profile.step1.edit")
            ->with("message", [
                "status" => "success",
                "message" => "Delete Boat $title Success!"
            ]);
    }
}
