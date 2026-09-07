<html>

<head>
    @include('download-pdf-component.style-pdf')
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
        }

        .section-header {
            background-color: rgba(0, 68, 171, 1) !important;
            padding: 1rem 1.5rem;
            font-size: 1rem;
            color: white;
            margin: 10px 0px;
        }

        .section-subheader {
            padding: 0.5rem 0px;
            font-size: 1rem;
            margin: 0px 0px;
        }

        .form-control {
            display: block;
            padding: 0.375rem 0.75rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #e9ecef;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0.25rem;
        }

        .bg-primary {
            background-color: rgba(0, 68, 171, 1);
            color: white;
        }

        .watermark {
            position: fixed;
            top: 35%;
            left: 10%;
            width: 80%;
            text-align: center;
            opacity: 0.3;
            font-size: 10em;
            transform: rotate(-45deg);
            z-index: 100;
        }
    </style>
</head>

<body>
    <div class="watermark">DRAFT</div>
    <table style="width:100%">
        <tr>
            <td>
                <h3 style="font-size: 2rem">RECEIPT</h3>
            </td>
            <td style="text-align: right;">
                <img
                    src="{{ $data['qrcode'] }}"
                    alt="qrcode for approval"
                    width="150px" />
            </td>
        </tr>
    </table>
    <hr />
    <table style="width:100%; margin-top: 10px;">
        <tr>
            <td style="text-align: left;">
                <div>
                    <strong>Date Created: </strong>
                    {{ $data['manifest']->created_at->format("d-m-Y") }}
                </div>
                <div>
                    <strong>Manifest Form Ref No: </strong>
                    {{ $data['manifest']->form_number }}
                </div>
            </td>
            <td style="text-align: right;">
                <div style="margin: 3px 0px;">
                    <strong>Payment Status: </strong>
                    @include('download-pdf-component.status-badge', [
                    "type" => $data['manifest']->payment_status == 1 ? 'success' : 'warning',
                    "text" => $data['manifest']->payment_status_text
                    ])
                </div>
                <div style="margin: 3px 0px;">
                    <strong>Authority Approval: </strong>
                    @include('download-pdf-component.status-badge', [
                    "type" => $data['manifest']->status == 1 ? 'success' : 'warning',
                    "text" => $data['manifest']->status_text
                    ])
                </div>
            </td>
        </tr>
    </table>

    <h6 class="section-header">Departure Details</h6>
    <table style="width: 100%">
        <tr>
            <th style="width: 20%" class="align-left">Departure Date</th>
            <td style="width: 30%">
                <input type="text"
                    class="form-control"
                    value="{{ $data['manifest']->departure_date }}">
            </td>
            <th style="width: 20%" class="align-left">Departure Date</th>
            <td style="width: 30%">
                <input type="text"
                    class="form-control"
                    value="{{ $data['manifest']->departure_time }}">
            </td>
        </tr>
    </table>


    <h6 class="section-header">Boat & Company Information</h6>
    @include('download-pdf-component.manifest.boat-company-details', [
    "manifest" => $data["manifest"],
    "arrInstructor" => $data["arrInstructor"],
    "arrDivemaster" => $data["arrDivemaster"],
    "arrGuide" => $data["arrGuide"]
    ])

    <h6 class="section-header">Destination</h6>
    <table style="width:100%">
        <tr>
            <th style="width: 20%" class="align-left">From</th>
            <td style="width: 30%">
                <input type="text"
                    class="form-control"
                    value="{{ $data['manifest']->departure_name }}" />
            </td>
            <th style="width: 10%" class="align-left">
                <img src="{{ public_path('assets/images/arrow-destination.png') }}"
                    style="width: 70%" />
            </th>
            <th style="width: 10%" class="align-left">To</th>
            <td style="width: 30%">
                <input type="text"
                    class="form-control"
                    value="{{ $data['destination'] }}" />
            </td>
        </tr>
    </table>

    <h6 class="section-header">Boat Staff Information</h6>
    @include('download-pdf-component.manifest.passenger-details', [
    "arrPassenger" => $data["staff"]
    ])

    <h6 class="section-header">Boat Passenger Information</h6>
    @include('download-pdf-component.manifest.passenger-details', [
    "arrPassenger" => $data["passenger"]
    ])

    <h6 class="section-header">Overall Fee Details</h6>
    @include('download-pdf-component.manifest.overall-fee-details', [
    "manifestFee" => $data["manifest_fee"]
    ])
</body>

</html>
