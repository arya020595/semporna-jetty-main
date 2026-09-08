<?php

return [

    /*
    |--------------------------------------------------------------------------
    | External Partner API Token
    |--------------------------------------------------------------------------
    |
    | Static bearer token used to authenticate external partner systems
    | (e.g. Semporna Jetty Resort) calling this app's /api/external/* routes.
    |
    */

    'token' => env('EXTERNAL_API_TOKEN'),
];
