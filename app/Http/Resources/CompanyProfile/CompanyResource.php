<?php

namespace App\Http\Resources\CompanyProfile;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "registration_no" => $this->registration_no,
            "name" => $this->name,
            "number" => $this->number,
            "boats" => BoatResource::collection($this->boat ?? [])->toArray($request),
            "instructor" => BoatmanResource::collection($this->boatmanInstructor)->toArray($request),
            "divemaster" => BoatmanResource::collection($this->boatmanDivemaster)->toArray($request),
            "guide" => BoatmanResource::collection($this->boatmanGuide)->toArray($request),
        ];
    }
}
