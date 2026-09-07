<?php

namespace Database\Seeders;

use App\Models\Guest;
use App\Models\RefActivity;
use App\Models\RefDestination;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefActivitySeeder extends Seeder
{

    protected $headers = [];

    protected $data = [
        "Snorkeling",
        "Fun Dive / Diving",
        "Discover Scuba Diving",
        "Fishing",
        "Hiking",
        "OW Course Day 1",
        "OW Course Day 2",
        "AOW Course Day 1",
        "AOW Course Day 2",
        "Island Hopping",
        "Check In Resort",
        "Non-Diver",
        "Free Diving",
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('guest_has_activities')->truncate();
        Guest::truncate();
        RefActivity::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        info("START SEED " . __CLASS__);


        foreach ($this->data as $key => $value) {
            $code = str_pad($key + 1, 5, "0", STR_PAD_LEFT);
            $result = RefActivity::create([
                "code" => "ACT_" . $code,
                "title" => $value
            ]);
        }
        info("FINISH SEED " . __CLASS__);
    }
}
