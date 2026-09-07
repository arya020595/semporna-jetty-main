<?php

namespace App\Http\Middleware;

use App\Models\Session;
use Closure;
use Illuminate\Http\Request;

class SessionValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (($request->is('api/*'))) {
            if (!$request->user() || !$request->user()->tokenCan("authorities")) {
                return response()->json([
                    "status" => false,
                    "message" => "OTP Verification is required!"
                ], 403);
            }
        } else {

            if ($request->session()->get("is_verified") != 1) {
                return redirect()->route('login-otp');
            }
        }

        return $next($request);
    }
}
