<?php

namespace App\Http\Resources\Authorities;

use App\Models\Manifest;
use App\Models\User;
use App\Policies\Authorities\MyDashboardPolicy;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class ManifestResource extends JsonResource
{
    protected User $user;
    protected Role $role;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $this->user = Auth::user();
        $this->role = $this->user->roles->first();

        return [
            "id" => $this->id,
            "form_number" => $this->form_number . (!$this->is_final ? " (DRAFT)" : ""),
            "departure_date" => $this->departure_date,
            "company_name" => $this->company_name,
            "boat_number" => $this->boat_number,
            "departure" => $this->departure_name,
            "destination" => $this->destination->map(function ($item) {
                return $item->pivot->ref_destination_name;
            })->join(", "),
            "guest_count" => $this->guest->where('is_staff', '!=', 1)->count(),
            "staff_count" => $this->guest->where('is_staff', 1)->count(),
            "status" => $this->formatedStatus($this->status, $this->status_text),
            "payment_status" => $this->formatedPayment($this->payment_status, $this->payment_status_text),
            "jetty_approval_status" => $this->formatedStatus($this->jetty_approval_status, $this->jetty_approval_status_text, false),
            "updated_at" => $this->updated_at,
            "action" => $this->getArrButton()
        ];
    }

    private function formatedStatus($status, $text, $isRoleSpecific = true)
    {
        if ($isRoleSpecific && !in_array($status, [Manifest::STATUS_APPROVED, Manifest::STATUS_REJECTED])) {
            $approvement = $this->approvement
                ->where("role_id", $this->role->id)
                ->sortByDesc('updated_at')
                ->first();

            if ($approvement) {
                $status = $approvement->status;
                $text = Manifest::ARR_STATUS[$status] ?? Manifest::ARR_STATUS[0];
            }
        }

        if ($status == Manifest::STATUS_PENDING) {
            return "<span>$text</span>";
        }

        $bgBadge = "bg-secondary";
        if ($status == Manifest::STATUS_APPROVED) {
            $bgBadge = "bg-success";
        }

        if ($status == Manifest::STATUS_APPROVED_PROGRESS) {
            $bgBadge = "bg-info";
        }

        if ($status == Manifest::STATUS_AMEND) {
            $bgBadge = "bg-warning";
        }

        if ($status == Manifest::STATUS_REJECTED) {
            $bgBadge = "bg-danger";
        }

        return "
            <span class='badge rounded-pill $bgBadge'>$text</span>
        ";
    }

    private function formatedPayment($status, $text)
    {
        $bgBadge = "bg-warning";
        if ($status == Manifest::PAYMENT_STATUS_PAID) {
            $bgBadge = "bg-success";
        }

        return "
            <span class='badge rounded-pill $bgBadge'>$text</span>
        ";
    }

    private function getArrButton()
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();

        $show = [
            "icon" => "fas fa-info",
            "url" => route("panel.autho-my-dashboard.show", ["manifest" => $this->uuid]),
            "label" => "Show Detail Manifest",
            "classStyle" => "btn-outline-success"
        ];
        $arrButton = [];

        $policy = new MyDashboardPolicy();

        if ($policy->view($user, $this->resource)) {
            $arrButton[] = $show;
        }

        return $arrButton;
    }
}
