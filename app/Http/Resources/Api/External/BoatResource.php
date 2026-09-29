<?php

namespace App\Http\Resources\Api\External;

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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'number' => $this->number,
            'license' => $this->license,
            'license_expiry_date' => $this->license_expiry_date,
            'capacity' => $this->capacity,
            // null when the owning company has been deleted
            'company' => $this->company ? [
                'id' => $this->company->id,
                'name' => $this->company->name,
            ] : null,
            'boatman' => BoatmanResource::collection($this->whenLoaded('boatman')),
        ];
    }
}
