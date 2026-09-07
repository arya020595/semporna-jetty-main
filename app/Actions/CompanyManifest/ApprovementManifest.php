<?php

namespace App\Actions\CompanyManifest;

use App\Models\Approvement;
use App\Models\Manifest;
use App\Models\RefDestination;
use App\Models\User;
use Carbon\Carbon;

class ApprovementManifest
{

    public function execute(Manifest $manifest, User $userAuth, array $arrRequest)
    {
        $userRole = $userAuth->activeRole();
        $approvement = $manifest->approvement()->updateOrCreate(
            [
                "role_id"  => $userRole->id,
                "version"  => $manifest->version,
            ],
            [
                "user_id"  => $userAuth->id,
                "role_id"  => $userRole->id,
                "date"     => Carbon::now()->format("Y-m-d H:i:s"),
                "status"   => $arrRequest["status"],
                "comments" => $arrRequest["comments"] ?? "",
                "version"  => $manifest->version,
            ]
        );

        if (
            $approvement->status == Approvement::STATUS_APPROVED
        ) {
            $isSabahParks = $this->checkSabahParks($manifest);
            $manifest->update([
                "status" => $this->checkApprove($manifest, $isSabahParks)
                    ? Manifest::STATUS_APPROVED
                    : Manifest::STATUS_APPROVED_PROGRESS
            ]);
        }

        if ($approvement->status == Approvement::STATUS_REJECTED) {
            $manifest->update([
                "status" => Manifest::STATUS_REJECTED
            ]);
        }

        if ($approvement->status == Approvement::STATUS_AMEND) {
            $manifest->update([
                "status" => Manifest::STATUS_AMEND
            ]);
        }

        return $approvement;
    }

    protected function checkApprove(Manifest $manifest, bool $isSabahParks)
    {
        $arrRoleId = [
            User::ROLE_JABATAN_LAUT,
            User::ROLE_JABATAN_PELABUHAN,
            User::ROLE_PDRM,
        ];

        foreach ($arrRoleId as $roleId) {
            $isApprove = $manifest->approvement
                ->where("status", Manifest::STATUS_APPROVED)
                ->where("version", $manifest->version)
                ->where("role_id", $roleId)
                ->first();

            if (!$isApprove) {
                return false;
            }
        }

        $isApprove = $manifest->approvement
            ->where("status", Manifest::STATUS_APPROVED)
            ->where("version", $manifest->version)
            ->where("role_id", User::ROLE_SABAH_PARKS)
            ->first();

        if ($isSabahParks && !$isApprove) {
            return false;
        }

        return true;
    }

    protected function checkSabahParks(Manifest $manifest)
    {
        $arrDestName = ["Bohey Dulang", "Sibuan", "Mantabuan", "Maiga"];

        $arrSabahParksDest = RefDestination::query()
            ->where("type", RefDestination::TYPE_DESTINATION)
            ->whereIn("title", $arrDestName)
            ->get()
            ->pluck("id")->toArray();

        $arrDestId = $manifest->manifestDestination
            ->pluck("ref_destination_id");

        foreach ($arrDestId as $destId) {
            if (in_array($destId, $arrSabahParksDest)) {
                return true;
            }
        }

        return false;
    }
}
