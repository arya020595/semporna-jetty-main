<?php

namespace App\Actions\CompanyManifest;

use App\Models\Manifest;
use App\Models\Ticket;
use Illuminate\Validation\ValidationException;

class CheckTicket
{
    /**
     * Execute the action
     *
     * @param array $arrPassenger
     * @param Manifestl $manifest
     * @return array
     */
    public function execute(array $arrPassenger, ?Manifest $manifest)
    {
        $arrTicket = [];
        foreach ($arrPassenger as $item) {
            if (!($item["ticket_code"] ?? false)) {
                continue;
            }

            $arrTicket[] = trim($item["ticket_code"]);
        }

        if (count($arrTicket) > count(array_unique($arrTicket))) {
            throw ValidationException::withMessages([
                "passengers" => "Cannot input the same ticket!"
            ]);
        }

        $ticketAvailable = Ticket::query()
            ->whereIn("code", $arrTicket)
            ->where(function ($query) use ($manifest) {
                return $query->where("status", Ticket::STATUS_AVAILABLE)
                    ->orWhere(function ($query) use ($manifest) {
                        return $query->where("status", Ticket::STATUS_TAKEN)
                            ->where("manifest_id", optional($manifest)->id);
                    });
            })
            ->pluck("code")
            ->toArray();

        if (count($ticketAvailable) < count($arrTicket)) {
            $notAvailable = array_diff($arrTicket, $ticketAvailable);
            throw ValidationException::withMessages([
                "passengers" => 'This ticket is not available: ' . implode(", ", $notAvailable)
            ]);
        }

        return true;
    }
}
