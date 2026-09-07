<?php

namespace App\Http\Resources\CompanyManifest;

use App\Models\ManifestFee;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $statusMap = [
            Payment::STATUS_PAID    => 'Paid',
            Payment::STATUS_PENDING => 'Pending',
            Payment::STATUS_FAILED  => 'Failed',
        ];

        $statusBadgeMap = [
            Payment::STATUS_PAID    => 'success',
            Payment::STATUS_PENDING => 'warning',
            Payment::STATUS_FAILED  => 'danger',
        ];

        $manifestFees = $this->manifestFee->map(function (ManifestFee $fee) {
            $manifest = $fee->manifest;

            $feeTypeMap = [
                ManifestFee::TYPE_PRIMARY    => 'Primary',
                ManifestFee::TYPE_ADDITIONAL => 'Additional',
            ];

            $feeStatusMap = [
                ManifestFee::STATUS_PAID    => 'Paid',
                ManifestFee::STATUS_PENDING => 'Pending',
                ManifestFee::STATUS_FAILED  => 'Failed',
            ];

            $destination = $manifest
                ? $manifest->manifestDestination->map(function ($d) {
                    return $d->ref_destination_name;
                })->join(', ')
                : '-';

            return [
                'manifest_fee_id'   => $fee->id,
                'type'              => $feeTypeMap[$fee->type] ?? '-',
                'fee_status'        => $feeStatusMap[$fee->status] ?? '-',
                'fee_status_raw'    => $fee->status,
                // Manifest info
                'form_number'       => ($manifest ? $manifest->form_number : null) ?? '-',
                'manifest_uuid'     => $manifest ? $manifest->uuid : null,
                'departure_date'    => ($manifest ? $manifest->departure_date : null) ?? '-',
                'company_name'      => ($manifest ? $manifest->company_name : null) ?? '-',
                'boat_number'       => ($manifest ? $manifest->boat_number : null) ?? '-',
                'destination'       => $destination,
                // Fee breakdown
                'local_adult'       => $fee->local_adult ?? 0,
                'local_child'       => $fee->local_child ?? 0,
                'foreign_adult'     => $fee->foreign_adult ?? 0,
                'foreign_child'     => $fee->foreign_child ?? 0,
                'local_adult_fee'   => $fee->local_adult_fee ?? 0,
                'local_child_fee'   => $fee->local_child_fee ?? 0,
                'foreign_adult_fee' => $fee->foreign_adult_fee ?? 0,
                'foreign_child_fee' => $fee->foreign_child_fee ?? 0,
                'boat_fee'          => $fee->boat_fee ?? 0,
                'total'             => $fee->total ?? 0,
                'show_url'          => ($manifest && $manifest->uuid)
                    ? route('panel.payment-status.show', ['manifest' => $manifest->uuid])
                    : null,
            ];
        });

        $payerName = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));

        $departureDates = $manifestFees->pluck('departure_date')->filter(fn($d) => $d !== '-')->unique()->sort();
        $departureDateDisplay = '-';

        if ($departureDates->isNotEmpty()) {
            $earliestDate = Carbon::parse($departureDates->first())->format('d M Y');
            $count = $departureDates->count();
            $departureDateDisplay = $earliestDate . ($count > 1 ? "| +" . ($count - 1) . " more" : "");
        }

        return [
            'id'              => $this->id,
            'code'            => $this->code,
            'departure_date_display' => $departureDateDisplay,
            'transaction_id'  => $this->transaction_id ?? null,
            'created_at'      => $this->created_at ? $this->created_at->copy()->timezone('Asia/Kuala_Lumpur')->format('d M Y H:i') : '-',
            'payer_name'      => $payerName ?: '-',
            'email'           => $this->email ?? '-',
            'tel_no'          => $this->tel_no ?? '-',
            'amount'          => $this->amount ?? 0,
            'amount_formatted' => 'RM ' . number_format($this->amount ?? 0, 2),
            'status'          => $this->status,
            'status_text'     => $statusMap[$this->status] ?? 'Unknown',
            'status_badge'    => $statusBadgeMap[$this->status] ?? 'secondary',
            'manifest_fees'   => $manifestFees,
            'manifest_count'  => $manifestFees->count(),
        ];
    }
}
