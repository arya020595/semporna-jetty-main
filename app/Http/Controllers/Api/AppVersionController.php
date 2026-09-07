<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    public function show(Request $request)
    {
        $osPlatform = $request->input("os");

        $config = Config::query()
            ->where("code", Config::APP_VERSION)
            ->first();

        $confValue = json_decode($config->value, true);
        $version = $confValue[$osPlatform] ?? null;

        return response()
            ->json([
                "version" => $version,
            ]);
    }
}
