<?php

namespace App\Actions\CompanyProfile;

use App\Models\Boat;
use App\Models\Boatman;
use App\Models\Company;
use App\Models\Fileable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateBoatman
{

    public function execute(Boat $boat, array $arrData, $type = Boatman::TYPE_BOATMAN)
    {
        $id = $arrData["id"] ?? null;

        if ($type === Boatman::TYPE_ASSISTANT) {
            $attributes = [
                "company_id" => $boat->company_id,
                "name" => $arrData["name"] ?? '',
                "ic_no" => $arrData["ic_no"] ?? null,
                "mate_card" => $arrData["mate_card"] ?? null,
                "seaman_card_no" => $arrData["seaman_card_no"] ?? null,
                "type" => $type
            ];
        } else {
            $attributes = [
                "company_id" => $boat->company_id,
                "name" => $arrData["name"] ?? '',
                "ic_no" => $arrData["ic_no"] ?? '',
                "mate_card" => $arrData["mate_card"] ?? '',
                "seaman_card_no" => $arrData["seaman_card_no"] ?? '',
                "type" => $type
            ];
        }

        if ($id) {
            $boatman = $boat->boatman()->updateOrCreate(["id" => $id], $attributes);
        } else {
            $boatman = $boat->boatman()->create($attributes);
        }

        if ($arrData['ic_no_file'] ?? false) {
            $requestFile = $arrData['ic_no_file'];
            $this->uploadFile(
                $boatman,
                $requestFile,
                Boatman::FILEABLE_IC
            );
        }

        if ($arrData['mate_card_file'] ?? false) {
            $requestFile = $arrData['mate_card_file'];
            $this->uploadFile(
                $boatman,
                $requestFile,
                Boatman::FILEABLE_MATE_CARD
            );
        }

        if ($arrData['seaman_card_file'] ?? false) {
            $requestFile = $arrData['seaman_card_file'];
            $this->uploadFile(
                $boatman,
                $requestFile,
                Boatman::FILEABLE_SEAMAN_CARD
            );
        }

        return $boatman;
    }

    protected function uploadFile(Boatman $boatman, $requestFile, string $codeType)
    {
        $path = Str::slug($codeType);
        $fileableFormat = Fileable::prepareForDB(
            $requestFile,
            $path,
            $boatman->id . "_" . Str::slug($boatman->name)
        );

        $fileable = $boatman->fileable()
            ->where("code_type", $codeType)
            ->first();

        if ($fileable) {
            $boatman->fileable()
                ->where("code_type", $codeType)
                ->update(array_merge([
                    "access_key" => Str::random(64)
                ], $fileableFormat));
        } else {
            $boatman->fileable()->create(array_merge([
                "code_type" => $codeType,
                "access_key" => Str::random(64)
            ], $fileableFormat));
        }
    }
}
