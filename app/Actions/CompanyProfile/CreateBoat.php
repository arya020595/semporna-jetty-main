<?php

namespace App\Actions\CompanyProfile;

use App\Models\Boat;
use App\Models\Company;
use App\Models\Fileable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateBoat
{

    public function execute(Company $company, array $arrData)
    {
        $boat = $company->boat()->updateOrCreate([
            "id" => $arrData["id"] ?? false
        ], [
            "number" => $arrData["license"],
            "license" => $arrData["license"],
            "capacity" => $arrData["capacity"],
            "license_expiry_date" => $arrData["license_expiry_date"],
        ]);

        $files = $boat->fileable()
            ->where("code_type", Boat::FILEABLE_LICENSE)
            ->get();

        foreach ($arrData['license_file'] as $key => $requestFile) {
            $fileable = optional($files)->shift();

            if ($requestFile ?? false) {
                $fileableFormat = Fileable::prepareForDB($requestFile, 'boat_license');

                if ($fileable) {
                    $fileable->update(array_merge([
                        "access_key" => Str::random(64)
                    ], $fileableFormat));
                } else {
                    $boat->fileable()->create(array_merge([
                        "code_type" => Boat::FILEABLE_LICENSE,
                        "access_key" => Str::random(64)
                    ], $fileableFormat));
                }

                continue;
            }

            if ($fileable && !($arrData["license_file_existing"][$key] ?? false)) {
                $fileable->delete();
            }
        }

        return $boat;
    }
}
