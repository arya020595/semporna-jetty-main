<?php

namespace App\Http\Resources\API;

use App\Models\Boatman;
use App\Models\ManifestFee;
use Illuminate\Http\Resources\Json\JsonResource;

class UserActivityDetailResource extends JsonResource
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
            "type" => $this->type,
            "form_number" => $this->form_number,
            "created_date" => $this->created_at->timezone('Asia/Kuala_Lumpur')->format("d-m-Y"),
            "payment_status" => $this->payment_status,
            "payment_status_text" => $this->payment_status_text,
            "status" => $this->status,
            "status_text" => $this->status_text,

            "jetty_approval_status" => $this->jetty_approval_status,
            "jetty_approval_status_text" => $this->jetty_approval_status_text,
            "jetty_approval_comments" => $this->jetty_approval_comments,
            "jetty_approval_user" => [
                "name" => optional($this->jettyApprovalUser)->name,
                "email" => optional($this->jettyApprovalUser)->email,
            ],

            "departure_date" => $this->departure_date,
            "departure_time" => substr($this->departure_time, 0, 5),
            "company_id" => $this->company_id,
            "company_name" => $this->company_name,
            "boat_id" => $this->boat_id,
            "boat_number" => $this->boat_number,

            "boatman_id" => $this->boatman_id,
            "boatman_name" => $this->boatman_name,
            "boatman_mate_no" => $this->boatman_mate_no,
            "seaman_no" => $this->seaman_no,
            "boatman_ic_no" => $this->boatman_ic_no,

            "assistant_id" => $this->assistant_id,
            "assistant_name" => $this->assistant_name,
            "assistant_mate_no" => $this->assistant_mate_no,
            "assistant_ic_no" => $this->assistant_ic_no,
            "assistant_seaman_no" => $this->assistant_seaman_no,

            "instructor" => $this->getBoatmanOther(Boatman::TYPE_INSTRUCTOR),

            "divemaster" => $this->getBoatmanOther(Boatman::TYPE_DIVEMASTER),

            "guide" => $this->getBoatmanOther(Boatman::TYPE_GUIDE),

            "departure_id" => $this->departure_id,
            "departure_name" => $this->departure_name,

            "destination" => $this->destination->map(function ($item) {
                return $item->id;
            })->toArray(),

            "manifest_destination" => $this->manifestDestination,

            "manifest_fee" => $this->calculateManifestFee(),

            "passengers" => $this->guest->where('is_staff', 0)->values(),
            "staff" => $this->guest->where('is_staff', 1)->values(),

            "approvement" => ApprovementResource::collection($this->latestApprovementByRole)
        ];
    }

    protected function getBoatmanOther($type)
    {
        $arrData = $this->manifestBoatman->where("type", $type)
            ->map(function ($item) {
                return [
                    "id" => $item->id,
                    "boatman_id" => $item->boatman_id,
                    "name" => $item->name,
                    "ic_no" => $item->ic_no
                ];
            })->toArray();

        return array_values($arrData);
    }

    protected function calculateManifestFee()
    {
        $primaryFee = $this->manifestFee
            ->where("type", ManifestFee::TYPE_PRIMARY)
            ->first();

        return [
            "local_child" => $this->manifestFee->sum("local_child"),
            "local_adult" => $this->manifestFee->sum("local_adult"),
            "foreign_child" => $this->manifestFee->sum("foreign_child"),
            "foreign_adult" => $this->manifestFee->sum("foreign_adult"),

            "total" => $this->manifestFee->sum("total"),

            "local_child_fee" => optional($primaryFee)->local_child_fee,
            "local_adult_fee" => optional($primaryFee)->local_adult_fee,
            "foreign_child_fee" => optional($primaryFee)->foreign_child_fee,
            "foreign_adult_fee" => optional($primaryFee)->foreign_adult_fee,
            "boat_fee" => optional($primaryFee)->boat_fee,
        ];
    }
}
