<?php

namespace App\Http\Controllers\Auth;

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

    public function form(Request $request)
    {
        /**
         * @var User
         */
        $userAuth = Auth::user();

        if ($request->session()->get("is_verified") == 1) {
            $role = $userAuth->roles()->first();
            return redirect()->route($role->default_route);
        }

        $otpable = Otpable::query()
            ->where("address", $userAuth->email)
            ->first();

        $code = $request->code ?? optional($otpable)->code;

        return Inertia::render("Auth/LoginOtp", [
            "title" => "Login OTP",
            "additional" => [
                "code" => $code,
                "email" => $userAuth->email,
                "urlResend" => route("login-otp.resend"),
                "urlSubmit" => route("login-otp.submit"),
                "urlBack" => route("login-otp.back-to-login"),
            ]
        ]);
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $arrRequest = $request->validate([
            'code' => ['required'],
            'otp' => ['required', 'max:5'],
        ]);

        /**
         * @var User
         */
        $user = Auth::user();
        $email = $user->email;

        $isExist = Otpable::query()
            ->where("code", $arrRequest["code"])
            ->where("otp", $arrRequest["otp"])
            ->where("address", $email)
            ->exists();

        $isProduction = config("app.env") == "production";

        if (!$isExist && $isProduction) {
            throw ValidationException::withMessages([
                "otp" => 'OTP Failed!'
            ]);
        }

        DB::transaction(function () use ($email) {
            Session::query()
                ->where("id", session()->getId())
                ->update([
                    "is_verified" => 1
                ]);

            Otpable::query()
                ->where("address", $email)
                ->delete();
        });

        $request->session()->put("is_verified", 1);

        $role = $user->roles()->first();

        return Inertia::location(route($role->default_route));
        // return redirect()->route($role->default_route);
    }

    public function backToLogin(Request $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        $role = $user->activeRole();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();

        return redirect()->route("login", [
            "role_id" => $role->id
        ]);
    }

    public function resend(Request $request, CreateOtp $action)
    {
        /**
         * @var User
         */
        $userAuth = Auth::user();

        $otp = DB::transaction(function () use ($userAuth, $action) {
            return $action->execute($userAuth);
        });

        SendEmailOtp::dispatch($otp->id);

        return redirect()->back()
            ->with("message", [
                "status" => "success",
                "message" => "OTP sent to your email!"
            ]);
    }
}
