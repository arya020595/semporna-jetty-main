<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserJettyResource extends JsonResource
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
            "staf_id" => $this->staf_id,
            "name" => $this->name,
            "ic_no" => $this->ic_no,
            "email" => $this->email,
            "status" => $this->status,
            "status_text" => $this->status_text,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
