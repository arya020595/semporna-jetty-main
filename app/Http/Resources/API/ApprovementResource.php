<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class ApprovementResource extends JsonResource
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
            "date" => $this->created_at->timezone('Asia/Kuala_Lumpur')->format("Y-m-d H:i:s"),
            "user" => optional($this->user)->name,
            "email" => optional($this->user)->email,
            "user_id" => optional($this->user)->id,
            "role" => $this->role->name,
            "role_id" => $this->role->id,
            "status" => $this->status,
            "status_text" => $this->status_text,
            "comments" => $this->comments,
            "version" => $this->version
        ];
    }
}
