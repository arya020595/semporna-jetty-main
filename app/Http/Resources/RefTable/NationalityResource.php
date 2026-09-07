<?php

namespace App\Http\Resources\RefTable;

use App\Policies\NationalityPolicy;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class NationalityResource extends JsonResource
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
            "url" => route("panel.nationality.show", ["nationality" => $this->id]),
            "label" => "Show Detail Nationality",
            "classStyle" => "btn-outline-info"
        ];

        $edit = [
            "icon" => "fas fa-edit",
            "url" => route("panel.nationality.edit", ["nationality" => $this->id]),
            "label" => "Edit Nationality",
            "classStyle" => "btn-outline-warning"
        ];

        $delete = [
            "icon" => "fas fa-trash",
            "url" => route("panel.nationality.delete", ["nationality" => $this->id]),
            "label" => "Delete Nationality",
            "classStyle" => "btn-outline-danger",
            "method" => "delete"
        ];

        $arrButton = [];

        $nationalityPolicy = new NationalityPolicy();
        if ($nationalityPolicy->view($user, $this->resource)) {
            $arrButton[] = $show;
        }
        if ($nationalityPolicy->update($user, $this->resource)) {
            $arrButton[] = $edit;
        }
        if ($nationalityPolicy->delete($user, $this->resource)) {
            $arrButton[] = $delete;
        }

        return $arrButton;
    }
}
