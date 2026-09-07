<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function generateArrYear($startDate, $duration)
    {
        $startYear = $startDate->format("Y");
        $endYear = $startDate->addMonth($duration)->format("Y");

        $arrYear = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $arrYear[] = $year;
        }

        return $arrYear;
    }

    public static function isInRange($startDate, $endDate, $year, $month)
    {
        $dateToCheck = Carbon::parse($year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01");

        return $dateToCheck->gte($startDate) && $dateToCheck->lte($endDate);
    }
}
