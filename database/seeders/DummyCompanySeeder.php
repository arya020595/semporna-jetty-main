<?php

namespace Database\Seeders;

use App\Helpers\EtlHelper;
use App\Models\Boatman;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyCompanySeeder extends Seeder
{

    protected $headers = [];

    /**
     * fzaninotto/faker is require-dev only (not shipped in the production/
     * staging image, which is built with `composer install --no-dev`), so
     * this seeder must not depend on it — it needs to be runnable in every
     * environment it might be invoked in.
     */
    protected $names = [
        'Ahmad bin Yusof', 'Rahim bin Osman', 'Faizal Ismail', 'Siti Aminah', 'Kamarul Zaman',
        'Hassan Ali', 'Mohd Firdaus', 'Azman Yaacob', 'Noraini Ibrahim', 'Ramli Hashim',
        'Zulkifli Bakar', 'Suhaimi Abdullah', 'Halim Razak', 'Fauzi Karim', 'Rosli Ahmad',
    ];

    protected function randomName(): string
    {
        return $this->names[array_rand($this->names)];
    }

    protected function randomNumber(int $digits): string
    {
        return (string) random_int((int) str_pad('1', $digits, '0'), (int) str_pad('9', $digits, '9'));
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
                "number" => "B-" . $this->randomNumber(6),
                "license" => "LC-" . $this->randomNumber(6)
            ]);

            for ($j = 0; $j < 2; $j++) {
                $company->boatman()->create([
                    "boat_id" => $boat->id,
                    "name" => $this->randomName(),
                    "ic_no" => $this->randomNumber(7),
                    "mate_card" => $this->randomNumber(7),
                    "seaman_card_no" => $this->randomNumber(7),
                    "type" => Boatman::TYPE_BOATMAN
                ]);

                $company->boatman()->create([
                    "boat_id" => $boat->id,
                    "name" => $this->randomName(),
                    "ic_no" => $this->randomNumber(7),
                    "mate_card" => $this->randomNumber(7),
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
                    "name" => $this->randomName(),
                    "ic_no" => $this->randomNumber(7),
                    "type" => $value
                ]);
            }
        }

        info("FINISH SEED " . __CLASS__);
    }
}
