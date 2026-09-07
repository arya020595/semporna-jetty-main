<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Policies\UserApprovalPolicy;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class UserApprovalTableResource extends JsonResource
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
            "picture" => url('storage/' . $this->picture),
            "name" => $this->name,
            "email" => $this->email,
            "status" => $this->status_text,
            "roles" => $this->roles->pluck("name")->join(", "),
            "last_access" => $this->last_access ? Carbon::parse($this->last_access) : " - ",
            "created_at" => $this->created_at,
            "action" => $this->getArrButton()
        ];
    }

    private function getArrButton()
    {
        $approve = [
            "icon" => "fas fa-check",
            "url" => "",
            "label" => "Approve User",
            "classStyle" => "btn-outline-success",
            "method" => "modal",
            "actionData" => [
                "url" => route("panel.user-approval.approve", ["user" => $this->id]),
                "type" => "approve",
                "data" => [
                    "staf_id" => $this->staf_id ?? ' - ',
                    "name" => $this->name,
                    "email" => $this->email,
                    "ic_no" => $this->ic_no ?? ' - ',
                    "role" => $this->roles->pluck("name")->join(", "),
                    "jetty" => optional($this->jetty)->title ?? " - "
                ]
            ],
        ];

        $user = Auth::user();
        $arrButton = [];

        $policy = new UserApprovalPolicy();
        if ($policy->approve($user, $this->resource) && $this->status == User::STATUS_PENDING) {
            $arrButton[] = $approve;
        }

        return $arrButton;
    }
}
