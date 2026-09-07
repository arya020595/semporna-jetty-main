<?php

namespace App\Http\Controllers;

use App\Jobs\PushManifestNotification;
use App\Models\Manifest;
use App\Models\ManifestFee;
use App\Models\Payment;
use App\Services\Payment\SenangPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SenangPayController extends Controller
{
    protected SenangPayService $senangPayService;

    public function __construct(SenangPayService $senangPayService)
    {
        $this->senangPayService = $senangPayService;
    }

    /**
     * Handles the return URL from SenangPay (customer is redirected here).
     */
    public function handleReturn(Request $request)
    {
        $orderId = $request->order_id;
        $paymentUuid = str_replace(SenangPayService::PREFIX_CODE, "", $orderId);
        $payment = Payment::query()
            ->where("code", $paymentUuid)
            ->first();

        $senangPayAccount = $payment->merchant_id;

        $merchantId = config("senangpay." . $senangPayAccount . ".merchant_id");
        $secretKey = config("senangpay." . $senangPayAccount . ".secret_key");

        $checkCallback = $this->senangPayService
            ->setMerchantId($merchantId)
            ->setSecretKey($secretKey)
            ->verifyCallback($request->all());


        if ($checkCallback) {
            $statusId = $request->input('status_id');
            $orderId = $request->input('order_id');
            $transactionId = $request->input('transaction_id');
            $msg = $request->input('msg');

            $paymentCode = str_replace(SenangPayService::PREFIX_CODE, "", $orderId);

            $payment = Payment::query()
                ->where("code", $paymentCode)
                ->first();

            if (!$payment) {
                Log::info('Payment Not Exists: ' . $orderId);
                abort(404);
            }

            if ($statusId == '1') {
                // Payment successful
                Log::info('SenangPay return: Payment successful for Order ID: ' . $orderId);
                // Update your order status in the database
                // e.g., Order::where('id', $orderId)->update(['status' => 'completed', 'transaction_id' => $transactionId]);
                $arrData = [
                    'status' => 'success',
                    'message' => 'Payment Successful! Order ID: ' . $orderId,
                    'data' => $payment
                ];
            } else {
                // Payment failed or cancelled
                Log::warning('SenangPay return: Payment failed/cancelled for Order ID: ' . $orderId . '. Message: ' . $msg);
                // Update your order status in the database
                // e.g., Order::where('id', $orderId)->update(['status' => 'failed', 'transaction_id' => $transactionId, 'error_message' => $msg]);
                $arrData = [
                    'status' => 'failed',
                    'message' => 'Payment Failed or Cancelled.',
                    'data' => $payment
                ];
            }
        } else {
            // Hash verification failed
            $arrData = [
                'status' => 'error',
                'message' => 'Payment verification failed. Please contact support.',
                'data' => null
            ];
        }

        return Inertia::render('SenangPay/Return', [
            "title" => "Payment Status",
            "additional" => [
                'status' => $arrData["status"],
                'message' => $arrData["message"],
                "urlProceed" => route("panel.payment.index")
            ]
        ]);
    }

    /**
     * Handles the callback URL from SenangPay (IPN - Instant Payment Notification).
     * This should be a POST request from SenangPay's server.
     */
    public function handleCallback(Request $request)
    {
        Log::info('SenangPay callback received:', $request->all());

        if ($this->senangPayService->verifyCallback($request->all())) {
            $statusId = $request->input('status_id');
            $orderId = $request->input('order_id');
            $transactionId = $request->input('transaction_id');
            $msg = $request->input('msg');

            $paymentCode = str_replace(SenangPayService::PREFIX_CODE, "", $orderId);

            $payment = Payment::query()
                ->where("code", $paymentCode)
                ->first();

            if ($statusId == '1') {
                // Payment successful
                Log::info('SenangPay callback: Payment successful for Order ID: ' . $orderId);
                // IMPORTANT: Update your order status here. This is the most reliable notification.
                // e.g., Order::where('id', $orderId)->update(['status' => 'completed', 'transaction_id' => $transactionId]);

                $arrManifestId = $payment->manifestFee->pluck("manifest_id")
                    ->toArray();

                if (empty($arrManifestId) && !empty($payment->detail)) {
                    $arrManifestId = array_map('intval', explode(',', $payment->detail));
                }

                $updateData = [
                    "payment_status" => Manifest::PAYMENT_STATUS_PAID,
                    "is_final" => 1
                ];

                if (config('features.bypass_approval_flow')) {
                    // Auto-approve
                    $updateData['status'] = Manifest::STATUS_APPROVED;
                    // $updateData['jetty_approval_status'] = Manifest::STATUS_APPROVED;
                }

                // Include APPROVED status for additional payment scenarios
                Manifest::query()
                    ->whereIn("id", $arrManifestId)
                    ->whereIn("status", [
                        Manifest::STATUS_PENDING,
                        Manifest::STATUS_APPROVED,
                        Manifest::STATUS_APPROVED_PROGRESS,
                        Manifest::STATUS_AMEND
                    ])
                    ->update($updateData);

                ManifestFee::query()
                    ->whereIn("manifest_id", $arrManifestId)
                    ->where("status", ManifestFee::STATUS_PENDING)
                    ->update([
                        "status" => ManifestFee::STATUS_PAID
                    ]);

                // One manifest paid boat fee, so others become free
                $paidManifests = Manifest::whereIn('id', $arrManifestId)->get();
                foreach ($paidManifests as $paidManifest) {
                    $hasPaidBoatFee = $paidManifest->manifestFee()
                        ->where('boat_fee', '>', 0)
                        ->exists();

                    if (!$hasPaidBoatFee) {
                        continue;
                    }

                    $concurrentManifestIds = Manifest::query()
                        ->where(function ($query) use ($paidManifest) {
                            if ($paidManifest->boat_id) {
                                $query->where('boat_id', $paidManifest->boat_id);
                            } else {
                                $query->whereNull('boat_id')
                                    ->where('boat_number', $paidManifest->boat_number);
                            }
                        })
                        ->whereDate('departure_date', $paidManifest->departure_date)
                        ->where('id', '!=', $paidManifest->id)
                        ->where('status', '!=', Manifest::STATUS_REJECTED)
                        ->pluck('id');

                    if ($concurrentManifestIds->isNotEmpty()) {
                        ManifestFee::query()
                            ->whereIn('manifest_id', $concurrentManifestIds)
                            ->update(['boat_fee' => 0]);
                    }
                }

                // Update payment record status
                $payment->update([
                    "status" => Payment::STATUS_PAID
                ]);

                $arrManifest = Manifest::query()
                    ->whereIn("id", $arrManifestId)
                    ->where("status", Manifest::STATUS_APPROVED)
                    ->get();

                foreach ($arrManifest as $manifest) {
                    PushManifestNotification::dispatch($manifest->id);
                }
            } else {
                // Payment failed or cancelled
                Log::warning('SenangPay callback: Payment failed/cancelled for Order ID: ' . $orderId . '. Message: ' . $msg);
                // Update your order status
                // e.g., Order::where('id', $orderId)->update(['status' => 'failed', 'transaction_id' => $transactionId, 'error_message' => $msg]);
            }
            // Always return a 200 OK response to SenangPay to acknowledge receipt
            return response('OK', 200);
        } else {
            Log::error('SenangPay callback: Hash verification failed for Order ID: ' . $request->input('order_id'));
            // Return an error status if verification fails, though SenangPay might not retry
            return response('Verification Failed', 400);
        }
    }
}
