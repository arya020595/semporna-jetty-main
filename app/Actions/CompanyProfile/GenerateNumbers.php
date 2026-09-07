<?php

namespace App\Actions\CompanyProfile;

use App\Models\Company;
use App\Models\Manifest;
use Carbon\Carbon;

class GenerateNumbers
{

    protected $numberLength = 3;

    /**
     * Execute the action
     *
     * @param string $prefix
     * @param Carbon|boolean|null $date
     * @return array
     */
    public function execute(string $prefix, ?Carbon $date = null)
    {
        if (!$date) {
            $date = Carbon::now();
        }

        $yearMonth = $date->format("Y-m");
        $lastEntries = Company::query()
            ->where("year_month", $yearMonth)
            ->latest()
            ->first();

        $nextSequence = $lastEntries ? $lastEntries->sequence + 1 : 1;

        $number = str_pad(
            $nextSequence,
            $this->numberLength,
            '0',
            STR_PAD_LEFT
        );

        $strDate = $date->format("ym");
        return [
            "number" => $prefix . '-' . $strDate . '-' . $number,
            "year_month" => $yearMonth,
            "sequence" => $nextSequence
        ];
    }
}
