<?php

namespace App\Actions\CompanyManifest;

use App\Actions\Concern\RetriesOnDuplicate;
use App\Models\Approvement;
use App\Models\Boatman;
use App\Models\Manifest;
use App\Models\ManifestDestination;
use App\Models\ManifestDestinationActivity;
use App\Models\ManifestFee;
use App\Models\RefActivity;
use App\Models\RefDestination;
use App\Models\RefNationality;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateManifest
{
    use RetriesOnDuplicate;

    public function execute(Manifest $manifest, array $arrData, User $user)
    {
        return $this->retryOnDuplicate(function () use ($manifest, $arrData, $user) {
            return DB::transaction(function () use ($manifest, $arrData, $user) {
                $isFinal = ($arrData["is_final"] ?? false);

                $departure = RefDestination::find($arrData["departure_id"]);

                $isRent = $arrData["is_rent"] ?? false;

                $oldVersion = $manifest->version;
                $version = $isFinal && in_array(
                    $manifest->status,
                    [
                        Manifest::STATUS_REJECTED,
                        Manifest::STATUS_AMEND
                    ]
                )
                    ? $manifest->version + 1
                    : $manifest->version;
                $versionIncremented = $version > $oldVersion;

                // Check if departure_date has changed - regenerate manifest number if changed
                $newDepartureDate = $arrData["departure_date"];
                $oldDepartureDate = $manifest->departure_date;

                $manifestNumberFields = [];

                // Only regenerate manifest number if departure date changed AND it hasn't been paid yet
                $hasPaidFees = $manifest->manifestFee()->where("status", ManifestFee::STATUS_PAID)->exists();

                if ($newDepartureDate !== $oldDepartureDate && !$hasPaidFees) {
                    // Departure date changed and not paid yet - regenerate manifest number
                    $arrNumber = (new GenerateNumbers)->execute("SM", Carbon::parse($newDepartureDate));
                    $manifestNumberFields = [
                        "form_number" => $arrNumber["number"] ?? "",
                        "form_date" => $arrNumber["date"] ?? "",
                        "sequence" => $arrNumber["sequence"] ?? "",
                    ];
                }

                $manifest->update(array_merge([
                    "type" => $isRent ? Manifest::TYPE_RENTAL : Manifest::TYPE_BY_COMPANY,
                    "is_dive_activity" => $arrData["is_dive_activity"] ?? 0,

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

                    "assistant_id" => $arrData["assistant_id"] ?? null,
                    "assistant_name" => $arrData["assistant_name"] ?? null,
                    "assistant_mate_no" => $arrData["assistant_mate_no"] ?? null,
                    "assistant_ic_no" => $arrData["assistant_ic_no"] ?? null,
                    "assistant_seaman_no" => $arrData["assistant_seaman_no"] ?? null,

                    "is_final" => $arrData["is_final"],

                    "updated_by" => $user->id,

                    "status" => $isFinal && $manifest->status != Manifest::STATUS_APPROVED
                        ? Manifest::STATUS_PENDING
                        : $manifest->status,

                    "jetty_approval_status" => $isFinal && $manifest->status == Manifest::STATUS_APPROVED
                        ? Manifest::STATUS_PENDING
                        : $manifest->jetty_approval_status,

                    "version" => $version
                ], $manifestNumberFields));


                $arrId = [];
                foreach ($arrData["destination"] as $value) {
                    $refDestination = RefDestination::find($value);
                    $manifestDestination = $manifest->manifestDestination()->updateOrCreate([
                        "ref_destination_id" => $refDestination->id
                    ], [
                        "ref_destination_id" => $refDestination->id,
                        "ref_destination_name" => $refDestination->title
                    ]);
                    $arrId[] = $manifestDestination->id;
                }

                $manifest->manifestDestination()
                    ->whereNotIn("id", $arrId)
                    ->delete();


                $this->createDestinationActivity($manifest, $arrData["destination_activity"]);
                $this->createBoatman($manifest, $arrData["instructor"], Boatman::TYPE_INSTRUCTOR);
                $this->createBoatman($manifest, $arrData["divemaster"], Boatman::TYPE_DIVEMASTER);
                $this->createBoatman($manifest, $arrData["guide"], Boatman::TYPE_GUIDE);

                $this->createPassenger($manifest, $arrData["passengers"] ?? [], 0);
                $this->createPassenger($manifest, $arrData["staff"] ?? [], 1);

                // If version incremented, carry over any previously APPROVED authorities to the new version
                if ($versionIncremented) {
                    $approvedRecords = $manifest->approvement()
                        ->where('version', $oldVersion)
                        ->where('status', Approvement::STATUS_APPROVED)
                        ->get();

                    foreach ($approvedRecords as $record) {
                        // Duplicate the approved record for the new version
                        $manifest->approvement()->create([
                            'user_id' => $record->user_id,
                            'role_id' => $record->role_id,
                            'status' => $record->status,
                            'comments' => $record->comments,
                            'date' => $record->date,
                            'version' => $version,
                        ]);
                    }

                    // It should NOT remain PENDING (which allows editing), but rather APPROVED_PROGRESS
                    if ($isFinal && $approvedRecords->isNotEmpty()) {
                        $manifest->update([
                            'status' => Manifest::STATUS_APPROVED_PROGRESS
                        ]);
                    }
                }

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

            if (!$arrValue["boatman_id"] && !$arrValue["name"]) {
                continue;
            }

            $manifestBoatman = $manifest->manifestBoatman()->updateOrCreate(
                $arrCheck,
                [
                    "boatman_id" => $arrValue["boatman_id"],
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

        $isPaymnetExists = $manifest->manifestFee()
            ->where("status", ManifestFee::STATUS_PAID)
            ->exists();

        $arrId = [];
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

            $guest = $manifest->guest()->find($arrValue["id"] ?? false);

            if ($guest) {
                // Existing guest - keep is_additional value
                $arrValue["is_additional"] = $guest->is_additional ?? 0;
            } else {
                // New guest - check if there's already a paid fee
                $arrValue["is_additional"] = $isPaymnetExists ? 1 : 0;
            }

            $arrValue["is_staff"] = $isStaff;

            $guest = $manifest->guest()->updateOrCreate([
                "id" => $arrValue["id"] ?? false
            ], $arrValue);

            if (!empty($arrValue['activity_ids'])) {
                $guest->activities()->sync($arrValue['activity_ids']);
            } else {
                // If activities are not changed
                $guest->activities()->sync([]);
            }

            $arrId[] = $guest->id;
        }

        $manifest->guest()
            ->whereNotIn("id", $arrId)
            ->where("is_staff", $isStaff)
            ->delete();
    }
}
