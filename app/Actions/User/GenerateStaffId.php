<?php

namespace App\Actions\User;

use App\Models\User;
use Carbon\Carbon;

class GenerateStaffId
{
    /**
     * Generate a new Staff ID with format MJ-ddmmyy-xx
     *
     * @return string
     */
    public function execute()
    {
        $date = Carbon::now();
        $dateStr = $date->format('dmy'); // ddmmyy format

        // Find the latest user created today to get the next sequence
        $latestUserToday = User::query()
            ->whereDate('created_at', $date->toDateString())
            ->where('staf_id', 'like', "MJ-{$dateStr}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextSequence = 1;
        if ($latestUserToday && $latestUserToday->staf_id) {
            // Extract the sequence from the latest staff ID (e.g., MJ-270226-01 -> 01)
            $parts = explode('-', $latestUserToday->staf_id);
            if (count($parts) === 3) {
                $lastSequence = (int) $parts[2];
                $nextSequence = $lastSequence + 1;
            }
        }

        // Pad sequence with leading zero if single digit (e.g., 01, 02)
        $sequenceStr = str_pad($nextSequence, 2, '0', STR_PAD_LEFT);

        return "MJ-{$dateStr}-{$sequenceStr}";
    }
}
