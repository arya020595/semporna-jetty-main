<?php

namespace App\Actions\CompanyManifest;

use App\Models\Manifest;
use App\Models\ManifestFee;
use App\Models\ManifestSummary;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class CalculateManifestSummary
{
    protected $columns;

    /**
     * @var User
     */
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }
    /**
     * Execute the action
     *
     * @param  array  $data
     * @return Pdf
     */
    public function execute(Manifest $manifest)
    {
        $manifestFee = $manifest->manifestFee()
            ->whereStatus(ManifestFee::STATUS_PAID)
            ->get();

        $manifestFeeFirst = $manifestFee->first();

        if (!$manifestFeeFirst) {
            echo $manifest->id . "\n";
            return false;
        }

        $passengers = $manifest->guest;

        $localAdult = $manifestFee->sum("local_adult");
        $localChild = $manifestFee->sum("local_child");
        $foreignAdult = $manifestFee->sum("foreign_adult");
        $foreignChild = $manifestFee->sum("foreign_child");

        ManifestSummary::updateOrCreate([
            "manifest_id" => $manifest->id
        ], [
            "manifest_id" => $manifest->id,
            "gender_male" => $passengers->where("gender", "M")->count(),
            "gender_female" => $passengers->where("gender", "F")->count(),

            "local_adult" => $localAdult,
            "local_child" => $localChild,
            "foreign_adult" => $foreignAdult,
            "foreign_child" => $foreignChild,

            "charge_local_adult" => $manifestFeeFirst->local_adult_fee * $localAdult,
            "charge_local_child" => $manifestFeeFirst->local_child_fee * $localChild,
            "charge_foreign_adult" => $manifestFeeFirst->foreign_adult_fee * $foreignAdult,
            "charge_foreign_child" => $manifestFeeFirst->foreign_child_fee * $foreignChild,

            "charge_total_passenger" => $manifestFeeFirst->local_adult_fee * $localAdult +
                $manifestFeeFirst->local_child_fee * $localChild +
                $manifestFeeFirst->foreign_adult_fee * $foreignAdult +
                $manifestFeeFirst->foreign_child_fee * $foreignChild,

            "charge_boat_fee" => $manifestFee->sum("boat_fee"),
        ]);
    }
}
