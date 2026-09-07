<?php

namespace App\Services\Payment;

use App\Models\Manifest;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SenangPayService
{
    const PREFIX_CODE = "SJ-ORDER-";
    protected string $merchantId;
    protected string $secretKey;
    protected string $baseUrl;
    protected string $verifyUrl;

    public function __construct()
    {
        $this->merchantId = config('senangpay.hartawan_stabil.merchant_id') ?? '';
        $this->secretKey = config('senangpay.hartawan_stabil.secret_key') ?? '';
        $this->baseUrl = config('senangpay.base_url');
        $this->verifyUrl = config('senangpay.verify_url');

        if (empty($this->merchantId) || empty($this->secretKey)) {
            Log::error('SenangPay credentials are not configured in .env file.');
            throw new \Exception('SenangPay credentials missing.');
        }
    }

    public function setMerchantId(string $merchantId)
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    public function setSecretKey(string $secretKey)
    {
        $this->secretKey = $secretKey;
        return $this;
    }

    /**
     * Generates the SenangPay payment URL.
     *
     * @param float $amount The payment amount (e.g., 10.50)
     * @param string $orderId Your unique order ID
     * @param string $detail A description of the item/order
     * @param string $name Customer's name
     * @param string $email Customer's email
     * @param string $phone Customer's phone number
     * @param string $returnUrl URL to redirect to after payment (success/failure)
     * @param string $callbackUrl URL for SenangPay to send IPN (Instant Payment Notification)
     * @return string The full payment URL
     * @throws \Exception
     */
    public function generatePaymentUrl(
        float $amount,
        string $orderId,
        string $detail,
        string $name,
        string $email,
        string $phone,
        string $returnUrl,
        string $callbackUrl
    ): string {
        // Ensure amount is in correct format (e.g., 10.00)
        $amount = number_format($amount, 2, '.', '');

        // Hash for the 'hash' parameter (v2 API hash calculation)
        // This hash is for verifying the core transaction details
        $hash = hash_hmac('sha256', $this->secretKey . $detail . $amount . $orderId, $this->secretKey);

        Log::info("genreted hash: " . $this->secretKey . urlencode($detail) . urlencode($amount) . $orderId);

        // Hash for the 'hash_value' parameter (for customer details)
        // This hash is for verifying the customer details passed
        $hashValue = hash_hmac('sha256', $detail . $name . $email . $phone, $this->secretKey);

        $parameters = [
            'amount' => $amount,
            'order_id' => $orderId,
            'detail' => $detail,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            // 'return_url' => $returnUrl,
            // 'callback_url' => $callbackUrl,
            'hash' => $hash,
            // 'hash_value' => $hashValue,
        ];

        // Build the query string
        $queryString = http_build_query($parameters);

        return $this->baseUrl . 'payment/' . $this->merchantId . '?' . $queryString;
    }

    /**
     * Verifies the integrity of the SenangPay callback (IPN/Return URL) data.
     *
     * @param array $callbackData The data received from SenangPay (e.g., $_GET or $_POST)
     * Expected keys: 'status_id', 'order_id', 'msg', 'transaction_id', 'hash'
     * @return bool True if the hash is valid, false otherwise.
     */
    public function verifyCallback(array $callbackData): bool
    {
        // Check if all required parameters are present
        if (!isset($callbackData['status_id'], $callbackData['order_id'], $callbackData['msg'], $callbackData['transaction_id'], $callbackData['hash'])) {
            Log::warning('SenangPay callback data missing required parameters.', $callbackData);
            return false;
        }

        $statusId = $callbackData['status_id'];
        $orderId = $callbackData['order_id'];
        $transactionId = $callbackData['transaction_id'];
        $msg = $callbackData['msg'];

        $receivedHash = $callbackData['hash'];

        // Calculate the expected hash based on SenangPay's verification formula
        // Hashing for verification: sha256(secret_key + status_id + order_id + transaction_id)

        $paymentUuid = str_replace(self::PREFIX_CODE, "", $orderId);
        $payment = Payment::query()
            ->where("code", $paymentUuid)
            ->first();

        if (!$payment) {
            Log::warning('Payment not found.', $callbackData);
            return false;
        }

        $expectedHash = hash_hmac('sha256', $this->secretKey . urldecode($statusId) . $orderId . $transactionId . $msg, $this->secretKey);

        Log::info("expected genreted hash: " . $this->secretKey . urldecode($statusId) . $orderId . $transactionId . $msg);

        // $expectedHash = hash_hmac('sha256', $this->secretKey . $statusId . $orderId . $transactionId, $this->secretKey);

        if ($receivedHash === $expectedHash) {
            Log::info('SenangPay callback hash verified successfully for Order ID: ' . $orderId);
            return true;
        } else {
            Log::warning('SenangPay callback hash verification failed for Order ID: ' . $orderId, [
                'received_hash' => $receivedHash,
                'expected_hash' => $expectedHash,
                'callback_data' => $callbackData,
            ]);
            return false;
        }
    }

    /**
     * Queries SenangPay for a transaction status (optional, for more robust verification).
     * This uses the Transaction Query API.
     *
     * @param string $orderId Your unique order ID
     * @return array|null Transaction details or null if not found/error
     */
    public function queryTransaction(string $orderId): ?array
    {
        $hash = hash_hmac('sha256', $this->secretKey . $this->merchantId . $orderId, $this->secretKey);

        $parameters = [
            'merchant_id' => $this->merchantId,
            'order_id' => $orderId,
            'hash' => $hash,
        ];

        $queryString = http_build_query($parameters);
        $url = $this->verifyUrl . 'gettransaction?' . $queryString;

        try {
            $client = new \GuzzleHttp\Client(); // Requires Guzzle HTTP client
            $response = $client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['status']) && $data['status'] === '1') {
                Log::info('SenangPay transaction query successful for Order ID: ' . $orderId, $data);
                return $data;
            } else {
                Log::warning('SenangPay transaction query failed or not found for Order ID: ' . $orderId, $data);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error querying SenangPay transaction for Order ID: ' . $orderId . ' - ' . $e->getMessage());
            return null;
        }
    }
}
