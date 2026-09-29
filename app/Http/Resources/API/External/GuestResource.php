<?php

namespace App\Http\Resources\Api\External;

use Illuminate\Http\Resources\Json\JsonResource;

class GuestResource extends JsonResource
{
    public function toArray($request)
    {
        $gender = strtoupper(trim((string) $this->gender));
        if (!in_array($gender, ['F', 'M'], true)) {
            throw new \UnexpectedValueException('Invalid guest gender for ID ' . $this->id);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'ic_no' => $this->ic_no,
            'nationality_name' => $this->nationality_name,
            'age' => (int) $this->age,
            'gender' => $gender,
        ];
    }
}