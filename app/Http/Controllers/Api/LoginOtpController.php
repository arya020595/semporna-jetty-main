<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateOtp;
use App\Actions\User\SetUserAccessLog;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmailOtp;
use App\Models\Otpable;
use App\Models\Session;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Illuminate\Support\Str;

class LoginOtpController extends Controller
{

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request, SetUserAccessLog $action)
    {
        $arrRequest = $request->validate([
            'uuid' => ['required'],
            'otp' => ['required', 'max:5'],
        ]);

        /**
         * @var User
         */
        $user = Auth::user();
        $email = $user->email;

        $isExist = Otpable::query()
            ->where("code", $arrRequest["uuid"])
            ->where("otp", $arrRequest["otp"])
            ->where("address", $email)
            ->exists();

        $bypassOTP = config("auth.bypass_otp");

        $isExist = $bypassOTP && $bypassOTP == $arrRequest["otp"]
            ? true
            : $isExist;

        $isProduction = config("app.env") == "production";

        if (!$isExist && $isProduction) {
            throw ValidationException::withMessages([
                "otp" => 'OTP Failed!'
            ]);
        }

        $token = DB::transaction(function () use ($user) {
            $user->tokens()->where('name', 'auth_token')->delete();
            $token = $user->createToken('auth_token', ['authorities'])->plainTextToken;

            Otpable::query()
                ->where("address", $user->email)
                ->delete();

            return $token;
        });

        return response()->json([
            "status" => true,
            "message" => "OTP Validation Success!",
            "data" => [
                "access_token" => $token
            ]
        ], 200);
    }

    public function requestOtp(CreateOtp $action)
    {
        /**
         * @var User
         */
        $userAuth = Auth::user();

        $otp = DB::transaction(function () use ($userAuth, $action) {
            return $action->execute($userAuth);
        });

        SendEmailOtp::dispatch($otp->id);

        return response()->json([
            "status" => true,
            "message" => "OTP sent sucess!",
            "data" => [
                "uuid" => $otp->code
            ]
        ], 200);
    }
}
