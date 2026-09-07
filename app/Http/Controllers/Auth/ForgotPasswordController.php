<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailResetPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/ForgotPassword', [
            'url_submit' => route('forgot-password')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'ic_no' => 'required'
        ]);

        $user = User::whereEmail($request->email)->first();

        if (optional($user)->ic_no != $request->ic_no) {
            throw ValidationException::withMessages([
                "ic_no" => 'IC No. not match!'
            ]);
        }

        $token = Str::random(128);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        SendEmailResetPassword::dispatch($request->email, $token);

        return redirect()->back()
            ->with("message", [
                "status" => "success",
                "message" => "Reset Password Email Sent!"
            ]);;
    }
}
