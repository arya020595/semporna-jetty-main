<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $picture = $this->fileable
            ? route('resources.fileable.show', [
                "fileable" => $this->fileable->id,
                "access_key" => $this->fileable->access_key
            ])
            : '';

        return [
            "id" => $this->id,
            "staf_id" => $this->staf_id,
            "picture" => $picture,
            "name" => $this->name,
            "ic_no" => $this->ic_no,
            "email" => $this->email,
            "status" => $this->status,
            "status_text" => $this->status ? "Active" : "Non-active",
            "roles" => RoleDetailResource::collection($this->whenLoaded('roles')),
            "company_id" => $this->company_id,
            "company_name" => optional($this->company)->name,
            "jetty_id" => $this->jetty_id,
            "jetty_name" => optional($this->jetty)->title,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
