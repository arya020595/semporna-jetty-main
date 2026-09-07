<?php

namespace Database\Seeders;

use App\Models\RefDestination;
use Illuminate\Database\Seeder;

class RefDestinationSeeder extends Seeder
{

    protected $headers = [];

    protected $arrDeparture = [
        "Semporna Jetty",
        "Seafest Jetty",
    ];

    protected $data = [
        "Mabul",
        "Kapalai",
        "Mataking",
        "Timba-Timba",
        "Pom-Pom",
        "Sibuan",
        "Bohey Dulang",
        "Mantabuan",
        "Pandanan",
        "Sipadan",
        "Larapan",
        "Bodgaya",
        "Maiga",
        "Sebangkat",
        "Tetagan",
        "Kalapuan",
        "Omadal",
        "Selakan",
        "Adal",
        "Denawan",
        "Gusungan",
        "Timbun Mata",
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        info("START SEED " . __CLASS__);

        $arrId = [];
        foreach ($this->arrDeparture as $key => $value) {
            $code = str_pad($key + 1, 5, "0", STR_PAD_LEFT);
            $destination = RefDestination::updateOrCreate([
                "title" => $value,
                "type" => RefDestination::TYPE_DEPARTURE,
            ], [
                "code" => "DPTR_" . $code,
                "title" => $value,
                "type" => RefDestination::TYPE_DEPARTURE,
            ]);

            $arrId[] = $destination->id;
        }

        foreach ($this->data as $key => $value) {
            $code = str_pad($key + 1, 5, "0", STR_PAD_LEFT);
            $destination = RefDestination::updateOrCreate([
                "title" => $value,
                "type" => RefDestination::TYPE_DESTINATION,
            ], [
                "code" => "DEST_" . $code,
                "title" => $value,
                "type" => RefDestination::TYPE_DESTINATION,
            ]);

            $arrId[] = $destination->id;
        }

        RefDestination::query()
            ->whereNotIn("id", $arrId)
            ->delete();

        info("FINISH SEED " . __CLASS__);
    }
}
