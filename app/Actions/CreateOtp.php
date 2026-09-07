<?php

namespace App\Actions;

use App\Models\Otpable;
use App\Models\User;
use Throwable;
use Illuminate\Support\Str;

class CreateOtp
{

    /**
     * Execute the action
     *
     * @param  array  $data
     * @return User
     * @throws Throwable
     */
    public function execute(User $user): Otpable
    {
        $otp = Otpable::query()
            ->where("address", $user->email)
            ->where("type", Otpable::TYPE_EMAIL)
            ->where("expired_at", ">=", now()->addMinutes(3))
            ->first();

        if ($otp) {
            return $otp;
        }

        $otp = Otpable::create([
            "code" => Str::uuid(),
            "otp" => rand(10000, 99999),
            "address" => $user->email,
            "type" => Otpable::TYPE_EMAIL,
            "expired_at" => now()->addMinutes(5)
        ]);

        Otpable::query()
            ->where("id", "!=", $otp->id)
            ->where("address", $user->email)
            ->delete();

        return $otp;
    }
}
