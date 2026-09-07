<?php

namespace App\Actions\CompanyManifest;

use App\Models\Manifest;
use App\Models\Ticket;
use Illuminate\Validation\ValidationException;

class CalculateTotalPassenger
{
    /**
     * Execute the action
     *
     * @param array $arrPassenger
     * @param Manifestl $manifest
     * @return array
     */
    public function execute(array $manifestRequest)
    {
        $boatman = ($manifestRequest["boatman_name"] ?? "") ? 1 : 0;
        $assistant = ($manifestRequest["assistant_name"] ?? "") ? 1 : 0;

        $countInstructor = count($manifestRequest["instructor"] ?? []);
        $countDivemaster = count($manifestRequest["divemaster"] ?? []);
        $countGuide = count($manifestRequest["guide"] ?? []);

        $countPassenger = count($manifestRequest["passengers "] ?? []);
        $countStaff = count($manifestRequest["staff"] ?? []);

        return $boatman + $assistant
            + $countInstructor + $countDivemaster + $countGuide
            + $countPassenger + $countStaff;
    }
}
