<?php

namespace App\Http\Resources\CompanyManifest;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();

        $userName = $user->id == $this->user->id
            ? $this->user->name . " (You)"
            : $this->user->name;

        return [
            "id" => $this->id,
            "date" => $this->created_at->timezone('Asia/Kuala_Lumpur')->format("Y-m-d H:i:s"),
            "user" => $userName,
            "user_email" => $this->user->email,
            "role" => $this->role->name,
            "role_id" => $this->role_id,
            "status" => $this->status,
            "status_text" => $this->status_text,
            "version" => $this->version,
            "comments" => $this->comments
        ];
    }
}
