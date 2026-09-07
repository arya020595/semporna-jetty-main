<?php

namespace Database\Seeders;

use App\Models\Config;
use App\Models\ManifestFee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ConfigSeeder extends Seeder
{

    protected $headers = [];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Config::updateOrCreate([
            "code" => ManifestFee::CONFIG_KEY_MANFIEST_FEE
        ], [
            "code" => ManifestFee::CONFIG_KEY_MANFIEST_FEE,
            "name" => "Manifest Fee",
            "description" => "Manifest Fee",
            "value" => json_encode([
                "local_child_fee" => 2,
                "local_adult_fee" => 5,
                "foreign_child_fee" => 5,
                "foreign_adult_fee" => 10,

                "boat_fee" => 2
            ])
        ]);


        Config::updateOrCreate([
            "code" => Config::APP_VERSION
        ], [
            "code" => Config::APP_VERSION,
            "name" => "App Version",
            "description" => "Apps Version for Android or IOS",
            "value" => json_encode([
                "android" => "1.0.0",
                "ios" => "1.0.0"
            ])
        ]);


        Config::updateOrCreate([
            "code" => Config::APP_CONTACT_US
        ], [
            "code" => Config::APP_CONTACT_US,
            "name" => "App Contact Us",
            "description" => "Apps Contact Us",
            "value" => "+6737342727"
        ]);

        info("FINISH SEED " . __CLASS__);
    }
}
