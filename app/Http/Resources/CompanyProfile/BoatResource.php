<?php

namespace App\Http\Resources\CompanyProfile;

use App\Models\Boat;
use App\Models\Company;
use Illuminate\Http\Resources\Json\JsonResource;

class BoatResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $files = $this->fileable
            ->where("code_type", Boat::FILEABLE_LICENSE);

        return [
            "id" => $this->id,
            "capacity" => $this->capacity,
            "license_expiry_date" => $this->license_expiry_date,
            "license" => $this->license,
            "license_file_existing" => $files ? $files->pluck("file_url") : null,
            "name" => $this->name,
            "boatman" => BoatmanResource::collection($this->boatmanMain)->toArray($request),
            "asst" => BoatmanResource::collection($this->boatmanAsst)->toArray($request),
            "boatman_count" => $this->boatmanMain->count(),
            "asst_count" => $this->boatmanAsst->count()
        ];
    }
}
