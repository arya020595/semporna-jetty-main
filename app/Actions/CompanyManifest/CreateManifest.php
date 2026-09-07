<?php

namespace App\Actions\CompanyManifest;

use App\Actions\Concern\RetriesOnDuplicate;
use App\Models\Boatman;
use App\Models\Manifest;
use App\Models\ManifestDestinationActivity;
use App\Models\RefActivity;
use App\Models\RefDestination;
use App\Models\RefNationality;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateManifest
{
    use RetriesOnDuplicate;

    public function execute(array $arrData, User $user)
    {
        return $this->retryOnDuplicate(function () use ($arrData, $user) {
            return DB::transaction(function () use ($arrData, $user) {

                $arrNumber = (new GenerateNumbers)->execute("SM", Carbon::parse($arrData["departure_date"]));

                $departure = RefDestination::find($arrData["departure_id"]);

                $isRent = $arrData["is_rent"] ?? false;
                $manifest = Manifest::create([
                    "user_id" => $user->id,
                    "type" => $isRent ? Manifest::TYPE_RENTAL : Manifest::TYPE_BY_COMPANY,
                    "is_dive_activity" => $arrData["is_dive_activity"] ?? 0,

                    "form_number" => $arrNumber["number"] ?? "",
                    "form_date" => $arrNumber["date"] ?? "",
                    "sequence" => $arrNumber["sequence"] ?? "",

                    "departure_date" => $arrData["departure_date"],
                    "departure_time" => $arrData["departure_time"],
                    "departure_id" => $departure->id,
                    "departure_name" => $departure->title,


                    "company_id" => $arrData["company_id"] ?? null,
                    "company_name" => $arrData["company_name"],

                    "boat_id" => $arrData["boat_id"] ?? null,
                    "boat_number" => $arrData["boat_number"],

                    "boatman_id" => $arrData["boatman_id"] ?? null,
                    "boatman_name" => $arrData["boatman_name"] ?? null,
                    "boatman_mate_no" => $arrData["boatman_mate_no"] ?? null,
                    "seaman_no" => $arrData["seaman_no"] ?? null,
                    "boatman_ic_no" => $arrData["boatman_ic_no"] ?? null,

                    "assistant_id" => ($arrData["assistant_id"] === 'other' ? null : ($arrData["assistant_id"] ?? null)),
                    "assistant_name" => $arrData["assistant_name"] ?? null,
                    "assistant_mate_no" => $arrData["assistant_mate_no"] ?? null,
                    "assistant_ic_no" => $arrData["assistant_ic_no"] ?? null,
                    "assistant_seaman_no" => $arrData["assistant_seaman_no"] ?? null,

                    "is_final" => $arrData["is_final"],
                    "status" => Manifest::STATUS_PENDING,
                    "jetty_approval_status" => Manifest::STATUS_PENDING,
                    "payment_status" => Manifest::PAYMENT_STATUS_PENDING,
                    "version" => 1,
                    "created_by" => $user->id
                ]);

                foreach ($arrData["destination"] as $value) {
                    $refDestination = RefDestination::find($value);

                    $manifest->destination()->attach($refDestination->id, [
                        "ref_destination_name" => $refDestination->title
                    ]);
                }


                $this->createDestinationActivity($manifest, $arrData["destination_activity"]);
                $this->createBoatman($manifest, $arrData["instructor"], Boatman::TYPE_INSTRUCTOR);
                $this->createBoatman($manifest, $arrData["divemaster"], Boatman::TYPE_DIVEMASTER);
                $this->createBoatman($manifest, $arrData["guide"], Boatman::TYPE_GUIDE);

                $manifest = $this->createPassenger($manifest, $arrData["passengers"] ?? [], 0);
                $manifest = $this->createPassenger($manifest, $arrData["staff"] ?? [], 1);

                return $manifest;
            });
        });
    }

    public function createDestinationActivity($manifest, $arrDestActivity)
    {
        $listManifestDestinations = $manifest->manifestDestination;
        $listActivity = RefActivity::all();

        $arrId = [];

        foreach ($arrDestActivity as $item) {
            $manifestDest = $listManifestDestinations->firstWhere("ref_destination_id", $item["destination_id"]);

            if (!$manifestDest) {
                continue;
            }

            foreach ($item["activity"] as $activityId) {
                $activity = $listActivity->firstWhere("id", $activityId);
                if (!$activity) {
                    continue;
                }

                $manDestActivity = ManifestDestinationActivity::updateOrCreate([
                    "manifest_destination_id" => $manifestDest->id,
                    "ref_activity_id" => $activity->id
                ], [
                    "manifest_destination_id" => $manifestDest->id,
                    "ref_activity_id" => $activity->id,
                    "activity_name" => $activity->title
                ]);
                $arrId[] = $manDestActivity->id;
            }
        }

        ManifestDestinationActivity::query()
            ->whereIn("manifest_destination_id", $listManifestDestinations->pluck("id"))
            ->whereNotIn("id", $arrId)
            ->delete();
    }

    public function createBoatman(Manifest $manifest, array $arrBoatman, $type)
    {
        $arrId = [];
        foreach ($arrBoatman as $arrValue) {
            $arrCheck = [
                "id" => $arrValue["id"] ?? false
            ];

            if (isset($arrValue["boatman_id"]) && $arrValue["boatman_id"]) {
                $arrCheck = [
                    "boatman_id" => $arrValue["boatman_id"],
                    "type" => $type
                ];
            }

            if (
                !($arrValue["boatman_id"] ?? false)
                && !($arrValue["name"] ?? false)
            ) {
                continue;
            }

            $manifestBoatman = $manifest->manifestBoatman()->updateOrCreate(
                $arrCheck,
                [
                    "boatman_id" => $arrValue["boatman_id"] ?? null,
                    "name" => $arrValue["name"],
                    "ic_no" => $arrValue["ic_no"],
                    "type" => $type
                ]
            );

            $arrId[] = $manifestBoatman->id;
        }

        $manifest->manifestBoatman()
            ->where("type", $type)
            ->whereNotIn("id", $arrId)
            ->delete();
    }

    public function createPassenger(Manifest $manifest, array $arrData, $isStaff = 0)
    {
        $arrNationality = RefNationality::all();
        $arrActivity = RefActivity::all();

        foreach ($arrData as $arrValue) {
            // Handle nationality
            if (!$arrValue["nationality_id"]) {
                // Others
                $arrValue["nationality_id"] = null;
            } else {
                // Select RefNationality
                $nationality = $arrNationality->find($arrValue["nationality_id"]);
                $arrValue["nationality_name"] = optional($nationality)->title;
            }

            // Handle multiple activities
            if (!empty($arrValue["activity_ids"]) && is_array($arrValue["activity_ids"])) {
                // Passengers
                $titles = [];
                foreach ($arrValue["activity_ids"] as $aid) {
                    $act = $arrActivity->find($aid);
                    if ($act) $titles[] = $act->title;
                }

                $arrValue["activity_name"] = !empty($titles) ? implode(", ", $titles) : null;
                $arrValue["activity_names"] = $arrValue["activity_name"];
            } else {
                // Staffs
                $arrValue["activity_name"] = null;
                $arrValue["activity_names"] = null;
            }

            $arrValue["is_staff"] = $isStaff;
            $guest = $manifest->guest()->create($arrValue);
            if (!empty($arrValue['activity_ids'])) {
                $guest->activities()->sync($arrValue['activity_ids']);
            } else {
                $guest->activities()->sync(null);
            }
        }

        return $manifest;
    }
}
