<?php

namespace App\Http\Resources\Api\External;

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
        return [
            'id' => $this->id,
            'boat_id' => $this->boat_id,
            'name' => $this->name,
            'ic_no' => $this->ic_no,
            'mate_card' => $this->mate_card,
            'seaman_card_no' => $this->seaman_card_no,
            'type' => $this->type,
            'type_label' => $this->typeLabel(),
            'company' => $this->whenLoaded('company', function () {
                return [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                ];
            }),
        ];
    }
}
