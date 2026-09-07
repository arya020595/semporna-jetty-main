<?php

namespace App\Http\Controllers\Auth;

use App\Actions\CreateOtp;
use App\Actions\User\SetUserAccessLog;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmailOtp;
use App\Models\User;
use App\Models\UserAccessLog;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class LoginController extends Controller
{


    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Auth::viaRemember();
        Auth::check();
        $this->middleware('guest')->except('logout');
    }

    public function form(Request $request)
    {
        return Inertia::render("Auth/Login", [
            "title" => "Login",
            "additional" => [
                "role_id" => $request->input("role_id")
            ]
        ]);
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request, SetUserAccessLog $action, CreateOtp $actionOtp)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $remember_me = $request->has('remember_me') ? true : false;

        if (Auth::attempt($credentials, $remember_me)) {
            $isVerified = $request->session()->get("is_verified");

            $request->session()->regenerate();

            /**
             * @var User
             */
            $user = Auth::user();
            if ($user->status != User::STATUS_ACTIVE) {
                Auth::logout();
                throw ValidationException::withMessages([
                    "email" => 'User is not Active!'
                ]);
            }

            $role = $user->roles()->first();

            if (
                !in_array($role->id, str_split($request->role_id))
                && $role->id != User::ROLE_SUPERADMIN
            ) {
                Auth::logout();
                throw ValidationException::withMessages([
                    "email" => 'User role not match!'
                ]);
            }

            if ($isVerified) {
                $request->session()->put("is_verified", 1);
            }

            // OTP disabled
            // $otp = $actionOtp->execute($user);
            // SendEmailOtp::dispatch($otp->id);

            // Skip OTP page
            $request->session()->put("is_verified", 1);

            $action->execute($user->id, UserAccessLog::TYPE_LOGIN);

            return redirect()->route($role->default_route);
        }

        throw ValidationException::withMessages([
            "email" => 'Username or Password not match!'
        ]);
    }
}
