<?php

/**
 * Boat License Data - Batch 2
 *
 * This file contains hardcoded boat license data for seeding (Batch 2).
 * Separated from BoatLicenseSeeder for better organization and maintainability.
 *
 * Companies in Batch 2:
 * - 38. BORNEO SEA TRAVEL AND TOUR SDN BHD
 * - 48. UC ISLANDER SDN BHD
 * - 49. BLUCORAL TRAVEL SDN BHD
 * - 53. WINDIA TRAVEL & TOURS SDN BHD
 * - 57. BORNEO JUNGLE RIVER ISLAND TOURS SDN BHD
 * - 65. YUE HAI TRAVEL SDN BHD
 * - 66. SOLARIS TAWAU TRAVEL & TOURS SDN BHD
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
    // Company 38: BORNEO SEA TRAVEL AND TOUR SDN BHD
    // ========================================
    [
        'company_id' => 38,
        'license' => 'SA 3600/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2024-12-20',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'NOH BIN SARRI',
                'ic_no' => '780115125891',
                'mate_card' => '84001054DIV_C1625',
                'seaman_card_no' => '201384002542',
            ],
        ],
        'files' => [
            'boat_license/SA 3600-5-P.pdf',
        ],
    ],
    [
        'company_id' => 38,
        'license' => 'SA 5822/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2025-10-29',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'NURHAN BIN IBRAHIM',
                'ic_no' => '871220496137',
                'mate_card' => '84001139DIV_C',
                'seaman_card_no' => '201284002039',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'NOH BIN SARRI',
                'ic_no' => '780115125891',
                'mate_card' => '84001054DIV_C1625',
                'seaman_card_no' => '201384002542',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'HEROSEN BIN HARISAN',
                'ic_no' => '000820120877',
                'mate_card' => '84001328DIV C',
                'seaman_card_no' => '201984006298',
            ],
        ],
        'files' => [
            'boat_license/SA 5822-5-P.pdf',
        ],
    ],
    [
        'company_id' => 38,
        'license' => 'SA 8521/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2025-01-29',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'NURHAN BIN IBRAHIM',
                'ic_no' => '871220496137',
                'mate_card' => '84001139DIV C',
                'seaman_card_no' => '201234002039',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SAHAM BIN ELVIS',
                'ic_no' => '980202126063',
                'mate_card' => '84001872DIV_C',
                'seaman_card_no' => '201784004773',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'NOH BIN SARRI',
                'ic_no' => '780115125891',
                'mate_card' => '84001054DIV_C1625',
                'seaman_card_no' => '201384002542',
            ],
        ],
        'files' => [
            'boat_license/SA 8521-5-P.pdf',
        ],
    ],
    [
        'company_id' => 38,
        'license' => 'SA 8522/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2024-12-29',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'NURHAN BIN IBRAHIM',
                'ic_no' => '871220496137',
                'mate_card' => '84001139DIV_C',
                'seaman_card_no' => '201284002039',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SAHAM BIN ELVIS',
                'ic_no' => '980202126063',
                'mate_card' => '84001872DIV_C',
                'seaman_card_no' => '201784004773',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'NOH BIN SARRI',
                'ic_no' => '780115125891',
                'mate_card' => '84001054DIV_C1625',
                'seaman_card_no' => '201384002542',
            ],
        ],
        'files' => [
            'boat_license/SA 8522-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 48: UC ISLANDER SDN BHD
    // ========================================
    [
        'company_id' => 48,
        'license' => 'SA 9938/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-08-26',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MOHD HAFIZI SOLEHIN BIN JAMAL',
                'ic_no' => '910325126035',
                'mate_card' => '84001786DIV_C',
                'seaman_card_no' => '202384009191',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'WASRI BIN SAWADI',
                'ic_no' => '910528125067',
                'mate_card' => '84002013DIV_C',
                'seaman_card_no' => '201481008564',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD KAMAL BIN NURAMIN',
                'ic_no' => '901201126311',
                'mate_card' => '84001372DIV_C',
                'seaman_card_no' => '201084000652',
            ],
        ],
        'files' => [
            'boat_license/SA 9938-5-P.pdf',
        ],
    ],
    [
        'company_id' => 48,
        'license' => 'SA 11488/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-07-07',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'WASRI BIN SAWADI',
                'ic_no' => '910528125067',
                'mate_card' => '84002013DIV_C',
                'seaman_card_no' => '201481008564',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD KAMAL BIN NURAMIN',
                'ic_no' => '901201126311',
                'mate_card' => '84001372DIV_C',
                'seaman_card_no' => '201084000652',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD HAFIZI SOLEHIN BIN JAMAL',
                'ic_no' => '910325126035',
                'mate_card' => '84001786DIV_C',
                'seaman_card_no' => '202384009191',
            ],
        ],
        'files' => [
            'boat_license/SA 11488-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 49: BLUCORAL TRAVEL SDN BHD
    // ========================================
    [
        'company_id' => 49,
        'license' => 'SA 1163/5/P',
        'capacity' => 16, // 4 crew + 12 passengers
        'expiry_date' => '2026-10-09',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'JASPIN BIN ALDEN',
                'ic_no' => '930525126345',
                'mate_card' => '84001818DIV_C',
                'seaman_card_no' => '201684004470',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'TEDY BIN BISATULAH',
                'ic_no' => '980122126119',
                'mate_card' => '84000350DIV_C',
                'seaman_card_no' => '201984006192',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'TOTOS BIN ALGUNDA',
                'ic_no' => '920413125111',
                'mate_card' => '84001891DIV_C',
                'seaman_card_no' => '201784004800',
            ],
        ],
        'files' => [
            'boat_license/SA 1163-5-P.pdf',
        ],
    ],
    [
        'company_id' => 49,
        'license' => 'SA 1183/5/P',
        'capacity' => 16, // 4 crew + 12 passengers
        'expiry_date' => '2025-12-03',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'JASPIN BIN ALDEN',
                'ic_no' => '930525126345',
                'mate_card' => '84001818DIV_C',
                'seaman_card_no' => '201684004470',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ISHAAQ YONG',
                'ic_no' => '940121126751',
                'mate_card' => '84001337DIV_C',
                'seaman_card_no' => '202084007210',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'TOTOS BIN ALGUNDA',
                'ic_no' => '920413125111',
                'mate_card' => '84001891DIV_C',
                'seaman_card_no' => '201784004800',
            ],
        ],
        'files' => [
            'boat_license/SA 1183-5-P.pdf',
        ],
    ],
    [
        'company_id' => 49,
        'license' => 'SA 9351/5/P',
        'capacity' => 16, // 4 crew + 12 passengers
        'expiry_date' => '2025-12-14',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'JASPIN BIN ALDEN',
                'ic_no' => '930525126345',
                'mate_card' => '84001818DIV_C',
                'seaman_card_no' => '201684004470',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ISHAAQ YONG',
                'ic_no' => '940121126751',
                'mate_card' => '84001337DIV_C',
                'seaman_card_no' => '202084007210',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'TOTOS BIN ALGUNDA',
                'ic_no' => '920413125111',
                'mate_card' => '84001891DIV_C',
                'seaman_card_no' => '201784004800',
            ],
        ],
        'files' => [
            'boat_license/SA 9351-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 53: WINDIA TRAVEL & TOURS SDN BHD
    // ========================================
    [
        'company_id' => 53,
        'license' => 'SA 7177/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-07-23',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'AB MAKIN BIN SISAL',
                'ic_no' => '870311496269',
                'mate_card' => '84001206DIV_C',
                'seaman_card_no' => '201784005167',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ALI YUSOF BIN ALKAP',
                'ic_no' => '740202126159',
                'mate_card' => '84001751DIV_C3627',
                'seaman_card_no' => '201784004718',
            ],
        ],
        'files' => [
            'boat_license/SA 7177-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 57: BORNEO JUNGLE RIVER ISLAND TOURS SDN BHD
    // ========================================
    [
        'company_id' => 57,
        'license' => 'SA 888/5/P',
        'capacity' => 15, // 3 crew + 12 passengers
        'expiry_date' => '2026-02-10',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'AZMI BIN MUSA',
                'ic_no' => '870805496365',
                'mate_card' => '84001291DIV_C2601',
                'seaman_card_no' => '201281006103',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MUSLIM BIN BADIRI',
                'ic_no' => '980717126687',
                'mate_card' => '84001967DIV_C',
                'seaman_card_no' => '202484009931',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SANIN BIN LAJAH BANGSA',
                'ic_no' => '890214126045',
                'mate_card' => '84002173DIV_C4302',
                'seaman_card_no' => '201584003664',
            ],
        ],
        'files' => [
            'boat_license/SA 888-5-P.pdf',
        ],
    ],
    [
        'company_id' => 57,
        'license' => 'SA 1311/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-02-10',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'AZMI BIN MUSA',
                'ic_no' => '870805496365',
                'mate_card' => '84001291DIV_C2601',
                'seaman_card_no' => '201281006103',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MUSLIM BIN BADIRI',
                'ic_no' => '980717126687',
                'mate_card' => '84001967DIV_C',
                'seaman_card_no' => '202484009931',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SANIN BIN LAJAH BANGSA',
                'ic_no' => '890214126045',
                'mate_card' => '84002173DIV_C4302',
                'seaman_card_no' => '201584003664',
            ],
        ],
        'files' => [
            'boat_license/SA 1311-5-P.pdf',
        ],
    ],
    [
        'company_id' => 57,
        'license' => 'SA 1992/5/P',
        'capacity' => 15, // 3 crew + 12 passengers
        'expiry_date' => '2026-07-11',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'AZMI BIN MUSA',
                'ic_no' => '870805496365',
                'mate_card' => '84001291DIV_C2601',
                'seaman_card_no' => '201281006103',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MUSLIM BIN BADIRI',
                'ic_no' => '980717126687',
                'mate_card' => '84001967DIV_C',
                'seaman_card_no' => '202484009931',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SANIN BIN LAJAH BANGSA',
                'ic_no' => '890214126045',
                'mate_card' => '84002173DIV_C4302',
                'seaman_card_no' => '201584003664',
            ],
        ],
        'files' => [
            'boat_license/SA 1992-5-P.pdf',
        ],
    ],
    [
        'company_id' => 57,
        'license' => 'SA 10008/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-10-16',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'AZMI BIN MUSA',
                'ic_no' => '870805496365',
                'mate_card' => '84001291DIV C2601',
                'seaman_card_no' => '201281006103',
            ],
        ],
        'files' => [
            'boat_license/SA 10008-5-P.pdf',
        ],
    ],
    [
        'company_id' => 57,
        'license' => 'SA 11499/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-08-04',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'AZMI BIN MUSA',
                'ic_no' => '870805496365',
                'mate_card' => '84001291DIV_C2601',
                'seaman_card_no' => '201281006103',
            ],
        ],
        'files' => [
            'boat_license/SA 11499-5-P.pdf',
        ],
    ],
    [
        'company_id' => 57,
        'license' => 'SA 11599/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-08-21',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'AZMAN BIN MUSA',
                'ic_no' => '840721125973',
                'mate_card' => '84000341DIV_C',
                'seaman_card_no' => '201784004734',
            ],
        ],
        'files' => [
            'boat_license/SA 11599-5-P.pdf',
        ],
    ],
    [
        'company_id' => 57,
        'license' => 'SA 11699/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-08-21',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'AZMAN BIN MUSA',
                'ic_no' => '840721125973',
                'mate_card' => '84000341DIV_C',
                'seaman_card_no' => '201784004734',
            ],
        ],
        'files' => [
            'boat_license/SA 11699-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 65: YUE HAI TRAVEL SDN BHD
    // ========================================
    [
        'company_id' => 65,
        'license' => 'SA 929/5/P',
        'capacity' => 28, // Total passengers & crew
        'expiry_date' => '2026-07-15',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'SAZWAN BIN JOHAN',
                'ic_no' => '980614125219',
                'mate_card' => '84002132DIV_C',
                'seaman_card_no' => '202384009422',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ANDU BIN SALAHUDDIN',
                'ic_no' => '680815125173',
                'mate_card' => '84001434DIV_C3033',
                'seaman_card_no' => '201084000889',
            ],
        ],
        'files' => [
            'boat_license/SA 929-5-P.pdf',
        ],
    ],
    [
        'company_id' => 65,
        'license' => 'SA 9607/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-03-29',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'JAMAL BIN LAJJA MURA',
                'ic_no' => '821027125793',
                'mate_card' => '84001511DIV_C',
                'seaman_card_no' => '201884005419',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MAHYUDDIN BIN MAKLADIN',
                'ic_no' => '960425126775',
                'mate_card' => '84001320DIV_C',
                'seaman_card_no' => '202184007970',
            ],
        ],
        'files' => [
            'boat_license/SA 9607-5-P.pdf',
        ],
    ],
    [
        'company_id' => 65,
        'license' => 'SA 9651/5/P',
        'capacity' => 33, // Total passengers & crew
        'expiry_date' => '2026-07-15',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MAHYUDDIN BIN MAKLADIN',
                'ic_no' => '960425126775',
                'mate_card' => '84001320DIV_C',
                'seaman_card_no' => '202184007970',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'JAMAL BIN LAJJA MURA',
                'ic_no' => '821027125793',
                'mate_card' => '84001511DIV_C',
                'seaman_card_no' => '201884005419',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ABDUL NIUN BIN USUNG',
                'ic_no' => '960725126961',
                'mate_card' => '84001295DIV_C',
                'seaman_card_no' => '201684004224',
            ],
        ],
        'files' => [
            'boat_license/SA 9651-5-P.pdf',
        ],
    ],
    [
        'company_id' => 65,
        'license' => 'SA 10996/5/P',
        'capacity' => 32, // Total passengers & crew
        'expiry_date' => '2025-12-01',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MAHYUDDIN BIN MAKLADIN',
                'ic_no' => '960425126775',
                'mate_card' => '84001320DIV_C',
                'seaman_card_no' => '202184007970',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD SUHAIMI BIN ABDUL MAHIL',
                'ic_no' => '990705125491',
                'mate_card' => '84001973DIV_C',
                'seaman_card_no' => '201784005093',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'EDHOGAN BIN RAMLEE',
                'ic_no' => '920313126467',
                'mate_card' => '84001357DIV_C',
                'seaman_card_no' => '201784004725',
            ],
        ],
        'files' => [
            'boat_license/SA 10996-5-P.pdf',
        ],
    ],
    [
        'company_id' => 65,
        'license' => 'SA 11096/5/P',
        'capacity' => 44, // Total passengers & crew
        'expiry_date' => '2026-01-14',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MAHYUDDIN BIN MAKLADIN',
                'ic_no' => '960425126775',
                'mate_card' => '84001320DIV_C',
                'seaman_card_no' => '202184007970',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'ASLI BIN ALGUNDA',
                'ic_no' => '810313126005',
                'mate_card' => '84001896DIV_CR',
                'seaman_card_no' => '201684004221',
            ],
        ],
        'files' => [
            'boat_license/SA 11096-5-P.pdf',
        ],
    ],
    [
        'company_id' => 65,
        'license' => 'SA 11495/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-07-02',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MAHYUDDIN BIN MAKLADIN',
                'ic_no' => '960425126775',
                'mate_card' => '84001320DIV_C',
                'seaman_card_no' => '202184007970',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'FAIZAL BIN AB JIHIL',
                'ic_no' => '810430125925',
                'mate_card' => '84001487DIV_C',
                'seaman_card_no' => '201484002909',
            ],
        ],
        'files' => [
            'boat_license/SA 11495-5-P.pdf',
        ],
    ],
    [
        'company_id' => 65,
        'license' => 'SA 11496/5/P',
        'capacity' => 14, // 2 crew + 12 passengers
        'expiry_date' => '2026-07-02',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MAHYUDDIN BIN MAKLADIN',
                'ic_no' => '960425126775',
                'mate_card' => '84001320DIV_C',
                'seaman_card_no' => '202184007970',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'FAIZAL BIN AB JIHIL',
                'ic_no' => '810430125925',
                'mate_card' => '84001487DIV_C',
                'seaman_card_no' => '201484002909',
            ],
        ],
        'files' => [
            'boat_license/SA 11496-5-P.pdf',
        ],
    ],
    [
        'company_id' => 65,
        'license' => 'SA 11497/5/P',
        'capacity' => 16, // 4 crew + 12 passengers
        'expiry_date' => '2026-07-02',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'ANDU BIN SALAHUDDIN',
                'ic_no' => '680815125173',
                'mate_card' => '84001434DIV_C3033',
                'seaman_card_no' => '201084000889',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD ABDUL LATIF BIN BARAHIM',
                'ic_no' => '970708125069',
                'mate_card' => '84001980DIV_C',
                'seaman_card_no' => '201884005993',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MOHD NOOR FAIZOL BIN PUNGUTAN',
                'ic_no' => '000510122367',
                'mate_card' => '84002031DIV_C',
                'seaman_card_no' => '202384009263',
            ],
        ],
        'files' => [
            'boat_license/SA 11497-5-P.pdf',
        ],
    ],
    [
        'company_id' => 65,
        'license' => 'SA 11498/5/P',
        'capacity' => 16, // 4 crew + 12 passengers
        'expiry_date' => '2026-07-02',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MAHYUDDIN BIN MAKLADIN',
                'ic_no' => '960425126775',
                'mate_card' => '84001320DIV_C',
                'seaman_card_no' => '202184007970',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'SAZWAN BIN JOHAN',
                'ic_no' => '980614125219',
                'mate_card' => '84002132DIV_C',
                'seaman_card_no' => '202384009422',
            ],
        ],
        'files' => [
            'boat_license/SA 11498-5-P.pdf',
        ],
    ],

    // ========================================
    // Company 66: SOLARIS TAWAU TRAVEL & TOURS SDN BHD
    // ========================================
    [
        'company_id' => 66,
        'license' => 'SA 3929/5/P',
        'capacity' => 30, // Total passengers & crew
        'expiry_date' => '2026-07-15',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'RUSLAN BIN NUBILHATI',
                'ic_no' => '890519125377',
                'mate_card' => '81101548DIV C',
                'seaman_card_no' => '201464002811',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'RUSLI BIN MOHD JUKIN',
                'ic_no' => '760715136821',
                'mate_card' => '81161338DIV_C',
                'seaman_card_no' => '201051004247',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'YUEN YONG XING',
                'ic_no' => '841714125933',
                'mate_card' => '811015DIV_C',
                'seaman_card_no' => '202084007311',
            ],
        ],
        'files' => [
            'boat_license/SA 3929-5-P (PAGE 1).jpeg',
            'boat_license/SA 3929-5-P (PAGE 2).jpeg',
        ],
    ],
    [
        'company_id' => 66,
        'license' => 'SA 8633/5/P',
        'capacity' => 16, // 04 crew + 12 passengers
        'expiry_date' => '2025-07-14',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'MUHAMAD KADRAH BIN ULENA',
                'ic_no' => '880622125916',
                'mate_card' => '84001444DIV C',
                'seaman_card_no' => '201381007222',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'RUSLAN BIN NUBILHATI',
                'ic_no' => '890519125377',
                'mate_card' => '81101548DIV C',
                'seaman_card_no' => '201454002811',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'RUSLI BIN MOHD JUKIN',
                'ic_no' => '760715126021',
                'mate_card' => '81101338DIV C',
                'seaman_card_no' => '291081004247',
            ],
        ],
        'files' => [
            'boat_license/SA 8633-5-P (PAGE 1).jpeg',
            'boat_license/SA 8633-5-P (PAGE 2).jpeg',
        ],
    ],
    [
        'company_id' => 66,
        'license' => 'SA 9555/5/P',
        'capacity' => 31, // 3 crew + 28 passengers
        'expiry_date' => '2026-02-12',
        'boatmen' => [
            [
                'type' => 1, // TYPE_BOATMAN
                'name' => 'RUSLI BIN MOHD JUKIN',
                'ic_no' => '760715126021',
                'mate_card' => '81101338DIV C',
                'seaman_card_no' => '201081004247',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'RUSLAN BIN NUBILHATI',
                'ic_no' => '890519125377',
                'mate_card' => '81101548DIV C',
                'seaman_card_no' => '201484002811',
            ],
            [
                'type' => 2, // TYPE_ASSISTANT
                'name' => 'MUHAMAD KADRAH BIN ULENA',
                'ic_no' => '880622129915',
                'mate_card' => '84001444DIV C',
                'seaman_card_no' => '201381007222',
            ],
        ],
        'files' => [
            'boat_license/SA 9555-5-P (PAGE 1).jpeg',
            'boat_license/SA 9555-5-P (PAGE 2).jpeg',
        ],
    ],
];
