<?php

namespace App\Http\Resources\CompanyProfile;

use App\Models\Boatman;
use Illuminate\Http\Resources\Json\JsonResource;

class BoatmanResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $fileable = $this->fileable;
        $icFile = $fileable->firstWhere("code_type", Boatman::FILEABLE_IC);
        $mateCardFile = $fileable->firstWhere("code_type", Boatman::FILEABLE_MATE_CARD);
        $seamanCardFile = $fileable->firstWhere("code_type", Boatman::FILEABLE_SEAMAN_CARD);

        return [
            "id" => $this->id,
            "name" => $this->name,
            "ic_no" => $this->ic_no,
            "mate_card" => $this->mate_card,
            "seaman_card_no" => $this->seaman_card_no,

            "ic_no_file_existing" => $icFile
                ? route('resources.fileable.show', [
                    'fileable' => $icFile->id,
                    'access_key' => $icFile->access_key
                ])
                : null,

            "mate_card_file_existing" => $mateCardFile
                ? route('resources.fileable.show', [
                    'fileable' => $mateCardFile->id,
                    'access_key' => $mateCardFile->access_key
                ])
                : null,

            "seaman_card_file_existing" => $seamanCardFile
                ? route('resources.fileable.show', [
                    'fileable' => $seamanCardFile->id,
                    'access_key' => $seamanCardFile->access_key
                ])
                : null,
        ];
    }
}
