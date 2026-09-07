<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;

class AppConfigController extends Controller
{
    public function show(Request $request, string $code)
    {
        $config = Config::query()
            ->where("code", $code)
            ->firstOrFail();

        return response()
            ->json([
                "code" => $code,
                "value" => $config->value
            ]);
    }
}
