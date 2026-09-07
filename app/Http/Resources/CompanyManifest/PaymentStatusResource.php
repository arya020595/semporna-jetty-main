<?php

namespace App\Http\Resources\CompanyManifest;

use App\Models\Manifest;
use App\Models\ManifestFee;
use App\Policies\ManifestPolicy;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PaymentStatusResource extends JsonResource
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
            "form_number" => $this->form_number,
            "departure_date" => $this->departure_date,
            "company_name" => $this->company_name,
            "boat_number" => $this->boat_number,
            "destination" => $this->manifestDestination->map(function ($item) {
                return $item->ref_destination_name;
            })->join(", "),

            "total" => "RM " . number_format($this->manifestFee->sum("total"), 2),
            "payment_status" => $this->payment_status_text,
            "checkbox_state" => $this->payment_status == Manifest::STATUS_APPROVED
                ? -1
                : 0,
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
            "url" => route("panel.payment-status.show", ["manifest" => $this->uuid]),
            "label" => "Show Detail Manifest",
            "classStyle" => "btn-outline-info"
        ];

        $arrButton = [];

        $arrButton[] = $show;

        return $arrButton;
    }
}
