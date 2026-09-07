<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | This file contains feature flags for temporary or experimental features.
    | These can be toggled via environment variables.
    |
    */

    /**
     * Bypass Approval Flow
     *
     * When enabled, manifests will be auto-approved after payment without
     * requiring authorities or jetty approval. This is a temporary feature
     * to support launch when mobile app has security issues.
     *
     * Set to true to enable 3-step flow (create -> payment -> print)
     * Set to false for normal 5-step flow (create -> payment -> authorities approval -> jetty approval -> print)
     */
    'bypass_approval_flow' => env('BYPASS_APPROVAL_FLOW', false),

    /**
     * Bypass Seafest Jetty Payment
     *
     * When enabled, Seafest Jetty manifests will bypass online payment gateway
     * and be auto-approved immediately. Payment is handled manually/offline.
     * Display status will show "-" instead of "PAID" or "PENDING".
     *
     * Set to true to enable bypass (current Seafest situation - no SenangPay)
     * Set to false when Seafest SenangPay account is ready
     */
    'bypass_seafest_payment' => env('BYPASS_SEAFEST_PAYMENT', true),
];
