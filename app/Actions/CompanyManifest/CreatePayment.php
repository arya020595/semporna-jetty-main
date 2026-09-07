<?php

namespace App\Actions\CompanyManifest;

use App\Models\Manifest;
use App\Models\ManifestFee;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CreatePayment
{
    const NATIONALITY_LOCAL = [18, 19];
    const MAX_AGE_CHILD = 11;

    const MERCHANT_HARTAWAN_STABIL = "hartawan_stabil";
    const MERCHANT_SEAFEST = "seafest";

    protected $numberLength = 3;

    /**
     * Execute the action
     *
     * @param string $prefix
     * @param Carbon|boolean|null $date
     * @return Payment
     */
    public function execute(array $arrData, User $user)
    {
        // Track boats that have paid the fee in this transaction
        $chargedBoats = [];

        foreach ($arrData["manifest_id"] as $manifestId) {
            $manifest = Manifest::find($manifestId);
            if (!$manifest) continue;

            $dateKey = ($manifest->boat_id ? "ID:{$manifest->boat_id}" : "NAME:{$manifest->boat_number}")
                . '_' . $manifest->departure_date;

            // Determine if we should force NO boat fee
            // This happens if we have already "charged" this boat in this transaction
            $forceNoBoatFee = in_array($dateKey, $chargedBoats);

            // Recalculate fee
            $feeResult = (new CreateManifestFee)->execute($manifest, $forceNoBoatFee);

            // If the calculation resulted in a boat fee > 0, mark this boat as charged
            if ($feeResult && $feeResult['boat_fee'] > 0) {
                $chargedBoats[] = $dateKey;
            }
        }

        $manifestFee = ManifestFee::query()
            ->whereIn("manifest_id", $arrData["manifest_id"])
            ->where("status", ManifestFee::STATUS_PENDING)
            ->get();

        $payment = Payment::create([
            "user_id" => $user->id,
            "code" => Str::uuid(),
            "reciept_number" => '',
            "payment_method" => '',
            "first_name" => $arrData["name"],
            "email" => $arrData["email"],
            "tel_no" => $arrData["tel_no"],
            "amount" => $manifestFee->sum("total"),
            "status" => Payment::STATUS_PENDING,
            "merchant_id" => $arrData["merchant_id"],
            "detail" => implode(",", $arrData["manifest_id"])
        ]);

        ManifestFee::query()
            ->whereIn("manifest_id", $arrData["manifest_id"])
            ->where("status", ManifestFee::STATUS_PENDING)
            ->whereNotNull("payment_id")
            ->update(["payment_id" => null]);

        ManifestFee::query()
            ->whereIn("manifest_id", $arrData["manifest_id"])
            ->where("status", ManifestFee::STATUS_PENDING)
            ->update([
                "payment_id" => $payment->id
            ]);

        return $payment;
    }
}
