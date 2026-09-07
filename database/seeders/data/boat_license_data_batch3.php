<?php

/**
 * Boat License Data - Batch 3
 *
 * This file contains hardcoded boat license data for seeding (Batch 3).
 * Separated from BoatLicenseSeeder for better organization and maintainability.
 *
 * Companies in Batch 3:
 * - 77. RED DIVERS SDN BHD
 * - 79. SEAHUNTER PRO FISHING TRAVEL & TOURS SDN BHD
 * - 83. SINGAMATA ADVENTURES AND REEF RESORT SDN BHD
 * - 90. YUN SHENG TRAVEL & RENT A CAR SDN BHD
 * - 98. DIVE MONSTER TRAVEL & TOURS SDN BHD
 * - 101. SEMPORNA PARADISE TRAVEL & TOUR SB
 * - 104. SEAVENTURE DIVE RIG SDN BHD
 *
 * Data structure for each boat:
 * - company_id: ID of existing company
 * - license: Boat license number (will be normalized)
 * - capacity: Total passenger + crew capacity
 * - expiry_date: License expiry date (YYYY-MM-DD)
 * - boatmen: Array of boatmen (main boatman + assistants)
 *   - type: 1 = TYPE_BOATMAN, 2 = TYPE_ASSISTANT
 *   - name, ic_no, mate_card, seaman_card_no
 * - files: Array of file paths in storage/app/ (must be uploaded first)
 */

return [
    // ========================================
    // Company 77: RED DIVERS SDN BHD
    // ========================================
    [
        'company_id' => 77,
        'license' => 'SA 8705/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-09-29',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'JURKARDI BIN ABDULAH',
                'ic_no' => '921028127959',
                'mate_card' => '84002008DIV_C',
                'seaman_card_no' => '201784004904',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SAZALI BIN BINSALI',
                'ic_no' => '851011126123',
                'mate_card' => '84002006DIV_C4130',
                'seaman_card_no' => '201281006406',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'NATHAN JAY JEREMY',
                'ic_no' => '950913125625',
                'mate_card' => '84002009DIV_C',
                'seaman_card_no' => '202384009414',
            ],
        ],
        'files' => [
            'boat_license/SA 8705-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 79: SEAHUNTER PRO FISHING TRAVEL & TOURS SDN BHD
    // ========================================
    [
        'company_id' => 79,
        'license' => 'SA 35/5/P',
        'capacity' => 20, // Total passengers & crew
        'expiry_date' => '2025-12-12',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MOHD ALI BIN SAPILIN',
                'ic_no' => '830303126499',
                'mate_card' => '84001462DIV_C',
                'seaman_card_no' => '201984006182',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SADDAM BIN DAKIB',
                'ic_no' => '901223126275',
                'mate_card' => '84001714DIV_C',
                'seaman_card_no' => '201884005420',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'RANO BIN AMLUN',
                'ic_no' => '871113495809',
                'mate_card' => '84001485DIV_C',
                'seaman_card_no' => '201884005478',
            ],
        ],
        'files' => [
            'boat_license/SA 35-5-P.pdf',
        ],
    ],
    [
        'company_id' => 79,
        'license' => 'SA 7374/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-02-02',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'SADDAM BIN DAKIB',
                'ic_no' => '901223126275',
                'mate_card' => '84001714DIV_C',
                'seaman_card_no' => '201884005420',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD ALI BIN SAPILIN',
                'ic_no' => '830303126499',
                'mate_card' => '84001462DIV_C',
                'seaman_card_no' => '201984006182',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MUHAMMAD YAKIM BIN ROSLIN',
                'ic_no' => '031023121705',
                'mate_card' => '84001965DIV_C',
                'seaman_card_no' => '202382001078',
            ],
        ],
        'files' => [
            'boat_license/SA 7374-5-P.pdf',
        ],
    ],
    [
        'company_id' => 79,
        'license' => 'SA 10439/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-04-24',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MUHAMMAD JAIS BIN MUHD DESA',
                'ic_no' => '050623121083',
                'mate_card' => '82000082DIV_C',
                'seaman_card_no' => '202384009268',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MUHAMMAD YAKIM BIN ROSLIN',
                'ic_no' => '031023121705',
                'mate_card' => '84001965DIV_C',
                'seaman_card_no' => '202382001078',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD ALI BIN SAPILIN',
                'ic_no' => '830303126499',
                'mate_card' => '84001462DIV_C',
                'seaman_card_no' => '201984006182',
            ],
        ],
        'files' => [
            'boat_license/SA 10439-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 83: SINGAMATA ADVENTURES AND REEF RESORT SDN BHD
    // ========================================
    [
        'company_id' => 83,
        'license' => 'SA 7/5/P',
        'capacity' => 31, // Total passengers & crew
        'expiry_date' => '2026-07-04',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'KALOY BIN EMLAN',
                'ic_no' => '870313495075',
                'mate_card' => '84001978DIV_C4112',
                'seaman_card_no' => '201484003081',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'NAZREIN BIN OMAR',
                'ic_no' => '930303125769',
                'mate_card' => '84001979DIV_C',
                'seaman_card_no' => '201584003405',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'WALIMAR BIN ISMAEL',
                'ic_no' => '890125126123',
                'mate_card' => '84001988DIV_C4120',
                'seaman_card_no' => '201784004843',
            ],
        ],
        'files' => [
            'boat_license/SA 7-5-P.pdf',
        ],
    ],
    [
        'company_id' => 83,
        'license' => 'SA 9/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-07-06',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'KALOY BIN EMLAN',
                'ic_no' => '870313495075',
                'mate_card' => '84001978DIV_C4112',
                'seaman_card_no' => '201484003081',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD NEJAM BIN USNI',
                'ic_no' => '981009126285',
                'mate_card' => '84001989DIV_C',
                'seaman_card_no' => '202384009679',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'WALIMAR BIN ISMAEL',
                'ic_no' => '890125126123',
                'mate_card' => '84001988DIV_C4120',
                'seaman_card_no' => '201784004843',
            ],
        ],
        'files' => [
            'boat_license/SA 9-5-P.pdf',
        ],
    ],
    [
        'company_id' => 83,
        'license' => 'SA 5000/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-11-07',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'KALOY BIN EMLAN',
                'ic_no' => '870313495075',
                'mate_card' => '84001978DIV_C4112',
                'seaman_card_no' => '201484003081',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'NAZREIN BIN OMAR',
                'ic_no' => '930303125769',
                'mate_card' => '84001979DIV_C',
                'seaman_card_no' => '201584003405',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'WALIMAR BIN ISMAEL',
                'ic_no' => '890125126123',
                'mate_card' => '84001988DIV_C4120',
                'seaman_card_no' => '201784004843',
            ],
        ],
        'files' => [
            'boat_license/SA 5000-5-P.pdf',
        ],
    ],
    [
        'company_id' => 83,
        'license' => 'SA 7000/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-11-07',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'KALOY BIN EMLAN',
                'ic_no' => '870313495075',
                'mate_card' => '84001978DIV_C4112',
                'seaman_card_no' => '201484003081',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MUHAMMAD FAMI',
                'ic_no' => '980426126329',
                'mate_card' => '84000287DIV_C',
                'seaman_card_no' => '201784004668',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'NAZREIN BIN OMAR',
                'ic_no' => '930303125769',
                'mate_card' => '84001979DIV_C',
                'seaman_card_no' => '201584003405',
            ],
        ],
        'files' => [
            'boat_license/SA 7000-5-P.pdf',
        ],
    ],
    [
        'company_id' => 83,
        'license' => 'SA 8000/5/P',
        'capacity' => 30, // Total passengers & crew
        'expiry_date' => '2026-02-02',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'KALOY BIN EMLAN',
                'ic_no' => '870313495075',
                'mate_card' => '84001978DIV_C4112',
                'seaman_card_no' => '201484003081',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'NAZREIN BIN OMAR',
                'ic_no' => '930303125769',
                'mate_card' => '84001979DIV_C',
                'seaman_card_no' => '201584003405',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MUHAMMAD FAMI',
                'ic_no' => '980426126329',
                'mate_card' => '84000287DIV_C',
                'seaman_card_no' => '201784004668',
            ],
        ],
        'files' => [
            'boat_license/SA 8000-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 90: YUN SHENG TRAVEL & RENT A CAR SDN BHD
    // ========================================
    [
        'company_id' => 90,
        'license' => 'SA 1666/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-02-10',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'RUMPIN BIN AUWALI',
                'ic_no' => '851112126675',
                'mate_card' => '84001970DIV_C',
                'seaman_card_no' => '201984006091',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'YUSOF',
                'ic_no' => '970326125223',
                'mate_card' => '84001451DIV_C',
                'seaman_card_no' => '201784004987',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SUHAIMEE BIN AZMAN',
                'ic_no' => '970721126541',
                'mate_card' => '84001772DIV_C',
                'seaman_card_no' => '202384009180',
            ],
        ],
        'files' => [
            'boat_license/SA 1666-5-P.pdf',
        ],
    ],
    [
        'company_id' => 90,
        'license' => 'SA 1888/5/P',
        'capacity' => 23, // Total passengers & crew
        'expiry_date' => '2025-07-21',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'YUSOF',
                'ic_no' => '970326125223',
                'mate_card' => '84001451DIV_C',
                'seaman_card_no' => '201784004987',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'RAZMAN BIN MOKVAR',
                'ic_no' => '010425120075',
                'mate_card' => '', // No mate card
                'seaman_card_no' => '202284008611',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD FAZLAN BIN JAAFAR',
                'ic_no' => '910407126397',
                'mate_card' => '84002081DIV_C',
                'seaman_card_no' => '201884005342',
            ],
        ],
        'files' => [
            'boat_license/SA 1888-5-P.pdf',
        ],
    ],
    [
        'company_id' => 90,
        'license' => 'SA 1999/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-05-21',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MOHAMMAD HARIS FIRDAUS',
                'ic_no' => '010324121811',
                'mate_card' => '84001773DIV_C',
                'seaman_card_no' => '202384009181',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SUHAIMEE BIN AZMAN',
                'ic_no' => '970721126541',
                'mate_card' => '84001772DIV_C',
                'seaman_card_no' => '202384009180',
            ],
        ],
        'files' => [
            'boat_license/SA 1999-5-P.pdf',
        ],
    ],
    [
        'company_id' => 90,
        'license' => 'SA 3777/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-01-06',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ABDUL GAFAR BIN AB ALMI',
                'ic_no' => '900703125939',
                'mate_card' => '84001535DIV_C',
                'seaman_card_no' => '201984006472',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'YUSOF',
                'ic_no' => '970326125223',
                'mate_card' => '84001451DIV_C',
                'seaman_card_no' => '201784004987',
            ],
        ],
        'files' => [
            'boat_license/SA 3777-5-P.pdf',
        ],
    ],
    [
        'company_id' => 90,
        'license' => 'SA 5666/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-09-21',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'JUHAL BIN MOHAMMAD',
                'ic_no' => '831122126005',
                'mate_card' => '84001694DIV_C',
                'seaman_card_no' => '201784005073',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'RUMPIN BIN AUWALI',
                'ic_no' => '851112126675',
                'mate_card' => '84001970DIV_C',
                'seaman_card_no' => '201984006091',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'YUSOF',
                'ic_no' => '970326125223',
                'mate_card' => '1451DIV C',
                'seaman_card_no' => '201784004987',
            ],
        ],
        'files' => [
            'boat_license/SA 5666-5-P.pdf',
        ],
    ],
    [
        'company_id' => 90,
        'license' => 'SA 6777/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-10-07',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'SUHAIMEE BIN AZMAN',
                'ic_no' => '970721126541',
                'mate_card' => '84001772DIV_C',
                'seaman_card_no' => '202384009180',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'JUHAL BIN MOHAMMAD',
                'ic_no' => '831122126005',
                'mate_card' => '84001694DIV_C',
                'seaman_card_no' => '201784005073',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'RUMPIN BIN AUWALI',
                'ic_no' => '851112126675',
                'mate_card' => '84001970DIV_C',
                'seaman_card_no' => '201984006091',
            ],
        ],
        'files' => [
            'boat_license/SA 6777-5-P.pdf',
        ],
    ],
    [
        'company_id' => 90,
        'license' => 'SA 9222/5/P',
        'capacity' => 31, // Total passengers & crew
        'expiry_date' => '2025-11-01',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'SUHAIMEE BIN AZMAN',
                'ic_no' => '970721126541',
                'mate_card' => '84001772DIV_C',
                'seaman_card_no' => '202384009180',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHAMMAD ZIMHAR BIN ABDUL RAZAK',
                'ic_no' => '971003125661',
                'mate_card' => '84000345DIV_C',
                'seaman_card_no' => '202584011017',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHAMMAD HARIS FIRDAUS',
                'ic_no' => '010324121861',
                'mate_card' => '84001773DIV_C',
                'seaman_card_no' => '202384009181',
            ],
        ],
        'files' => [
            'boat_license/SA 9222-5-P.pdf',
        ],
    ],
    [
        'company_id' => 90,
        'license' => 'SA 9998/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-03-11',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ABDUL GAFAR BIN AB ALMI',
                'ic_no' => '900703125939',
                'mate_card' => '84001535DIV_C',
                'seaman_card_no' => '201984006472',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHAMMAD FAIZAL BIN MOKSIN',
                'ic_no' => '900203126599',
                'mate_card' => '84001457DIV_C',
                'seaman_card_no' => '201884005690',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SUHAIMEE BIN AZMAN',
                'ic_no' => '970721126541',
                'mate_card' => '84001772DIV_C',
                'seaman_card_no' => '202384009180',
            ],
        ],
        'files' => [
            'boat_license/SA 9998-5-P.pdf',
        ],
    ],
    [
        'company_id' => 90,
        'license' => 'SA 10999/5/P',
        'capacity' => 16, // 4 crew + 12 passengers
        'expiry_date' => '2026-10-01',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MOHAMMAD HARIS FIRDAUS',
                'ic_no' => '010324121861',
                'mate_card' => '84001773DIV_C',
                'seaman_card_no' => '202384009181',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SUHAIMEE BIN AZMAN',
                'ic_no' => '970721126541',
                'mate_card' => '84001772DIV_C',
                'seaman_card_no' => '202384009180',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'JUHAL BIN MOHAMMAD',
                'ic_no' => '831122126005',
                'mate_card' => '84001694DIV_C',
                'seaman_card_no' => '201784005073',
            ],
        ],
        'files' => [
            'boat_license/SA 10999-5-P.pdf',
        ],
    ],
    [
        'company_id' => 90,
        'license' => 'SA 11000/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-09-24',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MOHAMMAD HANS FIRDAUS BIN MUSTAPA',
                'ic_no' => '010324121861',
                'mate_card' => '84001773DIV_C',
                'seaman_card_no' => '202384009181',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SUHAIMEE BIN AZMAN',
                'ic_no' => '970721126541',
                'mate_card' => '84001772DIV_C',
                'seaman_card_no' => '202384009180',
            ],
        ],
        'files' => [
            'boat_license/SA 11000-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 98: DIVE MONSTER TRAVEL & TOURS SDN BHD
    // ========================================
    [
        'company_id' => 98,
        'license' => 'SA 5987/5/P',
        'capacity' => 16, // 4 crew + 12 passengers
        'expiry_date' => '2025-12-20',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'SALASALI BIN PARAD',
                'ic_no' => '790406126033',
                'mate_card' => '84001090DIV_C',
                'seaman_card_no' => '201484003161',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ABDUL KARIM BIN KIRINO',
                'ic_no' => '930928127289',
                'mate_card' => '84002056DIV_C4200',
                'seaman_card_no' => '201184001253',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ARMAN BIN LAITDIN',
                'ic_no' => '890618126463',
                'mate_card' => '84002230DIV_C4419',
                'seaman_card_no' => '201884005674',
            ],
        ],
        'files' => [
            'boat_license/SA 5987-5-P.pdf',
        ],
    ],
    [
        'company_id' => 98,
        'license' => 'SA 8498/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-04-22',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ARMAN BIN LAITDIN',
                'ic_no' => '890618126463',
                'mate_card' => '84002230DIV_C4419',
                'seaman_card_no' => '201884005674',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ABDUL KARIM BIN KIRINO',
                'ic_no' => '930928127289',
                'mate_card' => '84002056DIV_C4200',
                'seaman_card_no' => '201184001253',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SALASALI BIN PARAD',
                'ic_no' => '790406126033',
                'mate_card' => '84002206DIV_C4368',
                'seaman_card_no' => '201484003161',
            ],
        ],
        'files' => [
            'boat_license/SA 8498-5-P.pdf',
        ],
    ],
    [
        'company_id' => 98,
        'license' => 'SA 8747/5/P',
        'capacity' => 27, // Total passengers & crew
        'expiry_date' => '2026-01-05',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ABDUL KARIM BIN KIRINO',
                'ic_no' => '930928127289',
                'mate_card' => '84001156DIV_C1921',
                'seaman_card_no' => '201184001253',
            ],
        ],
        'files' => [
            'boat_license/SA 8747-5-P.jpeg',
        ],
    ],
    [
        'company_id' => 98,
        'license' => 'SA 8783/5/P',
        'capacity' => 27, // Total passengers & crew
        'expiry_date' => '2026-01-10',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'JAMIL BIN KIRINO',
                'ic_no' => '840507126449',
                'mate_card' => '84001737DIV C',
                'seaman_card_no' => '201984006346',
            ],
        ],
        'files' => [
            'boat_license/SA 8783-5-P.jpeg',
        ],
    ],
    [
        'company_id' => 98,
        'license' => 'SA 10777/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-11-26',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ABDUL KARIM BIN KIRINO',
                'ic_no' => '930928127289',
                'mate_card' => '84002056DIV_C4200',
                'seaman_card_no' => '201184001253',
            ],
        ],
        'files' => [
            'boat_license/SA 10777-5-P.jpeg',
        ],
    ],

    // ========================================
    // Company 101: SEMPORNA PARADISE TRAVEL & TOUR SB
    // ========================================
    [
        'company_id' => 101,
        'license' => 'SA 7287/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-03-20',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'AMRIN BIN ABDULLAH',
                'ic_no' => '850831126517',
                'mate_card' => '84001760DIV_C',
                'seaman_card_no' => '201884005863',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'NAILUL AZMI BIN POJI',
                'ic_no' => '861021495167',
                'mate_card' => '84001839DIV_C',
                'seaman_card_no' => '201281006628',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHON BIN PISTUL HATI',
                'ic_no' => '900101127021',
                'mate_card' => '84002184DIV_CR',
                'seaman_card_no' => '202484010306',
            ],
        ],
        'files' => [
            'boat_license/SA 7287-5-P.pdf',
        ],
    ],
    [
        'company_id' => 101,
        'license' => 'SA 8448/5/P',
        'capacity' => 33, // Total passengers & crew
        'expiry_date' => '2026-08-27',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MUSALIM BIN LILLAH',
                'ic_no' => '890613126579',
                'mate_card' => '84001615DIV_C',
                'seaman_card_no' => '201884005823',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'AMRIN BIN ABDULLAH',
                'ic_no' => '850831126517',
                'mate_card' => '84001760DIV_C',
                'seaman_card_no' => '201884005863',
            ],
        ],
        'files' => [
            'boat_license/SA 8448-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 104: SEAVENTURE DIVE RIG SDN BHD
    // ========================================
    [
        'company_id' => 104,
        'license' => 'SA 3788/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-03-02',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'SHARIF BIN ABBAI',
                'ic_no' => '911031126771',
                'mate_card' => '84001920DIV C3989',
                'seaman_card_no' => '201984006239',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'KINIDI BIN LIHONDO',
                'ic_no' => '740515125595',
                'mate_card' => '84001579DIV C3202',
                'seaman_card_no' => '201084000914',
            ],
        ],
        'files' => [
            'boat_license/SA 3788-5-P (PAGE 1).jpeg',
            'boat_license/SA 3788-5-P (PAGE 2).jpeg',
        ],
    ],
    [
        'company_id' => 104,
        'license' => 'SA 7528/5/P',
        'capacity' => 30, // Total passengers & crew
        'expiry_date' => '2026-03-02',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'SHARIF BIN ABBAI',
                'ic_no' => '911031126771',
                'mate_card' => '84001920DIV C3989',
                'seaman_card_no' => '201984006239',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'KINIDI BIN LIHONDO',
                'ic_no' => '740515125595',
                'mate_card' => '84001579DIV C3202',
                'seaman_card_no' => '201084000914',
            ],
        ],
        'files' => [
            'boat_license/SA 7528-5-P (PAGE 1).jpeg',
            'boat_license/SA 7528-5-P (PAGE 2).jpeg',
        ],
    ],
];
