<?php

namespace App\Http\Resources\Api\External;

use Illuminate\Http\Resources\Json\JsonResource;

class BoatResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'number' => $this->number,
        ];
    }
}