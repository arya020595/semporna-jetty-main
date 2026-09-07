<?php

namespace App\Actions\CompanyProfile;

use App\Models\Boat;
use App\Models\Boatman;
use App\Models\Company;
use App\Models\Fileable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateBoatmanOthers
{

    public function execute(Company $company, array $arrData, $type = Boatman::TYPE_BOATMAN)
    {
        $id = $arrData["id"] ?? null;
        $name = trim($arrData["name"] ?? '');

        if (empty($name)) {
            if ($id) {
                $company->boatman()->where("id", $id)->delete();
            }
            return null;
        }

        $attributes = [
            "company_id" => $company->id,
            "name" => $arrData["name"] ?? '',
            "ic_no" => $arrData["ic_no"] ?? '',
            "mate_card" => $arrData["mate_card"] ?? '',
            "seaman_card_no" => $arrData["seaman_card_no"] ?? '',
            "type" => $type
        ];

        if ($id) {
            return $company->boatman()->updateOrCreate(["id" => $id], $attributes);
        }

        return $company->boatman()->create($attributes);
    }
}
