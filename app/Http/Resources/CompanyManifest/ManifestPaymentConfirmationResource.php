<?php

namespace App\Http\Resources\CompanyManifest;

use App\Models\Manifest;
use App\Models\ManifestFee;
use Illuminate\Http\Resources\Json\JsonResource;

class ManifestPaymentConfirmationResource extends JsonResource
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
            "boat_number" => $this->boat_number,
            "destination" => $this->manifestDestination->map(function ($item) {
                return $item->ref_destination_name;
            })->join(", "),
            "overall_fee" => $this->manifestFee->sum("total"),
            "boat_id" => $this->boat_id,
            "boat_fee" => $this->manifestFee->sum("boat_fee"),
            "standard_boat_fee" => \App\Actions\CompanyManifest\CreateManifestFee::getConfig()['boat_fee'] ?? 0,
            "boat_identifier" => $this->boat_id ? "ID:{$this->boat_id}" : "NAME:{$this->boat_number}",
            "is_boat_already_paid" => ManifestFee::query()
                ->whereHas('manifest', function ($q) {
                    $q->where(function ($query) {
                        if ($this->boat_id) {
                            $query->where('boat_id', $this->boat_id);
                        } else {
                            $query->whereNull('boat_id')
                                ->where('boat_number', $this->boat_number);
                        }
                    })
                        ->whereDate('departure_date', $this->departure_date)
                        ->where('id', '!=', $this->id)
                        ->where('status', '!=', Manifest::STATUS_REJECTED);
                })
                ->where('status', ManifestFee::STATUS_PAID)
                ->where('boat_fee', '>', 0)
                ->exists()
        ];
    }
}
