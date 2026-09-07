<?php

namespace App\Actions\CompanyManifest;

use App\Models\Payment;
use App\Models\ManifestFee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class GeneratePaymentReceipt
{

    public function handle($user, $startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate, 'Asia/Kuala_Lumpur')->startOfDay()->timezone('UTC');
        $endDate = Carbon::parse($endDate, 'Asia/Kuala_Lumpur')->endOfDay()->timezone('UTC');

        // ===== OPTION 1: Filter by USER_ID only
        // $payments = Payment::query()
        //     ->where('user_id', $user->id)
        //     ->where('status', Payment::STATUS_PAID)
        //     ->whereBetween('created_at', [$startDate, $endDate])
        //     ->with(['manifestFee.manifest'])
        //     ->get();

        // ===== OPTION 2: Filter by COMPANY_ID only
        $payments = Payment::query()
            ->whereHas('manifestFee.manifest', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            })
            // Check if Payment is PAID OR linked ManifestFee is PAID (for inconsistent data)
            ->where(function ($q) {
                $q->where('status', Payment::STATUS_PAID)
                    ->orWhereHas('manifestFee', function ($subQ) {
                        $subQ->where('status', ManifestFee::STATUS_PAID);
                    });
            })
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->with(['manifestFee.manifest'])
            ->get();

        $receipts = [];

        foreach ($payments as $payment) {
            // Case 3: 1 Payment, Multiple Manifest (Grouped Payment)
            // In current logic, if multiple manifests are paid together, they share the same payment_id
            // So one Payment row has multiple ManifestFees from different manifests

            $manifestFees = $payment->manifestFee;
            $uniqueManifests = $manifestFees->pluck('manifest')->unique('id');

            if ($uniqueManifests->count() > 1) {
                // Case 3: Multiple Manifests
                $manifestNumbers = $uniqueManifests->pluck('form_number')->implode('<br>');
                $boatNumbers = $uniqueManifests->pluck('boat_number')->unique()->implode('<br>');
                $count = $uniqueManifests->count();

                $receipts[] = [
                    'transaction_date' => $payment->created_at->setTimezone('Asia/Kuala_Lumpur'),
                    'manifest_number' => $manifestNumbers,
                    'boat_number' => $boatNumbers,
                    'amount' => $payment->amount,
                    'remarks' => "Group Payment ({$count} manifests)",
                    'is_group' => true
                ];
            } else {
                // Case 1 & 2: Single Manifest
                $manifest = $uniqueManifests->first();
                $boatNumber = $manifest ? $manifest->boat_number : '-';
                $manifestNumber = $manifest ? $manifest->form_number : '-';

                $isAdditional = $manifestFees->contains('type', ManifestFee::TYPE_ADDITIONAL);
                $remark = $isAdditional ? 'Additional Payment' : '';
                $receipts[] = [
                    'transaction_date' => $payment->created_at->setTimezone('Asia/Kuala_Lumpur'),
                    'manifest_number' => $manifestNumber,
                    'boat_number' => $boatNumber,
                    'amount' => $payment->amount,
                    'remarks' => $remark,
                    'is_group' => false
                ];
            }
        }

        // Sort receipts: 1. Manifest No, 2. Date (Asc), 3. Boat No
        usort($receipts, function ($a, $b) {
            // 1. Manifest Number (Natural Sort)
            $manifestA = $a['manifest_number'];
            $manifestB = $b['manifest_number'];
            $cmpManifest = strnatcmp($manifestA, $manifestB);
            if ($cmpManifest !== 0) {
                return $cmpManifest;
            }

            // 2. Transaction Date (Ascending)
            $dateA = $a['transaction_date'];
            $dateB = $b['transaction_date'];
            if ($dateA != $dateB) {
                return $dateA <=> $dateB;
            }

            // 3. Boat Number (Natural Sort)
            return strnatcmp($a['boat_number'], $b['boat_number']);
        });

        // Convert total amount to words
        $totalAmount = $payments->sum('amount');

        $dollars = floor($totalAmount);
        $cents = round(($totalAmount - $dollars) * 100);

        $amountWords = "RINGGIT MALAYSIA " . $this->numberToWords($dollars) . " AND CENTS " . $this->numberToWords($cents) . " ONLY";

        $companyName = $user->company ? str_replace(' ', '_', $user->company->name) : 'Company';
        $fileName = $startDate->format('ymd') . '-' . $endDate->format('ymd') . '_PaymentReceipts_' . $companyName . '.pdf';

        $pdf = Pdf::loadView('pdf.payment.receipt', [
            'receipts' => $receipts,
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'company' => $user->company,
            'generated_at' => now(),
            'amountWords' => $amountWords,
            'totalAmount' => $totalAmount,
            'issuedBy' => $user->name,
            'title' => str_replace('.pdf', '', $fileName)
        ]);

        return $pdf->stream($fileName);
    }

    /**
     * Convert number to words (English)
     */
    private function numberToWords($number)
    {
        $number = (int) $number;

        if ($number === 0) {
            return 'ZERO';
        }

        $ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE'];
        $tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];
        $teens = ['TEN', 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN', 'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'];

        if ($number < 10) {
            return $ones[$number];
        } elseif ($number < 20) {
            return $teens[$number - 10];
        } elseif ($number < 100) {
            $ten = floor($number / 10);
            $one = $number % 10;
            return $tens[$ten] . ($one > 0 ? ' ' . $ones[$one] : '');
        } elseif ($number < 1000) {
            $hundred = floor($number / 100);
            $remainder = $number % 100;
            return $ones[$hundred] . ' HUNDRED' . ($remainder > 0 ? ' ' . $this->numberToWords($remainder) : '');
        } elseif ($number < 1000000) {
            $thousand = floor($number / 1000);
            $remainder = $number % 1000;
            return $this->numberToWords($thousand) . ' THOUSAND' . ($remainder > 0 ? ' ' . $this->numberToWords($remainder) : '');
        } elseif ($number < 1000000000) {
            $million = floor($number / 1000000);
            $remainder = $number % 1000000;
            return $this->numberToWords($million) . ' MILLION' . ($remainder > 0 ? ' ' . $this->numberToWords($remainder) : '');
        }

        return (string) $number;
    }
}
