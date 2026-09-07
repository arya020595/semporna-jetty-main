<?php

namespace App\Http\Resources\CompanyManifest;

use App\Models\Manifest;
use App\Policies\ManifestPolicy;
use App\Policies\UserActivityPolicy;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ManifestResource extends JsonResource
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
            "form_number" => $this->form_number . (!$this->is_final ? " (DRAFT)" : ""),
            "departure_date" => $this->departure_date,
            "company_name" => $this->company_name,
            "boat_number" => $this->boat_number,
            "destination" => $this->destination->map(function ($item) {
                return $item->pivot->ref_destination_name;
            })->join(", "),
            "guest_count" => $this->guest->where('is_staff', '!=', 1)->count(),
            "staff_count" => $this->guest->where('is_staff', 1)->count(),
            "status" => $this->formatStatus($this->status, $this->status_text),
            "jetty_approval_status" => $this->formatStatus($this->jetty_approval_status, $this->jetty_approval_status_text),
            "payment_status" => $this->payment_status_text,
            "updated_at" => $this->updated_at,
            "action" => $this->getArrButton()
        ];
    }

    private function formatStatus($status, $text)
    {
        if ($status == Manifest::STATUS_PENDING) {
            return $text;
        }

        $_ARR_BADGE = [
            Manifest::STATUS_PENDING => "bg-secondary",
            Manifest::STATUS_AMEND => "bg-warning",
            Manifest::STATUS_REJECTED => "bg-danger",
            Manifest::STATUS_APPROVED => "bg-success",
            Manifest::STATUS_APPROVED_PROGRESS => "bg-info",
        ];

        $badgeClass = $_ARR_BADGE[$status] ?? 'bg-secondary';

        return '
            <span class="badge rounded-pill ' . $badgeClass . '">
                ' . $text . '
            </span>
        ';
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

        $showOnActivity = [
            "icon" => "fas fa-info",
            "url" => route("panel.user-activity.show", ["manifest" => $this->uuid]),
            "label" => "Show Detail Approved Manifest",
            "classStyle" => "btn-outline-success"
        ];

        $edit = [
            "icon" => "fas fa-edit",
            "url" => route("panel.manifest.edit", ["manifest" => $this->uuid]),
            "label" => "Edit Manifest",
            "classStyle" => "btn-outline-warning"
        ];

        $delete = [
            "icon" => "fas fa-trash",
            "url" => route("panel.user-activity.delete", ["manifest" => $this->uuid]),
            "label" => "Delete Manifest",
            "classStyle" => "btn-outline-danger",
            "method" => "delete"
        ];

        $arrButton = [];

        $policy = new ManifestPolicy();
        $policyUserActivity = new UserActivityPolicy();

        if ($policy->view($user, $this->resource)) {
            $arrButton[] = $show;
        } elseif ($policyUserActivity->view($user, $this->resource)) {
            $arrButton[] = $showOnActivity;
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
