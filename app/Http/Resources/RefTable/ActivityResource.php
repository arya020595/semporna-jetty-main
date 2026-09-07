<?php

namespace App\Http\Resources\RefTable;

use App\Policies\ActivityPolicy;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ActivityResource extends JsonResource
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
            "code" => $this->code,
            "title" => $this->title,
            "updated_at" => $this->updated_at,
            "action" => $this->getArrButton()
        ];
    }

    private function getArrButton()
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();

        $show = [
            "icon" => "fas fa-info",
            "url" => route("panel.activity.show", ["activity" => $this->id]),
            "label" => "Show Detail Activity",
            "classStyle" => "btn-outline-info"
        ];

        $edit = [
            "icon" => "fas fa-edit",
            "url" => route("panel.activity.edit", ["activity" => $this->id]),
            "label" => "Edit Activity",
            "classStyle" => "btn-outline-warning"
        ];

        $delete = [
            "icon" => "fas fa-trash",
            "url" => route("panel.activity.delete", ["activity" => $this->id]),
            "label" => "Delete Activity",
            "classStyle" => "btn-outline-danger",
            "method" => "delete"
        ];

        $arrButton = [];

        $policy = new ActivityPolicy();
        if ($policy->view($user, $this->resource)) {
            $arrButton[] = $show;
        }
        if ($policy->update($user, $this->resource)) {
            $arrButton[] = $edit;
        }
        if ($policy->delete($user, $this->resource)) {
            $arrButton[] = $delete;
        }

        return $arrButton;
    }
}
