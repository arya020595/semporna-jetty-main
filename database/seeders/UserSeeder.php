<?php

namespace Database\Seeders;

use App\Helpers\EtlHelper;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    protected $headers = [];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        DB::table("model_has_roles")->truncate();

        $arrUser = [
            [
                "name" => "Administrator",
                "code" => '',
                "staf_id" => '',
                "tel_no" => '',
                "email" => "admin@email.com",
                "password" => Hash::make("abc12345"),
                "created_at" => now(),
                "updated_at" => now(),
                "role" => User::ROLE_SUPERADMIN,
            ],
        ];

        foreach ($arrUser as $arrVal) {
            $role = $arrVal["role"];
            unset($arrVal['role']);

            $user = User::create($arrVal);

            $user->assignRole($role);
        }

        info("FINISH SEED " . __CLASS__);
    }
}
