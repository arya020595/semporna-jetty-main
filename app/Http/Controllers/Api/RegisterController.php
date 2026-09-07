<?php

namespace App\Http\Controllers\Api;

use App\Actions\CompanyProfile\GenerateNumbers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{

    public function store(RegisterRequest $request, GenerateNumbers $generateNumbers)
    {
        // Log::info('Register API Request Payload:', $request->all());

        $arrData = $request->validated();

        $roleId = $arrData["role"] ?? User::ROLE_PDRM;

        $user = DB::transaction(function () use ($arrData, $roleId, $generateNumbers) {
            $user = User::create([
                "email" => $arrData["email"],
                "name" => $arrData["name"],
                "ic_no" => $arrData["ic_no"],
                "password" => Hash::make($arrData["password"]),
                "status" => User::STATUS_PENDING,
                "jetty_id" => $arrData["jetty_id"] ?? null,
            ]);

            $user->assignRole($roleId);

            return $user;
        });

        return response()
            ->json([
                "message" => "Register success, wait for approval!",
                "data" => [
                    "user" => [
                        "email" => $user->email,
                        "name" => $user->name
                    ]
                ]
            ]);
    }
}
