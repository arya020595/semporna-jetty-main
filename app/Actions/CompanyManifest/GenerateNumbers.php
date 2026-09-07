<?php

namespace App\Actions\CompanyManifest;

use App\Models\Manifest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
    public function execute(string $prefix, ?Carbon $date = null): array
    {
        if (!$date) {
            $date = Carbon::now();
        }

        $formDate = $date->format('Y-m-d');

        $nextSequence = DB::transaction(function () use ($formDate) {
            $lastEntry = Manifest::withTrashed()
                ->where('form_date', $formDate)
                ->lockForUpdate()
                ->orderByDesc('sequence')
                ->first(['sequence']);

            return $lastEntry ? $lastEntry->sequence + 1 : 1;
        });

        $number = str_pad($nextSequence, $this->numberLength, '0', STR_PAD_LEFT);
        $strDate = $date->format('ymd');

        return [
            "number" => $prefix . '-' . $strDate . '-' . $number,
            "date" => $formDate,
            "sequence" => $nextSequence
        ];
    }
}
