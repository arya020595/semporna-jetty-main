<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 10mm 80mm 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7pt;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }

        .footer {
            position: fixed;
            bottom: -70mm;
            left: 0;
            right: 0;
            height: 70mm;
        }

        .approved-by {
            margin: 5px 0;
            border: 2px solid #000;
            padding: 5px;
        }

        .procedures {
            font-size: 7pt;
            margin-top: 5px;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .header-left {
            display: table-cell;
            width: 15.1%;
            vertical-align: top;
            padding-right: 10px;
            font-size: 7pt;
        }

        .header-center {
            display: table-cell;
            width: 40%;
            text-align: center;
            vertical-align: top;
        }

        .header-center img {
            max-height: 120px;
            margin: 0 2px;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            width: 13%;
            vertical-align: top;
            padding-left: 10px;
            font-size: 7pt;
        }

        .office-use-box {
            border: 2px solid #000;
            width: 100%;
        }

        .office-use-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .office-use-box td {
            border: 1px solid #000;
            padding: 1px 1px;
            height: 16px;
            font-size: 7pt;
        }

        .office-use-box .header-cell {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .manifest-info {
            margin: 10px 0;
        }

        .manifest-info table {
            width: 100%;
        }

        .manifest-info td {
            padding: 2px 0;
        }

        .guest-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 7pt;
        }

        .guest-table th,
        .guest-table td {
            border: 1px solid #000;
            padding: 1px 1px;
            font-size: 7pt;
        }

        .guest-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .guest-table thead {
            display: table-header-group;
        }

        .guest-table td {
            height: auto;
            min-height: 14px;
        }

        /* Allow Next of Kin and Emergency Contact to wrap */
        .guest-table td:nth-child(7),
        .guest-table td:nth-child(8) {
            word-wrap: break-word;
            white-space: normal;
            max-height: 28px;
        }

        .footer-info {
            display: table;
            width: 100%;
            margin: 8px 0;
            font-size: 7pt;
        }

        .footer-col {
            display: table-cell;
            width: 33.33%;
            padding: 0 3px;
            vertical-align: top;
        }

        .extra-passenger {
            margin: 10px 0;
        }

        .extra-passenger table {
            width: 100%;
            border-collapse: collapse;
        }

        .extra-passenger th,
        .extra-passenger td {
            border: 1px solid #000;
            padding: 2px 2px;
            height: 16px;
            font-size: 7pt;
        }

        .extra-passenger th {
            background-color: #f0f0f0;
            font-size: 7pt;
        }

        .extra-passenger thead {
            display: table-header-group;
        }

        .approved-by {
            margin: 15px 0;
            border: 2px solid #000;
            padding: 10px;
            page-break-inside: avoid;
        }

        .approved-by-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .approved-boxes {
            display: table;
            width: 100%;
        }

        .approved-box {
            display: table-cell;
            width: 25%;
            border: 1px solid #000;
            height: 100px;
            text-align: center;
            vertical-align: bottom;
            padding: 5px;
        }

        .approved-box-status {
            font-size: 7pt;
            font-weight: bold;
            padding: 2px 4px;
            border-radius: 3px;
            display: inline-block;
            margin-bottom: 3px;
        }

        .status-accepted { color: #006400; background: #d4edda; }
        .status-amend    { color: #7d5a00; background: #fff3cd; }
        .status-rejected { color: #721c24; background: #f8d7da; }
        .status-pending  { color: #555555; background: #e9ecef; }

        .procedures {
            font-size: 7pt;
            margin-top: 10px;
        }

        .procedures-left {
            float: left;
            width: 48%;
        }

        .procedures-right {
            float: right;
            width: 30%;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- Footer Section (Fixed Position) -->
    <div class="footer">
        <!-- Footer Info - 3 Columns -->
        <div class="footer-info">
            <div class="footer-col">
                <div><strong>Boatman:</strong> {{ $data['manifest']->boatman_name ?? '_________________' }}</div>
                <div><strong>Seaman Number:</strong> {{ $data['manifest']->seaman_no ?? '_________________' }}</div>
                <div><strong>Boat No:</strong> {{ $data['manifest']->boat_number ?? '_________________' }}</div>
            </div>

            <div class="footer-col">
                <div><strong>Asst.Boatman:</strong> {{ $data['manifest']->assistant_name ?? '_________________' }}</div>
                <div><strong>IC Number:</strong> {{ $data['manifest']->assistant_ic_no ?? '_________________' }}</div>
            </div>

            <div class="footer-col">
                @if (count($data['arrInstructor'] ?? []) > 0)
                    <div><strong>Instructor:</strong></div>
                    @foreach ($data['arrInstructor'] as $instructor)
                        <div>{{ $instructor->name ?? 'N/A' }} ({{ $instructor->ic_no ?? 'N/A' }})</div>
                    @endforeach
                @endif
                @if (count($data['arrDivemaster'] ?? []) > 0)
                    <div><strong>Divemaster:</strong></div>
                    @foreach ($data['arrDivemaster'] as $divemaster)
                        <div>{{ $divemaster->name ?? 'N/A' }} ({{ $divemaster->ic_no ?? 'N/A' }})</div>
                    @endforeach
                @endif
            </div>
            <div class="footer-col">
                @if (count($data['arrGuide'] ?? []) > 0)
                    <div><strong>Guide:</strong></div>
                    @foreach ($data['arrGuide'] as $guide)
                        <div>{{ $guide->name ?? 'N/A' }} ({{ $guide->ic_no ?? 'N/A' }})</div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Approved By Section -->
        <div class="approved-by">
            @php
                $bypassApproval     = $data['bypassApproval'] ?? true;
                $authorityApprovals = $data['authorityApprovals'] ?? [];
                $isSabahParks       = $data['isSabahParks'] ?? true; // default true = all 4 required

                // Only count authorities that are required for this manifest
                $requiredApprovals = collect($authorityApprovals)->filter(fn($a) => $a['requires_approval'] ?? true);

                // Determine overall approval status (based only on required authorities)
                $overallStatus = 'Pending';
                $overallColor  = '#555555';
                $overallBg     = '#e9ecef';

                if (!$bypassApproval && $requiredApprovals->count() > 0) {
                    $hasRejected = $requiredApprovals->contains('status', -1);
                    $hasAmend    = $requiredApprovals->contains('status', 3);
                    $allApproved = $requiredApprovals->every(fn($a) => $a['status'] === 1);

                    if ($hasRejected) {
                        $overallStatus = 'Rejected';      $overallColor = '#721c24'; $overallBg = '#f8d7da';
                    } elseif ($hasAmend) {
                        $overallStatus = 'Amend Required'; $overallColor = '#7d5a00'; $overallBg = '#fff3cd';
                    } elseif ($allApproved) {
                        $overallStatus = 'Approved';      $overallColor = '#006400'; $overallBg = '#d4edda';
                    }
                }
            @endphp

            <div style="display: table; width: 100%; margin-bottom: 4px;">
                {{-- Left: Overall Approval Status + QR Code --}}
                <div style="display: table-cell; vertical-align: middle; width: 80px; text-align: center;">
                    @if (!$bypassApproval)
                        <div style="font-size: 6pt; font-weight: bold; color: {{ $overallColor }}; background: {{ $overallBg }}; border-radius: 3px; padding: 2px 4px; display: inline-block; margin-bottom: 4px;">
                            {{ $overallStatus }}
                        </div>
                    @endif
                    @if ($data['qrcode'] ?? false)
                        <div>
                            <img src="{{ $data['qrcode'] }}" style="width: 60px; height: 60px;" alt="QR">
                        </div>
                    @endif
                </div>
                {{-- Right: Authority boxes --}}
                <div style="display: table-cell; vertical-align: top;">
                    <div class="approved-by-title">Approved By:</div>
                    <div class="approved-boxes">
                        @php
                            $defaultAuth = [
                                ['authority_name'=>'Jabatan Pelabuhan Dan Dermaga Sabah','status'=>null,'status_text'=>'','user_name'=>null,'date'=>null,'comments'=>null],
                                ['authority_name'=>'Jabatan Laut Malaysia','status'=>null,'status_text'=>'','user_name'=>null,'date'=>null,'comments'=>null],
                                ['authority_name'=>'Polis Diraja Malaysia','status'=>null,'status_text'=>'','user_name'=>null,'date'=>null,'comments'=>null],
                                ['authority_name'=>'Sabah Parks','status'=>null,'status_text'=>'','user_name'=>null,'date'=>null,'comments'=>null],
                            ];
                            $displayAuthorities = $bypassApproval ? $defaultAuth : $authorityApprovals;
                        @endphp
                        @foreach ($displayAuthorities as $authority)
                            @php
                                $aStatus = $authority['status'] ?? null;
                            @endphp
                            <div class="approved-box" style="position: relative;">
                                @php
                                    $requiresApproval = $authority['requires_approval'] ?? true;
                                    $aStatus = $authority['status'] ?? null;
                                    if (!$requiresApproval) {
                                        $badgeColor = '#999999'; $badgeBg = '#e9ecef'; $badgeLabel = 'N/A';
                                    } elseif ($aStatus == 1)       { $badgeColor = '#006400'; $badgeBg = '#d4edda'; $badgeLabel = 'Approved'; }
                                    elseif ($aStatus == 3)  { $badgeColor = '#7d5a00'; $badgeBg = '#fff3cd'; $badgeLabel = 'Amend'; }
                                    elseif ($aStatus == -1) { $badgeColor = '#721c24'; $badgeBg = '#f8d7da'; $badgeLabel = 'Rejected'; }
                                    else                     { $badgeColor = '#555555'; $badgeBg = '#e9ecef'; $badgeLabel = 'Pending'; }
                                @endphp
                                @if (!$bypassApproval)
                                    {{-- Authority logo (dimmed if not required) --}}
                                    @if (!empty($authority['logo']) && file_exists($authority['logo']))
                                        <div style="text-align: center; padding: 4px 0;">
                                            <img src="{{ $authority['logo'] }}" style="max-height: 55px; max-width: 90%; opacity: {{ $requiresApproval ? '0.85' : '0.3' }};" alt="{{ $authority['authority_name'] }}">
                                        </div>
                                    @else
                                        <div style="height: 55px;"></div>
                                    @endif

                                    {{-- Status / N/A badge --}}
                                    <div style="margin-top: 4px;">
                                        <span style="font-size: 6pt; font-weight: bold; color: {{ $badgeColor }}; background: {{ $badgeBg }}; padding: 1px 4px; border-radius: 3px;">
                                            {{ $badgeLabel }}
                                        </span>
                                    </div>

                                    {{-- Approved by info (only if actually required and has data) --}}
                                    @if ($requiresApproval && $authority['user_name'])
                                        <div style="font-size: 5.5pt; color: #333; margin-top: 2px;">{{ $authority['user_name'] }}</div>
                                        <div style="font-size: 5.5pt; color: #555;">{{ $authority['date'] }}</div>
                                    @endif
                                @else
                                    {{-- Bypass mode: blank box with signature line --}}
                                    <div style="height: 60px;"></div>
                                    <div style="border-top: 1px solid #888; width: 80%; margin: 0 auto;"></div>
                                @endif
                                <div style="margin-top: 3px; font-size: 6.5pt;">{{ $authority['authority_name'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Procedures -->
        <div class="procedures">
            <div class="procedures-left">
                <div class="font-bold">Procedure of Passenger Manifest in Tourist Jetty & Information Centre Semporna
                </div>
                <div>1) Fill in guest name, passport number, gender and nationality</div>
                <div>2) Obtain Passenger Fee receipt from Hartawan Stabil Sdn Bhd Office</div>
                <div>3) Attach passenger Passenger Fee ticket</div>
                <div>4) Obtain approval from government agencies</div>
                <div>5) Submit form upon embarkation</div>
            </div>
            <div class="procedures-right">
                @if (isset($data['manifest']->departure) && $data['manifest']->departure->code == 'DPTR_00002')
                    {{-- Seafest Jetty Address --}}
                    <div style="font-size: 8.5pt;"><strong>Jetty Gerbang Lepa</strong></div>
                    <div style="font-size: 8.5pt;"><strong>Seafest Marina Jetty</strong></div>
                    <hr>
                    <div>Seafest Square</div>
                    <div>Jalan Floating, Pekan Semporna</div>
                    <div>91308 Semporna, Sabah</div>
                @else
                    {{-- Default Hartawan Stabil Address --}}
                    <div style="font-size: 8.5pt;"><strong>Hartawan Stabil Sdn. Bhd. (Co. No. 877086-H)</strong></div>
                    <div style="font-size: 8.5pt;"><strong>(Tourist Jetty Management Company)</strong></div>
                    <hr>
                    <div>Office No 1, Tourist Jetty & Information Centre</div>
                    <div>Jalan Bangau - Bangau</div>
                    <div>91307 Semporna, Sabah</div>
                    <div>089-784481</div>
                @endif
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <!-- Header Section -->
    <div class="header">
        <div class="header-left">
            <div><strong>Passenger Manifest Form (For All Resort Company)</strong></div>
            <br>
            <div>
                <strong>From Semporna To:</strong> {{ $data['destination'] ?? 'N/A' }}
            </div>
            <div>
                <strong>Company:</strong> {{ $data['manifest']->company_name ?? 'N/A' }}
            </div>
        </div>

        <div class="header-center">
            @php
                $departureCode = $data['manifest']->departure->code ?? '';
                $isSeafest = $departureCode == 'DPTR_00002';
                $isSemporna = $departureCode == 'DPTR_00001';
            @endphp

            @if (!$isSeafest)
                @if (!$isSemporna)
                    <img src="{{ public_path('/assets/images/Coat_of_arms_of_Sabah.jpg') }}" alt="Sabah Logo">
                @endif
                <img src="{{ public_path('/assets/images/semporna-logo.png') }}" alt="Semporna Logo">
            @endif
        </div>

        <div class="header-right">
            <div class="office-use-box">
                <table>
                    <tr>
                        <td colspan="2" class="header-cell">For Office Use</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;" class="header-cell">Passenger Fee</td>
                        <td style="width: 50%;" class="header-cell">Boat Fee</td>
                    </tr>
                    <tr class="text-center">
                        <td>{{ number_format(($data['manifest_fee']['total'] ?? 0) - ($data['manifest_fee']['boat_fee'] ?? 0), 2) }}
                        </td>
                        <td>{{ $data['manifest_fee']['boat_fee'] ?? '0.00' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="header-cell">Ref Number</td>
                    </tr>
                    <tr class="text-center">
                        <td colspan="2">{{ $data['manifest']->form_number ?? '' }}</td>
                    </tr>
                </table>
            </div>
            <br>
            <div>
                <strong>Payment Status:</strong>
                @if (config('features.bypass_seafest_payment') &&
                        $data['manifest']->departure &&
                        $data['manifest']->departure->code == 'DPTR_00002')
                    <span>-</span>
                @elseif ($data['manifest']->payment_status == 1)
                    <span style="color: green;">PAID</span>
                @else
                    <span style="color: orange;">PENDING</span>
                @endif
            </div>
            <div>
                <strong>Date:</strong>
                {{ $data['manifest']->departure_date ? date('d / m / Y', strtotime($data['manifest']->departure_date)) : '__/__/____' }}
            </div>
            <div>
                <strong>Departure Time:</strong> {{ $data['manifest']->departure_time ?? '____:____' }}
            </div>
        </div>
    </div>

    <!-- Guest Table -->
    @php
        $passengers = $data['passenger'] ?? [];

        $totalPassengers = count($passengers);

        // Maximum passengers per page
        $passengersPerPage = 20;
        $passengerChunks = array_chunk($passengers, $passengersPerPage);
        $totalChunks = count($passengerChunks);
    @endphp

    @foreach ($passengerChunks as $chunkIndex => $passengerChunk)
        @php
            $isLastChunk = $chunkIndex === $totalChunks - 1;
            $startNumber = $chunkIndex * $passengersPerPage + 1;
        @endphp

        <table class="guest-table" style="{{ !$isLastChunk ? 'page-break-after: always;' : '' }}">
            <thead>
                <tr>
                    <th colspan="9" style="text-align: center; font-weight: bold;">
                        GUEST LIST
                    </th>
                </tr>
                <tr>
                    <th style="width: 3%;">NO</th>
                    <th style="width: 15%;">Guest Name List</th>
                    <th style="width: 10%;">I.C / Passport No.</th>
                    <th style="width: 8%;">Nationality</th>
                    <th style="width: 5%;">Gender</th>
                    <th style="width: 8%;">Age / YoB</th>
                    <th style="width: 10%;">Activity</th>
                    <th style="width: 18%;">Next Of Kin</th>
                    <th style="width: 15%;">Emergency Contact No.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($passengerChunk as $index => $passenger)
                    <tr>
                        <td class="text-center">{{ $startNumber + $index }}</td>
                        <td>{{ $passenger['name'] ?? '' }}</td>
                        <td>{{ $passenger['ic_no'] ?? '' }}</td>
                        <td>{{ $passenger['nationality_name'] ?? '' }}</td>
                        <td class="text-center">{{ $passenger['gender'] ?? '' }}</td>
                        <td class="text-center">{{ $passenger['age'] ?? '' }} / {{ $passenger['year_of_birth'] ?? '-' }}</td>
                        <td>
                                {{ $passenger['activity_name'] ?? '' }}
                        </td>
                        <td>{{ $passenger['next_of_kin'] ?? '' }}</td>
                        <td>{{ $passenger['emergency_contact'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <!-- Staff Table -->
    @php
        $staffs = $data['staff'] ?? [];
        $totalStaffs = count($staffs);
        $staffChunks = array_chunk($staffs, $passengersPerPage);
        $totalStaffChunks = count($staffChunks);
    @endphp

    @if ($totalStaffs > 0)
        @foreach ($staffChunks as $chunkIndex => $staffChunk)
            @php
                $isLastStaffChunk = $chunkIndex === $totalStaffChunks - 1;
                $startNumber = $chunkIndex * $passengersPerPage + 1;
            @endphp
            <table class="guest-table" style="{{ !$isLastStaffChunk ? 'page-break-after: always;' : '' }}">
                <thead>
                    <tr>
                        <th colspan="6" style="text-align: center; font-weight: bold;">
                            STAFF LIST
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 5%;">NO</th>
                        <th style="width: 35%;">Staff Name List</th>
                        <th style="width: 20%;">I.C / Passport No.</th>
                        <th style="width: 20%;">Nationality</th>
                        <th style="width: 10%;">Gender</th>
                        <th style="width: 10%;">Age / YoB</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($staffChunk as $index => $staffMem)
                        <tr>
                            <td class="text-center">{{ $startNumber + $index }}</td>
                            <td>{{ $staffMem['name'] ?? '' }}</td>
                            <td>{{ $staffMem['ic_no'] ?? '' }}</td>
                            <td>{{ $staffMem['nationality_name'] ?? '' }}</td>
                            <td class="text-center">{{ $staffMem['gender'] ?? '' }}</td>
                            <td class="text-center">{{ $staffMem['age'] ?? '' }} / {{ $staffMem['year_of_birth'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif

    <!-- Extra Passenger Section -->
    @php
        $additionalPassengers = $data['additionalPassenger'] ?? [];
        $totalAdditionalPassengers = count($additionalPassengers);

        // HARDCODED EXAMPLE - Uncomment to test EXTRA PASSENGER table display
        // $additionalPassengers = [
        // [
        //     'name' => 'John Doe (Additional)',
        //     'ic_no' => '990101-01-1234',
        //     'nationality_name' => 'West Malaysian',
        //     'gender' => 'M',
        //     'activity_name' => 'Diving',
        //     'next_of_kin' => 'Jane Doe / Mother',
        //     'emergency_contact' => '+60 12-345 6789',
        // ],
        // [
        //     'name' => 'Ahmad bin Ali (Extra)',
        //     'ic_no' => '950505-12-5678',
        //     'nationality_name' => 'West Malaysian',
        //     'gender' => 'M',
        //     'activity_name' => 'Snorkeling',
        //     'next_of_kin' => 'Siti binti Ahmad / Wife',
        //     'emergency_contact' => '+60 13-987 6543',
        // ],
        // [
        //     'name' => 'Sarah Wong (Approved Extra)',
        //     'ic_no' => '921212-06-9999',
        //     'nationality_name' => 'West Malaysian',
        //     'gender' => 'F',
        //     'activity_name' => 'Island Tour',
        //     'next_of_kin' => 'Wong Kim Chuan / Father',
        //     'emergency_contact' => '+60 16-111 2222',
        // ],
        // ];
        // $totalAdditionalPassengers = count($additionalPassengers);

    @endphp

    @if ($totalAdditionalPassengers > 0)
        <div class="extra-passenger">
            <table>
                <thead>
                    <tr>
                        <th colspan="9" style="text-align: center; font-weight: bold;">
                            EXTRA PASSENGERS
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 3%;">NO</th>
                        <th style="width: 15%;">Guest Name List</th>
                        <th style="width: 10%;">I.C / Passport No.</th>
                        <th style="width: 8%;">Nationality</th>
                        <th style="width: 5%;">Gender</th>
                        <th style="width: 8%;">Age / YoB</th>
                        <th style="width: 10%;">Activity</th>
                        <th style="width: 18%;">Next Of Kin</th>
                        <th style="width: 15%;">Emergency Contact No.</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < $totalAdditionalPassengers; $i++)
                        @php
                            $additionalPassenger = $additionalPassengers[$i] ?? null;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $additionalPassenger['name'] ?? '' }}</td>
                            <td>{{ $additionalPassenger['ic_no'] ?? '' }}</td>
                            <td>{{ $additionalPassenger['nationality_name'] ?? '' }}</td>
                            <td class="text-center">{{ $additionalPassenger['gender'] ?? '' }}</td>
                            <td class="text-center">{{ $additionalPassenger['age'] ?? '' }} / {{ $additionalPassenger['year_of_birth'] ?? '-' }}</td>
                            <td>{{ $additionalPassenger['activity_name'] ?? '' }}</td>
                            <td>{{ $additionalPassenger['next_of_kin'] ?? '' }}</td>
                            <td>{{ $additionalPassenger['emergency_contact'] ?? '' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    @endif


</body>

</html>
