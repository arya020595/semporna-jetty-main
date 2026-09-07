<?php

namespace Database\Seeders;

use App\Helpers\EtlHelper;
use App\Models\Boatman;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DummyCompanySeeder extends Seeder
{

    protected $headers = [];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $user = User::updateOrCreate([
            "email" => "company@email.com",
        ], [
            "name" => "Company",
            "code" => '',
            "staf_id" => '',
            "tel_no" => '',
            "email" => "company@email.com",
            "password" => Hash::make("abc12345"),
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        $user->assignRole(User::ROLE_AGENT);

        $company = Company::updateOrCreate([
            "user_id" => $user->id
        ], [
            "user_id" => $user->id,
            "registration_no" => "0001",
            "name" => "PT. Company Dummy"
        ]);

        for ($i = 0; $i < 2; $i++) {
            $boat = $company->boat()->create([
                "number" => "B-" . $faker->randomNumber(6),
                "license" => "LC-" . $faker->randomNumber(6)
            ]);

            for ($j = 0; $j < 2; $j++) {
                $company->boatman()->create([
                    "boat_id" => $boat->id,
                    "name" => $faker->name(),
                    "ic_no" => $faker->randomNumber(7),
                    "mate_card" => $faker->randomNumber(7),
                    "seaman_card_no" => $faker->randomNumber(7),
                    "type" => Boatman::TYPE_BOATMAN
                ]);

                $company->boatman()->create([
                    "boat_id" => $boat->id,
                    "name" => $faker->name(),
                    "ic_no" => $faker->randomNumber(7),
                    "mate_card" => $faker->randomNumber(7),
                    "type" => Boatman::TYPE_ASSISTANT
                ]);
            }
        }

        $arrBoatman = [
            Boatman::TYPE_GUIDE,
            Boatman::TYPE_DIVEMASTER,
            Boatman::TYPE_INSTRUCTOR
        ];

        foreach ($arrBoatman as $value) {
            $company->boatman()
                ->where("type", $value)
                ->delete();

            for ($i = 0; $i < 3; $i++) {
                $company->boatman()->create([
                    "name" => $faker->name(),
                    "ic_no" => $faker->randomNumber(7),
                    "type" => $value
                ]);
            }
        }

        info("FINISH SEED " . __CLASS__);
    }
}
