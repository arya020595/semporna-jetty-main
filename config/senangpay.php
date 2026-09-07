<?php

return [
    "hartawan_stabil" => [
        'merchant_id' => env('SENANGPAY_MERCHANT_ID', ''),
        'secret_key' => env('SENANGPAY_SECRET_KEY', ''),
    ],
    "seafest" => [
        'merchant_id' => env('SEAFEST_SENANGPAY_MERCHANT_ID', ''),
        'secret_key' => env('SEAFEST_SENANGPAY_SECRET_KEY', ''),
    ],
    'base_url' => env('SENANGPAY_BASE_URL', 'https://sandbox.senangpay.my/'), // Use sandbox for testing
    'verify_url' => env('SENANGPAY_VERIFY_URL', 'https://sandbox.senangpay.my/querytransaction/'), // For transaction query API
];
