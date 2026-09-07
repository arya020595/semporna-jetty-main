<?php

namespace App\Http\Controllers\Api;

use App\Actions\User\SetUserAccessLog;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

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

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request, SetUserAccessLog $action)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember_me = $request->has('remember_me') ? true : false;

        if (Auth::attempt($credentials, $remember_me)) {
            /**
             * @var User
             */
            $user = Auth::user();

            $allowedRoles = [
                User::ROLE_JABATAN_LAUT,
                User::ROLE_JABATAN_PELABUHAN,
                User::ROLE_PDRM,
                User::ROLE_SABAH_PARKS
            ];

            if (!$user->hasAnyRole($allowedRoles)) {
                throw ValidationException::withMessages([
                    "email" => 'User not allowed to use this apps!'
                ]);
            }

            $token = $user->createToken('auth_token', [])->plainTextToken;

            $action->execute($user->id, UserAccessLog::TYPE_LOGIN);

            return response()
                ->json([
                    "message" => "Login Success!",
                    "data" => [
                        "access_token" => $token
                    ]
                ]);
        }

        throw ValidationException::withMessages([
            "email" => 'Username or Password not match!'
        ]);
    }

    public function logout()
    {
        /**
         * @var User
         */
        $user = Auth::user();
        $user->tokens()->delete();

        return response()
            ->json([
                'message' => 'You have successfully logged out and the token was successfully deleted'
            ]);
    }
}
