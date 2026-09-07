<?php

/**
 * Boat License Data
 *
 * This file contains hardcoded boat license data for seeding.
 * Separated from BoatLicenseSeeder for better organization and maintainability.
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
    // Company 23: SCUBA HOME DIVE CENTRE SDN BHD
    // ========================================
    [
        'company_id' => 23,
        'license' => 'SA 7576/5/P',
        'capacity' => 15, // 3 crew + 12 passengers
        'expiry_date' => '2026-04-08',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'YEW KAI SIONG',
                'ic_no' => '761206135661',
                'mate_card' => '84001765DIV_C',
                'seaman_card_no' => '201474620274',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'IDRIS BIN ALBANI',
                'ic_no' => '920517126741',
                'mate_card' => '84001374DIV_C',
                'seaman_card_no' => '201984006614',
            ],
        ],
        'files' => [
            'boat_license/SA 7576-5-P.jpeg',
            'boat_license/SA 7576-5-P (PAGE 2).jpeg',
        ],
    ],
    [
        'company_id' => 23,
        'license' => 'SA 9223/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-11-07',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'YEW KAI SIONG',
                'ic_no' => '761206135661',
                'mate_card' => '84001765DIV_C',
                'seaman_card_no' => '201474620274',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'IDRIS BIN ALBANI',
                'ic_no' => '920517126741',
                'mate_card' => '84001374DIV_C',
                'seaman_card_no' => '201984006614',
            ],
        ],
        'files' => [
            'boat_license/SA 9223-5-P.jpeg',
            'boat_license/SA 9223-5-P (PAGE 2).jpeg',
        ],
    ],
    [
        'company_id' => 23,
        'license' => 'SA 9947/5/P',
        'capacity' => 27, // Total passengers & crew
        'expiry_date' => '2026-10-06',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'YEW KAI SIONG',
                'ic_no' => '761206135661',
                'mate_card' => '84001765DIV_C',
                'seaman_card_no' => '201474620274',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'IDRIS BIN ALBANI',
                'ic_no' => '920517126741',
                'mate_card' => '84001374DIV_C',
                'seaman_card_no' => '201984006614',
            ],
        ],
        'files' => [
            'boat_license/SA 9947-5-P.jpeg',
            'boat_license/SA 9947-5-P (PAGE 2).jpeg',
        ],
    ],

    // ========================================
    // Company 109: TAG & TRAVEL SDN. BHD
    // ========================================
    [
        'company_id' => 109,
        'license' => 'SA 8433/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-08-28',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ROSLAN BIN KAMLUN',
                'ic_no' => '830210126617',
                'mate_card' => '84002196DIV_C4349',
                'seaman_card_no' => '201984006441',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'KAMARUDIN BIN KAMLUN',
                'ic_no' => '910826126711',
                'mate_card' => '84002207DIV_C4369',
                'seaman_card_no' => '201884005650',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SHAMSUDDIN BIN KAMLUN',
                'ic_no' => '810605127157',
                'mate_card' => '84002205DIV_C4367',
                'seaman_card_no' => '201584003612',
            ],
        ],
        'files' => [
            'boat_license/SA 8433-5-P (PAGE 1).jpg',
            'boat_license/SA 8433-5-P (PAGE 2).jpg',
        ],
    ],
    [
        'company_id' => 109,
        'license' => 'SA 8686/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-08-19',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'SHAMSUDDIN BIN KAMLUN',
                'ic_no' => '810605127157',
                'mate_card' => '84002205DIV C4367',
                'seaman_card_no' => '201584003612',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'JULKIRAM BIN MOHD SAID',
                'ic_no' => '871120495439',
                'mate_card' => '84001460DIV_C',
                'seaman_card_no' => '201984006195',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'KAMARUDIN BIN KAMLUN',
                'ic_no' => '910826126711',
                'mate_card' => '84002207DIV_C4369',
                'seaman_card_no' => '201884005650',
            ],
        ],
        'files' => [
            'boat_license/SA 8686-5-P (PAGE 1).jpg',
            'boat_license/SA 8686-5-P (PAGE 2).jpg',
        ],
    ],
    [
        'company_id' => 109,
        'license' => 'SA 9666/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-08-07',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'JULKIRAM BIN MOHD SAID',
                'ic_no' => '871120496439',
                'mate_card' => '84001460DIV_C',
                'seaman_card_no' => '201984006195',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'KAMARUDIN BIN KAMLUN',
                'ic_no' => '910826126711',
                'mate_card' => '84002207DIV_C4369',
                'seaman_card_no' => '201884005450',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ROSLAN BIN KAMLUN',
                'ic_no' => '830210126617',
                'mate_card' => '84002196DIV_C4349',
                'seaman_card_no' => '201984006441',
            ],
        ],
        'files' => [
            'boat_license/SA 9666-5-P (PAGE 1).jpg',
            'boat_license/SA 9666-5-P (PAGE 2).jpg',
        ],
    ],
    [
        'company_id' => 109,
        'license' => 'SA 9696/5/P',
        'capacity' => 25, // 3 crew + 22 passengers
        'expiry_date' => '2026-02-06',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'SHAMSUDDIN BIN KAMLUN',
                'ic_no' => '810605127157',
                'mate_card' => '84001085DIV_C',
                'seaman_card_no' => '201584003612',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'JULKIRAM BIN MOHD SAID',
                'ic_no' => '871120496439',
                'mate_card' => '84001460DIV_C',
                'seaman_card_no' => '201984006195',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ROSLAN BIN KAMLUN',
                'ic_no' => '830210126617',
                'mate_card' => '84001083DIV_C',
                'seaman_card_no' => '201984006441',
            ],
        ],
        'files' => [
            'boat_license/SA 9696-5-P (PAGE 1).jpg',
            'boat_license/SA 9696-5-P (PAGE 2).jpg',
        ],
    ],
    [
        'company_id' => 109,
        'license' => 'SA 9898/5/P',
        'capacity' => 16, // 4 crew + 12 passengers
        'expiry_date' => '2026-02-02',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'KAMARUDIN BIN KAMLUN',
                'ic_no' => '910826126711',
                'mate_card' => '84001082DIV_C',
                'seaman_card_no' => '201884005650',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ROSLAN BIN KAMLUN',
                'ic_no' => '830210126617',
                'mate_card' => '84001083DIV_C',
                'seaman_card_no' => '201984006441',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'JULKIRAM BIN MOHD SAID',
                'ic_no' => '871120496439',
                'mate_card' => '84001460DIV_C',
                'seaman_card_no' => '201984006195',
            ],
        ],
        'files' => [
            'boat_license/SA 9898-5-P (PAGE 1).jpg',
            'boat_license/SA 9898-5-P (PAGE 2).jpg',
        ],
    ],

    // ========================================
    // Company 110: GTS TRAVEL SERVICES SDN BHD
    // ========================================
    [
        'company_id' => 110,
        'license' => 'SA 2460/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2025-12-04',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'NASSAR BIN KHAMIS',
                'ic_no' => '741015126115',
                'mate_card' => '84001503DIV_C',
                'seaman_card_no' => '201284002057',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SANJAY BIN BOHENATA',
                'ic_no' => '760709125129',
                'mate_card' => '84001680DIV_C',
                'seaman_card_no' => '201284001949',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD ISA BIN ABDUL AZIZ',
                'ic_no' => '851007125689',
                'mate_card' => '84001502DIV_C',
                'seaman_card_no' => '201884005274',
            ],
        ],
        'files' => [
            'boat_license/SA 2460-5-P.pdf',
        ],
    ],
    [
        'company_id' => 110,
        'license' => 'SA 3031/5/P',
        'capacity' => 38, // Total passengers & crew
        'expiry_date' => '2025-11-26',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ABDUL LAJIK BIN ISTARI',
                'ic_no' => '781212126153',
                'mate_card' => '84001013DIV_C1588',
                'seaman_card_no' => '201384002548',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'AMAT DELI BIN AWANG DAMIT',
                'ic_no' => '720322125531',
                'mate_card' => '84001289DIV_C2599',
                'seaman_card_no' => '201084000752',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ROSA BIN BOHЕНАТА',
                'ic_no' => '720806125837',
                'mate_card' => '84001504DIV_C',
                'seaman_card_no' => '201684004170',
            ],
        ],
        'files' => [
            'boat_license/SA 3031-5-P.pdf',
        ],
    ],
    [
        'company_id' => 110,
        'license' => 'SA 3363/5/P',
        'capacity' => 7, // Crew only
        'expiry_date' => '2026-12-11',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'AMAT DELI BIN AWANG DAMIT',
                'ic_no' => '720322125531',
                'mate_card' => '84001289DIV_C2599',
                'seaman_card_no' => '201084000752',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'NAMDO BIN RAIS',
                'ic_no' => '021012121073',
                'mate_card' => '84000423DIV_C',
                'seaman_card_no' => '202484010417',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MUHAMMAD ASRAF BIN AMAT DELI',
                'ic_no' => '980529125767',
                'mate_card' => '84001069DIV_C',
                'seaman_card_no' => '202084006968',
            ],
        ],
        'files' => [
            'boat_license/SA 3363-5-P.pdf',
        ],
    ],
    [
        'company_id' => 110,
        'license' => 'SA 4359/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-12-19',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'NASSAR BIN KHAMIS',
                'ic_no' => '741015126115',
                'mate_card' => '84001503DIV_C',
                'seaman_card_no' => '201284002057',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SANJAY BIN BOHЕНАТА',
                'ic_no' => '760709125129',
                'mate_card' => '84001680DIV_C',
                'seaman_card_no' => '201284001949',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD ISA BIN ABDUL AZIZ',
                'ic_no' => '851007125689',
                'mate_card' => '84001502DIV_C',
                'seaman_card_no' => '201884005274',
            ],
        ],
        'files' => [
            'boat_license/SA 4359-5-P.pdf',
        ],
    ],
    [
        'company_id' => 110,
        'license' => 'SA 5333/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-05-15',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ABDUL LAJIK BIN ISTARI',
                'ic_no' => '781212126153',
                'mate_card' => '84001013DIV_C1588',
                'seaman_card_no' => '201384002548',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'NASSAR BIN KHAMIS',
                'ic_no' => '741015126115',
                'mate_card' => '84001503DIV_C',
                'seaman_card_no' => '291284002157',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'AMAT DELI BIN AWANG DAMIT',
                'ic_no' => '720322125531',
                'mate_card' => '84001289DIV_C2599',
                'seaman_card_no' => '201084000752',
            ],
        ],
        'files' => [
            'boat_license/SA 5333-5-P.pdf',
        ],
    ],
    [
        'company_id' => 110,
        'license' => 'SA 5990/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-11-23',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ABDUL LAJIK BIN ISTARI',
                'ic_no' => '781212126153',
                'mate_card' => '84001013DIV_C1588',
                'seaman_card_no' => '201384002548',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'AL SHAHRIR BIN AMAT DELI',
                'ic_no' => '911005125919',
                'mate_card' => '84001070DIV_C',
                'seaman_card_no' => '201784005216',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'AMAT DELI BIN AWANG DAMIT',
                'ic_no' => '720322125531',
                'mate_card' => '84001289DIV_C2599',
                'seaman_card_no' => '201084000752',
            ],
        ],
        'files' => [
            'boat_license/SA 5990-5-P.pdf',
        ],
    ],
    [
        'company_id' => 110,
        'license' => 'SA 6868/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-10-20',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ROSA BIN BOHЕНАТА',
                'ic_no' => '720806125837',
                'mate_card' => '84001504DIV_C',
                'seaman_card_no' => '201684004170',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'AMAT DELI BIN AWANG DAMIT',
                'ic_no' => '720322125531',
                'mate_card' => '84001289DIV_C2599',
                'seaman_card_no' => '201084000752',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ABDUL LAJIK BIN ISTARI',
                'ic_no' => '781212126153',
                'mate_card' => '84002103DIV_C4253',
                'seaman_card_no' => '201384002548',
            ],
        ],
        'files' => [
            'boat_license/SA 6868-5-P.pdf',
        ],
    ],
    [
        'company_id' => 110,
        'license' => 'SA 10789/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-10-16',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'NASSAR BIN KHAMIS',
                'ic_no' => '741015126115',
                'mate_card' => '84001503DIV_C',
                'seaman_card_no' => '201284002057',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD ISA BIN ABDUL AZIZ',
                'ic_no' => '851007125689',
                'mate_card' => '84001502DIV_C',
                'seaman_card_no' => '201884005274',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SANJAY BIN BOHEHATA',
                'ic_no' => '760709125129',
                'mate_card' => '84001680DIV_C',
                'seaman_card_no' => '201284001949',
            ],
        ],
        'files' => [
            'boat_license/SA 10789-5-P.pdf',
        ],
    ],
    [
        'company_id' => 110,
        'license' => 'SA 11136/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-02-17',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'NASSAR BIN KHAMIS',
                'ic_no' => '741015126115',
                'mate_card' => '84001503DIV_C',
                'seaman_card_no' => '201284002057',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SANJAY BIN BOHEHATA',
                'ic_no' => '760709125129',
                'mate_card' => '84001680DIV_C',
                'seaman_card_no' => '201284001949',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD ISA BIN ABDUL AZIZ',
                'ic_no' => '851007125689',
                'mate_card' => '84001502DIV_C',
                'seaman_card_no' => '201884005274',
            ],
        ],
        'files' => [
            'boat_license/SA 11136-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 24: SEMPORNA BLUE REEF DIVE & TOURS SDN BHD
    // ========================================
    [
        'company_id' => 24,
        'license' => 'SA 887/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2025-06-16',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ABDUL BIN BUDASAL',
                'ic_no' => '720404125375',
                'mate_card' => '40014500IV G',
                'seaman_card_no' => '201004000801',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'JIMSAL',
                'ic_no' => '810323126447',
                'mate_card' => '82000075DIV_C',
                'seaman_card_no' => '202384000391',
            ],
        ],
        'files' => [
            'boat_license/SA 887-5-P (PAGE 1).jpeg',
            'boat_license/SA 887-5-P (PAGE 2).jpeg',
        ],
    ],
    [
        'company_id' => 24,
        'license' => 'SA 8778/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-06-12',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ABDUL BIN BUDASAL',
                'ic_no' => '720404125375',
                'mate_card' => '84001450DIV_C',
                'seaman_card_no' => '201084000891',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'JIMSAL',
                'ic_no' => '810323126447',
                'mate_card' => '82000075DIV_C',
                'seaman_card_no' => '202384009391',
            ],
        ],
        'files' => [
            'boat_license/SA 8778-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 30: THE BUWAN DIVE RESORTS AND TOURS SDN BHD
    // ========================================
    [
        'company_id' => 30,
        'license' => 'SA 9815/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-07-10',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MOHD NOOR BIN ABDUL SALLEH',
                'ic_no' => '860822495111',
                'mate_card' => '84001567DIV_C',
                'seaman_card_no' => '202284008699',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'REDWAN BIN AMILHARI',
                'ic_no' => '041201121505',
                'mate_card' => '84002188DIV_CR',
                'seaman_card_no' => '202384009630',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'RAMDI BIN JEFRI',
                'ic_no' => '980223125035',
                'mate_card' => '84002215DIV_C4386',
                'seaman_card_no' => '201884005848',
            ],
        ],
        'files' => [
            'boat_license/SA 9815-5-P.pdf',
        ],
    ],
    [
        'company_id' => 30,
        'license' => 'SA 10437/5/P',
        'capacity' => 15, // 3 crew + 12 passengers
        'expiry_date' => '2025-04-18',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MOHD NOOR BIN ABDUL SALLEH',
                'ic_no' => '860822495111',
                'mate_card' => '84001567DIV_C',
                'seaman_card_no' => '202284008699',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'REDWAN BIN AMILHARI',
                'ic_no' => '041201121505',
                'mate_card' => '84001836DIV_C',
                'seaman_card_no' => '202384009630',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'RAMDI BIN JEFRI',
                'ic_no' => '980223125035',
                'mate_card' => '84001098DIV_C',
                'seaman_card_no' => '201884005848',
            ],
        ],
        'files' => [
            'boat_license/SA 10437-5-P.pdf',
        ],
    ],
    [
        'company_id' => 30,
        'license' => 'SA 10526/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2025-05-26',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MOHD NOOR BIN ABDUL SALLEH',
                'ic_no' => '860822495111',
                'mate_card' => '84001567DIV_C',
                'seaman_card_no' => '202284008699',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'REDWAN BIN AMILHARI',
                'ic_no' => '041201121505',
                'mate_card' => '84001836DIV_C',
                'seaman_card_no' => '202384009630',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ABD KARIM BIN AJARMA',
                'ic_no' => '971003126381',
                'mate_card' => '84001977DIV_C',
                'seaman_card_no' => '202384009322',
            ],
        ],
        'files' => [
            'boat_license/SA 10526-5-P.pdf',
        ],
    ],
    [
        'company_id' => 30,
        'license' => 'SA 11589/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-08-06',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'RAMDI BIN JEFRI',
                'ic_no' => '980223125035',
                'mate_card' => '84002215DIV_C4386',
                'seaman_card_no' => '201884005848',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'REDWAN BIN AMILHARI',
                'ic_no' => '041201121505',
                'mate_card' => '84002188DIV_CR',
                'seaman_card_no' => '202384009630',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ABD KARIM BIN AJARMA',
                'ic_no' => '971003126381',
                'mate_card' => '84001977DIV_C',
                'seaman_card_no' => '202384009322',
            ],
        ],
        'files' => [
            'boat_license/SA 11589-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 31: MAGLAMI TOUR AND TRAVEL SDN BHD
    // ========================================
    [
        'company_id' => 31,
        'license' => 'SA 4371/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2025-10-16',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'SUPIAN BIN ABDUL SALAM',
                'ic_no' => '880609125376',
                'mate_card' => '84001345DIV_G',
                'seaman_card_no' => '201984000194',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'AZMAN BIN ALLING',
                'ic_no' => '931216127373',
                'mate_card' => '54001445DIV',
                'seaman_card_no' => '201754004933',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MAD ASLAN BIN MUHAMMAD NAN',
                'ic_no' => '100928126887',
                'mate_card' => '', // Hard to read in document
                'seaman_card_no' => '201584005582',
            ],
        ],
        'files' => [
            'boat_license/SA 4371-5-P (PAGE 1).jpeg',
            'boat_license/SA 4371-5-P (PAGE 2).jpeg',
        ],
    ],
    [
        'company_id' => 31,
        'license' => 'SA 10026/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2025-10-15',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'SUPIAN BIN ABDUL SALAM',
                'ic_no' => '880609125275',
                'mate_card' => '84001349DIV_C',
                'seaman_card_no' => '201984006194',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SUHAIMI BIN ARMSTRONG',
                'ic_no' => '980731126095',
                'mate_card' => '84000413DIV_C',
                'seaman_card_no' => '200084007107',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD SHAHRUL NIZAM BIN HARDY',
                'ic_no' => '990407127309',
                'mate_card' => '84000414DIV_C',
                'seaman_card_no' => '201584011103',
            ],
        ],
        'files' => [
            'boat_license/SA 10026-5-P (PAGE 1).jpeg',
            'boat_license/SA 10026-5-P (PAGE 2).jpeg',
        ],
    ],
    [
        'company_id' => 31,
        'license' => 'SA 11554/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2025-08-06',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'SUPIAN BIN ABDUL SALAM',
                'ic_no' => '880609125275',
                'mate_card' => '84001348DIV_C',
                'seaman_card_no' => '201984006194',
            ],
        ],
        'files' => [
            'boat_license/SA 11554-5-P (PAGE 1).jpeg',
            'boat_license/SA 11554-5-P (PAGE 2).jpeg',
        ],
    ],

    // ========================================
    // Company 32: MUSSAH POTEH RESORT SDN. BHD
    // ========================================
    [
        'company_id' => 32,
        'license' => 'SA 2/5/P',
        'capacity' => 30,
        'expiry_date' => '2025-09-16',
        'boatmen' => [
            [
                'type' => 1,
                'name' => 'ALI HARUN BIN ALGISUN',
                'ic_no' => '010101121725',
                'mate_card' => '84001545DIV_C',
                'seaman_card_no' => '202284008217',
            ],
            [
                'type' => 2,
                'name' => 'MADSAUDIN BIN BUDJAHARI',
                'ic_no' => '920312126529',
                'mate_card' => '84001708DIV_C',
                'seaman_card_no' => '201884005497',
            ],
            [
                'type' => 2,
                'name' => 'RUDI BIN ALIRUIH',
                'ic_no' => '811024126337',
                'mate_card' => '84002003DIV_C4127',
                'seaman_card_no' => '201784005197',
            ],
        ],
        'files' => ['boat_license/SA 2-5-P.pdf'],
    ],
    [
        'company_id' => 32,
        'license' => 'SA 22/5/P',
        'capacity' => 27,
        'expiry_date' => '2025-11-26',
        'boatmen' => [
            [
                'type' => 1,
                'name' => 'NORI BIN LIUN',
                'ic_no' => '890127126151',
                'mate_card' => '84001681DIV_C',
                'seaman_card_no' => '201784005089',
            ],
            [
                'type' => 2,
                'name' => 'RUDI BIN ALIRUIH',
                'ic_no' => '811024126337',
                'mate_card' => '84002003DIV_C4127',
                'seaman_card_no' => '201784005197',
            ],
            [
                'type' => 2,
                'name' => 'MADSAUDIN BIN BUDJAHARI',
                'ic_no' => '920312126529',
                'mate_card' => '84001708DIV_C',
                'seaman_card_no' => '201884005497',
            ],
        ],
        'files' => ['boat_license/SA 22-5-P.pdf'],
    ],
    [
        'company_id' => 32,
        'license' => 'SA 89/5/P',
        'capacity' => 14,
        'expiry_date' => '2025-10-22',
        'boatmen' => [
            [
                'type' => 1,
                'name' => 'ROSLI BIN ROSA',
                'ic_no' => '960310125441',
                'mate_card' => '84001582DIV_C',
                'seaman_card_no' => '201684004067',
            ],
            [
                'type' => 2,
                'name' => 'ALI HARUN BIN ALGISUN',
                'ic_no' => '010101121725',
                'mate_card' => '84001545DIV_C',
                'seaman_card_no' => '202284008217',
            ],
            [
                'type' => 2,
                'name' => 'RUDI BIN ALIRUIH',
                'ic_no' => '811024126337',
                'mate_card' => '84002003DI',
                'seaman_card_no' => '201784005197',
            ],
        ],
        'files' => ['boat_license/SA 89-5-P.pdf'],
    ],
    [
        'company_id' => 32,
        'license' => 'SA 98/5/P',
        'capacity' => 14,
        'expiry_date' => '2026-02-24',
        'boatmen' => [
            [
                'type' => 1,
                'name' => 'MAHA TURI BIN JUHARI',
                'ic_no' => '771215125771',
                'mate_card' => '84002180DIV_C',
                'seaman_card_no' => '202484009880',
            ],
            [
                'type' => 2,
                'name' => 'BAIPING BIN BAGITI',
                'ic_no' => '840926126523',
                'mate_card' => '84002125DIV_C',
                'seaman_card_no' => '202484009863',
            ],
            [
                'type' => 2,
                'name' => 'ROSLI BIN ROSA',
                'ic_no' => '960310126441',
                'mate_card' => '84001582DIV_C',
                'seaman_card_no' => '201684004067',
            ],
        ],
        'files' => ['boat_license/SA 98-5-P.pdf'],
    ],
    [
        'company_id' => 32,
        'license' => 'SA 298/5/P',
        'capacity' => 14,
        'expiry_date' => '2026-01-05',
        'boatmen' => [
            [
                'type' => 1,
                'name' => 'RUDI BIN ALIRUIH',
                'ic_no' => '811024126337',
                'mate_card' => '84002003DIV_C4127',
                'seaman_card_no' => '201784005197',
            ],
            [
                'type' => 2,
                'name' => 'ROSLI BIN ROSA',
                'ic_no' => '960310126441',
                'mate_card' => '84001582DIV_C',
                'seaman_card_no' => '201684004067',
            ],
            [
                'type' => 2,
                'name' => 'ALI HARUN BIN ALGISUN',
                'ic_no' => '010101121725',
                'mate_card' => '84001545DIV_C',
                'seaman_card_no' => '202284008217',
            ],
        ],
        'files' => ['boat_license/SA 298-5-P.pdf'],
    ],
    [
        'company_id' => 32,
        'license' => 'SA 527/5/P',
        'capacity' => 14,
        'expiry_date' => '2025-12-01',
        'boatmen' => [
            [
                'type' => 1,
                'name' => 'RUDI BIN ALIRUIH',
                'ic_no' => '811024126337',
                'mate_card' => '84002003DIV_C4127',
                'seaman_card_no' => '201784005197',
            ],
            [
                'type' => 2,
                'name' => 'ALI HARUN BIN ALGISUN',
                'ic_no' => '010101121725',
                'mate_card' => '84001545DIV_C',
                'seaman_card_no' => '202284008217',
            ],
            [
                'type' => 2,
                'name' => 'MADSAUDIN BIN BUDJAHARI',
                'ic_no' => '920312126529',
                'mate_card' => '84001708DIV_C',
                'seaman_card_no' => '201884005497',
            ],
        ],
        'files' => ['boat_license/SA 527-5-P.pdf'],
    ],
    [
        'company_id' => 32,
        'license' => 'SA 7378/5/P',
        'capacity' => 42,
        'expiry_date' => '2025-09-16',
        'boatmen' => [
            [
                'type' => 1,
                'name' => 'NAZLI BIN LIUN',
                'ic_no' => '901211126323',
                'mate_card' => '84001829DIV_C',
                'seaman_card_no' => '201784005061',
            ],
            [
                'type' => 2,
                'name' => 'MADSAUDIN BIN BUDJAHARI',
                'ic_no' => '920312126529',
                'mate_card' => '84001708DIV_C',
                'seaman_card_no' => '201884005497',
            ],
            [
                'type' => 2,
                'name' => 'RUDI BIN ALIRUIH',
                'ic_no' => '811024126337',
                'mate_card' => '84002003DIV_C4127',
                'seaman_card_no' => '201784005197',
            ],
        ],
        'files' => ['boat_license/SA 7378-5-P.pdf'],
    ],
    [
        'company_id' => 32,
        'license' => 'SA 10189/5/P',
        'capacity' => 38,
        'expiry_date' => '2025-12-17',
        'boatmen' => [
            [
                'type' => 1,
                'name' => 'ALI HARUN BIN ALGISUN',
                'ic_no' => '010101121725',
                'mate_card' => '84001545DIV_C',
                'seaman_card_no' => '202284008217',
            ],
            [
                'type' => 2,
                'name' => 'RUDI BIN ALIRUIH',
                'ic_no' => '811024126337',
                'mate_card' => '84002003DIV_C4127',
                'seaman_card_no' => '201784005197',
            ],
            [
                'type' => 2,
                'name' => 'MADSAUDIN BIN BUDJAHARI',
                'ic_no' => '920312126529',
                'mate_card' => '84001708DIV_C',
                'seaman_card_no' => '201884005497',
            ],
        ],
        'files' => ['boat_license/SA 10189-5-P.pdf'],
    ],
    [
        'company_id' => 32,
        'license' => 'SA 10239/5/P',
        'capacity' => 14,
        'expiry_date' => '2026-01-15',
        'boatmen' => [
            [
                'type' => 1,
                'name' => 'NAZLI BIN LIUN',
                'ic_no' => '901211126323',
                'mate_card' => '84001829DIV_C',
                'seaman_card_no' => '201784005061',
            ],
            [
                'type' => 2,
                'name' => 'ALI HARUN BIN ALGISUN',
                'ic_no' => '010101121725',
                'mate_card' => '84001545DIV_C',
                'seaman_card_no' => '202284008217',
            ],
            [
                'type' => 2,
                'name' => 'MADSAUDIN BIN BUDJAHARI',
                'ic_no' => '920312126529',
                'mate_card' => '84001708DIV_C',
                'seaman_card_no' => '201884005497',
            ],
        ],
        'files' => ['boat_license/SA 10239-5-P.pdf'],
    ],

    // ========================================
    // TODO: Add more companies data here
    // Format:
    // [
    //     'company_id' => XX,
    //     'license' => 'SA XXXX/5/P',
    //     'capacity' => XX,
    //     'expiry_date' => 'YYYY-MM-DD',
    //     'boatmen' => [...],
    //     'files' => [...],
    // ],
    // ========================================
];
