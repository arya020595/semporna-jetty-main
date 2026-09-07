<?php

namespace App\Actions\CompanyManifest;

use App\Models\Manifest;
use App\Models\Ticket;
use Illuminate\Validation\ValidationException;

class TakeTicket
{
    /**
     * Execute the action
     *
     * @param array $arrPassenger
     * @param Manifestl $manifest
     * @return array
     */
    public function execute(Manifest $manifest)
    {
        $arrPassenger = $manifest->guest()
            ->whereNotNull("ticket_code")
            ->get();

        foreach ($arrPassenger as $item) {
            if (!$item["ticket_code"]) {
                continue;
            }

            $ticketCode = $item->ticket_code;

            Ticket::query()
                ->where("code", $ticketCode)
                ->update([
                    "status" => Ticket::STATUS_TAKEN,
                    "manifest_id" => $manifest->id,
                    "manifest_form_number" => $manifest->form_number,
                    "passenger_id" => $item->id,
                    "taken_at" => now()
                ]);
        }

        return true;
    }
}
