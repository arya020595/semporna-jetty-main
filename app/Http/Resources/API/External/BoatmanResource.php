<?php

namespace App\Http\Resources\Api\External;

use Illuminate\Http\Resources\Json\JsonResource;

class BoatmanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'boat_id' => $this->boat_id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'ic_no' => $this->ic_no,
            'type' => $this->type,
        ];
    }
}