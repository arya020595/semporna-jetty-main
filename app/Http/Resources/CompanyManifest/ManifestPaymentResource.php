<?php

namespace App\Http\Resources\CompanyManifest;

use App\Models\Manifest;
use App\Models\ManifestFee;
use App\Policies\ManifestPolicy;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ManifestPaymentResource extends JsonResource
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
            "payment_status" => $this->getPaymentStatusDisplay(),
            "checkbox_state" => $this->getCheckboxState(),
            "action" => $this->getArrButton()
        ];
    }

    private function getPaymentStatusDisplay()
    {
        $today = now('Asia/Kuala_Lumpur')->format('Y-m-d');
        if ($this->payment_status == Manifest::PAYMENT_STATUS_PENDING && $this->departure_date < $today) {
            return "Expired";
        }

        return $this->payment_status_text;
    }

    private function getCheckboxState()
    {
        $today = now('Asia/Kuala_Lumpur')->format('Y-m-d');
        if (
            $this->payment_status == Manifest::PAYMENT_STATUS_PAID ||
            ($this->payment_status == Manifest::PAYMENT_STATUS_PENDING && $this->departure_date < $today)
        ) {
            return -1; // Disabled
        }

        return 0; // Enabled
    }

    private function getTotal()
    {
        $pending = $this->manifestFee
            ->where("status", ManifestFee::STATUS_PENDING);

        $sumPending = $pending->sum("total");

        $sumAll = $this->manifestFee->sum("total");

        return $sumPending > 0
            ? "RM " . $sumPending . " / " . "RM " . $sumAll
            : "RM " . $sumAll;
    }


    private function getArrButton()
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();

        $show = [
            "icon" => "fas fa-info",
            "url" => route("panel.manifest.show", ["manifest" => $this->uuid]),
            "label" => "Show Detail Manifest",
            "classStyle" => "btn-outline-info"
        ];

        $edit = [
            "icon" => "fas fa-edit",
            "url" => route("panel.manifest.edit", ["manifest" => $this->uuid]),
            "label" => "Edit Manifest",
            "classStyle" => "btn-outline-warning"
        ];

        $arrButton = [];

        $policy = new ManifestPolicy();
        if ($policy->view($user, $this->resource)) {
            $arrButton[] = $show;
        }
        if ($policy->update($user, $this->resource)) {
            $arrButton[] = $edit;
        }

        return $arrButton;
    }
}
