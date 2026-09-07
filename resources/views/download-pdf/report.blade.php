<html>

<head>
    @include('download-pdf-component.style-pdf')
</head>

<body>
    <h4 class="align-center" style="margin: 0px">{{ $title }}</h4>

    @php
        $jettyLabel = $departure ?? ($data->first()->departure_name ?? 'All Jetties');
    @endphp

    <table style="width: 100%; margin-top: 10px; margin-bottom: 15px; font-size: 10pt; border-collapse: collapse;">
        <tr>
            <td style="padding: 2px 4px; width: 180px;"><strong>Jetty</strong></td>
            <td style="padding: 2px 4px;">: {{ $jettyLabel }}</td>
        </tr>
        <tr>
            <td style="padding: 2px 4px;"><strong>Total Tour Operators</strong></td>
            <td style="padding: 2px 4px;">: {{ $totalTourOperators }}</td>
        </tr>
        <tr>
            <td style="padding: 2px 4px;"><strong>Total Manifest</strong></td>
            <td style="padding: 2px 4px;">: {{ $totalManifest }}</td>
        </tr>
    </table>

    <table class="table-border" style="font-size:10pt; margin-top: 5px;">
        <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Local Passenger (Adult)</th>
                <th scope="col">Local Passenger (Child)</th>
                <th scope="col">Foreign Passenger (Adult)</th>
                <th scope="col">Foreign Passenger (Child)</th>
                <th scope="col">Male</th>
                <th scope="col">Female</th>
                <th scope="col">Total Passenger</th>
                <th scope="col">Total Charge Passenger</th>
                <th scope="col">Total Boat Fee</th>
                <th scope="col">Total No of Manifest Form</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td class="nowrap">{{ $item->departure_date }}</td>
                    <td class="align-center">{{ $item->total_local_adult }}</td>
                    <td class="align-center">{{ $item->total_local_child }}</td>
                    <td class="align-center">{{ $item->total_foreign_adult }}</td>
                    <td class="align-center">{{ $item->total_foreign_child }}</td>
                    <td class="align-center">{{ $item->total_male }}</td>
                    <td class="align-center">{{ $item->total_female }}</td>
                    <td class="align-center">
                        {{ $item->total_local_adult + $item->total_local_child + $item->total_foreign_adult + $item->total_foreign_child }}
                    </td>
                    <td class="align-center nowrap">RM {{ number_format($item->total_charge_passenger, 2) }}</td>
                    <td class="align-center nowrap">RM {{ number_format($item->total_charge_boat_fee, 2) }}</td>
                    <td class="align-center">{{ $item->total_manifest }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td class="nowrap"><strong>TOTAL</strong></td>
                <td class="align-center">{{ $data->sum('total_local_adult') }}</td>
                <td class="align-center">{{ $data->sum('total_local_child') }}</td>
                <td class="align-center">{{ $data->sum('total_foreign_adult') }}</td>
                <td class="align-center">{{ $data->sum('total_foreign_child') }}</td>
                <td class="align-center">{{ $data->sum('total_male') }}</td>
                <td class="align-center">{{ $data->sum('total_female') }}</td>
                <td class="align-center">
                    {{ $data->sum('total_local_adult') + $data->sum('total_local_child') + $data->sum('total_foreign_adult') + $data->sum('total_foreign_child') }}
                </td>
                <td class="align-center nowrap">RM {{ number_format($data->sum('total_charge_passenger'), 2) }}</td>
                <td class="align-center nowrap">RM {{ number_format($data->sum('total_charge_boat_fee'), 2) }}</td>
                <td class="align-center">{{ $data->sum('total_manifest') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
