<?php

namespace Database\Seeders;

use App\Models\RefDestination;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{

    protected $headers = [];

    protected $arrDeparture = [
        "Semporna Jetty",
        "Seafest Jetty",
    ];

    protected $ticketTempatan = [
        [
            "prefix" => "",
            "name" => "Dalam Simpanan (66 SET)",
            "category" => "TEMPATAN",
            "start" => 34901,
            "end" => 38200
        ],
        [
            "prefix" => "",
            "name" => "Happy Divers (3 SET)",
            "category" => "TEMPATAN",
            "start" => 34651,
            "end" => 34800
        ],
        [
            "prefix" => "",
            "name" => "Sipadan Water Village (1 SET)",
            "category" => "TEMPATAN",
            "start" => 34401,
            "end" => 34450
        ],
        [
            "prefix" => "",
            "name" => "Borneo Jungle (1 SET)",
            "category" => "TEMPATAN",
            "start" => 34801,
            "end" => 34850
        ],
        [
            "prefix" => "",
            "name" => "Smart (3 SET)",
            "category" => "TEMPATAN",
            "start" => 34501,
            "end" => 34650
        ],
        [
            "prefix" => "",
            "name" => "Seaventures (1 SET)",
            "category" => "TEMPATAN",
            "start" => 34451,
            "end" => 34500
        ],
        [
            "prefix" => "",
            "name" => "Zafqan (1 SET)",
            "category" => "TEMPATAN",
            "start" => 34851,
            "end" => 34900
        ],
    ];

    protected $ticketBot = [
        [
            "prefix" => "",
            "name" => "Dalam Simpanan (22 SET)",
            "category" => "BOT",
            "start" => 29301,
            "end" => 30400
        ],
        [
            "prefix" => "",
            "name" => "SMART (1 SET)",
            "category" => "BOT",
            "start" => 28101,
            "end" => 28150
        ],
        [
            "prefix" => "",
            "name" => "Zafqan Travel (5 SET)",
            "category" => "BOT",
            "start" => 27951,
            "end" => 28050
        ],
        [
            "prefix" => "",
            "name" => "Zafqan Travel (5 SET)",
            "category" => "BOT",
            "start" => 28351,
            "end" => 28450
        ],
        [
            "prefix" => "",
            "name" => "Zafqan Travel (5 SET)",
            "category" => "BOT",
            "start" => 28901,
            "end" => 28950
        ],
        [
            "prefix" => "",
            "name" => "Happy Divers (2 SET)",
            "category" => "BOT",
            "start" => 28251,
            "end" => 28350
        ],
        [
            "prefix" => "",
            "name" => "Borneo Divers (5 SET)",
            "category" => "BOT",
            "start" => 28601,
            "end" => 28850
        ],
        [
            "prefix" => "",
            "name" => "Infinity Scuba (2 SET)",
            "category" => "BOT",
            "start" => 29101,
            "end" => 29200
        ],
        [
            "prefix" => "",
            "name" => "Seaventures (1 SET)",
            "category" => "BOT",
            "start" => 29001,
            "end" => 29050
        ],
    ];


    protected $ticketLuarNegara = [
        [
            "prefix" => "F1",
            "name" => "Dalam Simpanan (57 SET)",
            "category" => "LUAR NEGARA",
            "start" => 46651,
            "end" => 49500
        ],
        [
            "prefix" => "F1",
            "name" => "BUMS (8 SET)",
            "category" => "LUAR NEGARA",
            "start" => 42001,
            "end" => 42100
        ],
        [
            "prefix" => "F1",
            "name" => "BUMS (8 SET)",
            "category" => "LUAR NEGARA",
            "start" => 42501,
            "end" => 42700
        ],
        [
            "prefix" => "F1",
            "name" => "BUMS (8 SET)",
            "category" => "LUAR NEGARA",
            "start" => 43401,
            "end" => 43500
        ],
        [
            "prefix" => "F1",
            "name" => "Aqua Planet (4 SET)",
            "category" => "LUAR NEGARA",
            "start" => 45001,
            "end" => 45200
        ],
        [
            "prefix" => "F1",
            "name" => "SMART (1 SET)",
            "category" => "LUAR NEGARA",
            "start" => 42151,
            "end" => 42300
        ],
        [
            "prefix" => "F1",
            "name" => "SMART (1 SET)",
            "category" => "LUAR NEGARA",
            "start" => 43201,
            "end" => 43750
        ],
        [
            "prefix" => "F1",
            "name" => "Borneo Jungle (11 SET)",
            "category" => "LUAR NEGARA",
            "start" => 42301,
            "end" => 42500
        ],
        [
            "prefix" => "F1",
            "name" => "Borneo Jungle (11 SET)",
            "category" => "LUAR NEGARA",
            "start" => 44251,
            "end" => 44450
        ],
        [
            "prefix" => "F1",
            "name" => "Borneo Jungle (11 SET)",
            "category" => "LUAR NEGARA",
            "start" => 45701,
            "end" => 45800
        ],
        [
            "prefix" => "F1",
            "name" => "Borneo Jungle (11 SET)",
            "category" => "LUAR NEGARA",
            "start" => 46601,
            "end" => 46650
        ],
        [
            "prefix" => "F1",
            "name" => "Zafqan Travel (36 SET)",
            "category" => "LUAR NEGARA",
            "start" => 42951,
            "end" => 43250
        ],
        [
            "prefix" => "F1",
            "name" => "Zafqan Travel (36 SET)",
            "category" => "LUAR NEGARA",
            "start" => 43751,
            "end" => 44250
        ],
        [
            "prefix" => "F1",
            "name" => "Zafqan Travel (36 SET)",
            "category" => "LUAR NEGARA",
            "start" => 44451,
            "end" => 44950
        ],
        [
            "prefix" => "F1",
            "name" => "Zafqan Travel (36 SET)",
            "category" => "LUAR NEGARA",
            "start" => 45701,
            "end" => 46300
        ],
        [
            "prefix" => "F1",
            "name" => "Happy Divers (2 SET)",
            "category" => "LUAR NEGARA",
            "start" => 28251,
            "end" => 28350
        ],
        [
            "prefix" => "F1",
            "name" => "Infinity (10 SET)",
            "category" => "LUAR NEGARA",
            "start" => 42701,
            "end" => 42950
        ],
        [
            "prefix" => "F1",
            "name" => "Infinity (10 SET)",
            "category" => "LUAR NEGARA",
            "start" => 46301,
            "end" => 46550
        ],
        [
            "prefix" => "F1",
            "name" => "Seaventures (3 SET)",
            "category" => "LUAR NEGARA",
            "start" => 42101,
            "end" => 42150
        ],
        [
            "prefix" => "F1",
            "name" => "Seaventures (3 SET)",
            "category" => "LUAR NEGARA",
            "start" => 44951,
            "end" => 45000
        ],
        [
            "prefix" => "F1",
            "name" => "Seaventures (3 SET)",
            "category" => "LUAR NEGARA",
            "start" => 46551,
            "end" => 46600
        ],
        [
            "prefix" => "F1",
            "name" => "SIPADAN WATER VILLAGE (1 SET)",
            "category" => "LUAR NEGARA",
            "start" => 43251,
            "end" => 43300
        ],
        [
            "prefix" => "F1",
            "name" => "HAPPY DIVERS (2 SET)",
            "category" => "LUAR NEGARA",
            "start" => 43301,
            "end" => 43400
        ],
        [
            "prefix" => "F1",
            "name" => "BORNEO DIVERS (10 SET)",
            "category" => "LUAR NEGARA",
            "start" => 45201,
            "end" => 45700
        ],
    ];

    protected $ticketLuarNegaraBudak = [
        [
            "prefix" => "IC0",
            "name" => "Dalam Simpanan (18 SET)",
            "category" => "LUAR NEGARA (BUDAK)",
            "start" => 7601,
            "end" => 8500
        ],
        [
            "prefix" => "IC0",
            "name" => "SMART (2 SET)",
            "category" => "LUAR NEGARA (BUDAK)",
            "start" => 7501,
            "end" => 7600
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        info("START SEED " . __CLASS__);

        $departure = RefDestination::query()
            ->where("code", RefDestination::CODE_SEAFEST)
            ->first();

        $this->insertBatch($this->ticketTempatan, $departure->id);
        $this->insertBatch($this->ticketBot, $departure->id);
        $this->insertBatch($this->ticketLuarNegara, $departure->id);
        $this->insertBatch($this->ticketLuarNegaraBudak, $departure->id);

        info("FINISH SEED " . __CLASS__);
    }

    protected function insertBatch($arrData, $departureId)
    {
        foreach ($arrData as $item) {
            info($item["category"] . " - " . $item["name"]);

            for ($number = $item["start"]; $number <= $item["end"]; $number++) {
                $code = $item["prefix"] . "$number";
                Ticket::updateOrCreate([
                    "code" => $code,
                ], [
                    "code" => $code,
                    "name" => $item["name"],
                    "category_name" => $item["category"],
                    "departure_id" => $departureId,
                    "status" => Ticket::STATUS_AVAILABLE
                ]);
            }
        }
    }
}
