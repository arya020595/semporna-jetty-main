<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class SyncUser
{

    /**
     * Execute the action
     *
     * @param  array  $data
     * @return User
     * @throws Throwable
     */
    public function execute(array $data): User
    {
        /** @var User|null $user */
        $user = User::where("code", $data['code'])->first();

        return DB::transaction(function () use ($data, $user) {
            $authUserId = Auth::id() ?? 0;

            if (!$user) {
                /** @var User $user */
                $user = User::create([
                    "name" => $data['name'],
                    "code" => $data['code'],
                    "ref_division_id" => $data['division'],
                    "ref_position_id" => $data['position'],
                    "tel_no" => $data['tel_no'],
                    "fax_no" => $data['fax_no'],
                    "email" => $data['email']
                ]);
            } else {
                $user->update([
                    "name" => $data['name'],
                    "code" => $data['code'],
                    "ref_division_id" => $data['division'],
                    "ref_position_id" => $data['position'],
                    "tel_no" => $data['tel_no'],
                    "fax_no" => $data['fax_no'],
                    "email" => $data['email']
                ]);
            }

            $roles = $data["roles"];
            $user->assignRole($roles);

            return $user;
        });
    }
}
