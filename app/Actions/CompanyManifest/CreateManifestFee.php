<?php

namespace App\Actions\CompanyManifest;

use App\Models\Config;
use App\Models\Manifest;
use App\Models\ManifestFee;
use App\Models\RefDestination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateManifestFee
{

    const NATIONALITY_LOCAL = [18, 19];
    const MAX_AGE_CHILD = 11;

    protected $numberLength = 3;

    /**
     * Execute the action
     *
     * @param string $prefix
     * @param Carbon|boolean|null $date
     * @return ManifestFee|bool
     */
    public function execute(Manifest $manifest, $forceNoBoatFee = false, $isChangeDate = false)
    {
        $config = self::getConfig();

        $guest = $manifest->guest->where("is_staff", "!=", 1);
        $arrCount = $this->countPassengers($guest);

        $isAdditional = ManifestFee::query()
            ->where("manifest_id", $manifest->id)
            ->where("type", ManifestFee::TYPE_PRIMARY)
            ->where("status", ManifestFee::STATUS_PAID)
            ->exists();

        $totalPaid = $this->calculateTotalPaid($manifest);
        $totalPassenger = $this->calculateTotal($arrCount, $config);
        $totalTicket = $this->calculateTotalTicket($arrCount, $config);

        $total = $isAdditional
            ? $totalPassenger - ($totalPaid["total"] - $totalPaid["boat_fee"])
            : $totalPassenger;

        if ($isAdditional && $total <= 0 && !$isChangeDate) {
            $manifest->update([
                "payment_status" => Manifest::PAYMENT_STATUS_PAID
            ]);
            return false;
        }

        if ($isAdditional && !$isChangeDate) {
            $boatFee = 0;
        } else {
            if ($forceNoBoatFee) {
                $boatFee = 0;
            } else {
                // Event-Driven Logic:
                // Check if there is ANY manifest for this boat on this date that has PAID for the boat fee.
                // If yes, this manifest is free. If no, this manifest is charged.

                $hasPaidManifest = ManifestFee::query()
                    ->whereHas('manifest', function ($q) use ($manifest) {
                        $q->where(function ($query) use ($manifest) {
                            if ($manifest->boat_id) {
                                $query->where('boat_id', $manifest->boat_id);
                            } else {
                                $query->whereNull('boat_id')
                                    ->where('boat_number', $manifest->boat_number);
                            }
                        })
                            ->whereDate('departure_date', $manifest->departure_date)
                            ->where('id', '!=', $manifest->id)
                            ->where('status', '!=', Manifest::STATUS_REJECTED);
                    })
                    ->where('status', ManifestFee::STATUS_PAID)
                    ->where('boat_fee', '>', 0)
                    ->exists();

                if ($hasPaidManifest || ($totalPaid["boat_fee"] > 0 && $isChangeDate)) {
                    $boatFee = 0;
                } else {
                    $boatFee = $config["boat_fee"];
                }
            }
        }

        if ($total + $boatFee <= 0) {
            return false;
        }

        // Only set payment_status to PENDING if not Seafest with bypass payment
        // Seafest manifests with bypass should keep payment_status = PAID
        // IMPORTANT: Only bypass if manifest is finalized (is_final=1).
        // Draft Seafest manifests must remain PENDING to avoid misleading other manifests.
        $shouldSetPending = true;
        if (config('features.bypass_seafest_payment') && $manifest->is_final) {
            $departure = RefDestination::find($manifest->departure_id);
            if ($departure && $departure->code == RefDestination::CODE_SEAFEST) {
                $shouldSetPending = false;
            }
        }

        if ($shouldSetPending) {
            $manifest->update([
                "payment_status" => Manifest::PAYMENT_STATUS_PENDING
            ]);
        }

        // Determine manifest fee status
        // For Seafest with bypass payment, set to PAID immediately (no online payment required)
        $manifestFeeStatus = ManifestFee::STATUS_PENDING;
        if (config('features.bypass_seafest_payment') && !$shouldSetPending) {
            // This is Seafest Jetty with bypass - mark fee as PAID
            $manifestFeeStatus = ManifestFee::STATUS_PAID;
        }

        $manifestFee = ManifestFee::updateOrCreate([
            "manifest_id" => $manifest->id,
            "status" => ManifestFee::STATUS_PENDING
        ], [
            "manifest_id" => $manifest->id,
            "type" => $isAdditional ? ManifestFee::TYPE_ADDITIONAL : ManifestFee::TYPE_PRIMARY,

            "local_child" => $arrCount["local_child"] - $totalPaid["local_child"],
            "local_adult" => $arrCount["local_adult"] - $totalPaid["local_adult"],
            "foreign_child" => $arrCount["foreign_child"] - $totalPaid["foreign_child"],
            "foreign_adult" => $arrCount["foreign_adult"] - $totalPaid["foreign_adult"],

            "local_child_fee" => $config["local_child_fee"],
            "local_adult_fee" => $config["local_adult_fee"],
            "foreign_child_fee" => $config["foreign_child_fee"],
            "foreign_adult_fee" => $config["foreign_adult_fee"],

            "ticket_local_child" => $arrCount["ticket_local_child"] - $totalPaid["ticket_local_child"],
            "ticket_local_adult" => $arrCount["ticket_local_adult"] - $totalPaid["ticket_local_adult"],
            "ticket_foreign_child" => $arrCount["ticket_foreign_child"] - $totalPaid["ticket_foreign_child"],
            "ticket_foreign_adult" => $arrCount["ticket_foreign_adult"] - $totalPaid["ticket_foreign_adult"],

            "boat_fee" => $boatFee,
            "status" => $manifestFeeStatus,
            "total" => $total + $boatFee,
            "total_ticket" => $totalTicket,
        ]);

        // If this manifest became PAID, update others to Free
        if ($manifestFeeStatus === ManifestFee::STATUS_PAID && $boatFee > 0) {
            $concurrentManifestIds = Manifest::query()
                ->where(function ($query) use ($manifest) {
                    if ($manifest->boat_id) {
                        $query->where('boat_id', $manifest->boat_id);
                    } else {
                        $query->whereNull('boat_id')
                            ->where('boat_number', $manifest->boat_number);
                    }
                })
                ->whereDate('departure_date', $manifest->departure_date)
                ->where('id', '!=', $manifest->id)
                ->where('status', '!=', Manifest::STATUS_REJECTED)
                ->pluck('id');

            if ($concurrentManifestIds->isNotEmpty()) {
                ManifestFee::query()
                    ->whereIn('manifest_id', $concurrentManifestIds)
                    ->where('status', ManifestFee::STATUS_PENDING)
                    ->where('boat_fee', '>', 0)
                    ->update([
                        'total' => DB::raw('total - boat_fee'),
                        'boat_fee' => 0,
                    ]);
            }
        }

        // Link guests to this manifest fee
        if ($isAdditional) {
            // For additional fees, link guests with is_additional=1 that don't have manifest_fee_id yet
            $manifest->guest()
                ->where("is_staff", "!=", 1)
                ->where("is_additional", 1)
                ->whereNull("manifest_fee_id")
                ->update(["manifest_fee_id" => $manifestFee->id]);
        } else {
            // For primary fees, link guests with is_additional=0 or NULL
            $manifest->guest()
                ->where("is_staff", "!=", 1)
                ->where(function ($query) {
                    $query->where("is_additional", 0)
                        ->orWhereNull("is_additional");
                })
                ->update(["manifest_fee_id" => $manifestFee->id]);
        }

        return $manifestFee;
    }

    protected function countPassengers($arrPassenger)
    {
        $localChild = 0;
        $localAdult = 0;
        $foreignChild = 0;
        $foreignAdult = 0;

        $ticketLocalChild = 0;
        $ticketLocalAdult = 0;
        $ticketForeignChild = 0;
        $ticketForeignAdult = 0;

        foreach ($arrPassenger as $passenger) {
            if (
                in_array($passenger->nationality_id, self::NATIONALITY_LOCAL)
                && $passenger->age <= self::MAX_AGE_CHILD
            ) {
                $localChild++;
                $ticketLocalChild = !$passenger->ticket_code
                    ? $ticketLocalChild
                    : $ticketLocalChild + 1;
            } elseif (
                in_array($passenger->nationality_id, self::NATIONALITY_LOCAL)
                && $passenger->age > self::MAX_AGE_CHILD
            ) {
                $localAdult++;
                $ticketLocalAdult = !$passenger->ticket_code
                    ? $ticketLocalAdult
                    : $ticketLocalAdult + 1;
            } elseif (
                !in_array($passenger->nationality_id, self::NATIONALITY_LOCAL)
                && $passenger->age <= self::MAX_AGE_CHILD
            ) {
                $foreignChild++;
                $ticketForeignChild = !$passenger->ticket_code
                    ? $ticketForeignChild
                    : $ticketForeignChild + 1;
            } else {
                $foreignAdult++;
                $ticketForeignAdult = !$passenger->ticket_code
                    ? $ticketForeignAdult
                    : $ticketForeignAdult + 1;
            }
        }

        return [
            "local_child" => $localChild,
            "local_adult" => $localAdult,
            "foreign_child" => $foreignChild,
            "foreign_adult" => $foreignAdult,

            "ticket_local_child" => $ticketLocalChild,
            "ticket_local_adult" => $ticketLocalAdult,
            "ticket_foreign_child" => $ticketForeignChild,
            "ticket_foreign_adult" => $ticketForeignAdult,
        ];
    }

    protected function calculateTotal($arrCount, $config)
    {
        $localTotal = ($arrCount["local_child"] - $arrCount["ticket_local_child"] ?? 0) * $config["local_child_fee"]
            + ($arrCount["local_adult"] - $arrCount["ticket_local_adult"] ?? 0) * $config["local_adult_fee"];

        $foreignTotal = ($arrCount["foreign_child"] - $arrCount["ticket_foreign_child"] ?? 0) * $config["foreign_child_fee"]
            + ($arrCount["foreign_adult"] - $arrCount["ticket_foreign_adult"] ?? 0) * $config["foreign_adult_fee"];

        return $localTotal + $foreignTotal;
    }

    protected function calculateTotalTicket($arrCount, $config)
    {
        $localTotal = ($arrCount["ticket_local_child"] ?? 0) * $config["local_child_fee"]
            + ($arrCount["ticket_local_adult"] ?? 0) * $config["local_adult_fee"];

        $foreignTotal = ($arrCount["ticket_foreign_child"] ?? 0) * $config["foreign_child_fee"]
            + ($arrCount["ticket_foreign_adult"] ?? 0) * $config["foreign_adult_fee"];

        return $localTotal + $foreignTotal;
    }

    protected function calculateTotalPaid(Manifest $manifest)
    {
        $manifestFeePaid = $manifest->manifestFee()
            ->where("status", ManifestFee::STATUS_PAID)
            ->get();

        return [
            "local_child" => $manifestFeePaid->sum("local_child"),
            "local_adult" => $manifestFeePaid->sum("local_adult"),
            "foreign_child" => $manifestFeePaid->sum("foreign_child"),
            "foreign_adult" => $manifestFeePaid->sum("foreign_adult"),

            "ticket_local_child" => $manifestFeePaid->sum("ticket_local_child"),
            "ticket_local_adult" => $manifestFeePaid->sum("ticket_local_adult"),
            "ticket_foreign_child" => $manifestFeePaid->sum("ticket_foreign_child"),
            "ticket_foreign_adult" => $manifestFeePaid->sum("ticket_foreign_adult"),

            "boat_fee" => $manifestFeePaid->sum("boat_fee"),
            "total" => $manifestFeePaid->sum("total")
        ];
    }

    public static function getConfig()
    {
        $configRecord = Config::where("code", ManifestFee::CONFIG_KEY_MANFIEST_FEE)
            ->first();

        $arrConfig = $configRecord ? json_decode($configRecord->value) : [];

        return [
            "local_child_fee" => $arrConfig->local_child_fee ?? 0,
            "local_adult_fee" => $arrConfig->local_adult_fee ?? 5,
            "foreign_child_fee" => $arrConfig->foreign_child_fee ?? 5,
            "foreign_adult_fee" => $arrConfig->foreign_adult_fee ?? 10,

            "boat_fee" => $arrConfig->boat_fee ?? 2
        ];
    }
}
