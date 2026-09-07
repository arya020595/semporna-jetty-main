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

        .logo-semporna {
            height: 120px;
        }

        /*
        table td {
            border: 1px solid black;
            padding: 0px;
        } */
    </style>
</head>

<body>
    <table style="width:100%">
        <tr>
            <td style="width:33.3%; vertical-align:bottom">
                <strong>
                    Majlis Daerah Semporna Peti Surat 134, 91308
                    Semporna Sabah
                </strong><br /><br />
                <strong>Passenger Manifest Form <br />(For All Resort
                    Company)</strong>
            </td>
            <td style="width:33.3%; text-align:center; padding-top:30px;">
                <img
                    src="{{ public_path('/assets/images/Coat_of_arms_of_Sabah.jpg') }}"
                    class="logo-semporna"
                    alt="Sabah Logo" />
                <img
                    src="{{ public_path('/assets/images/semporna-logo.png') }}"
                    class="logo-semporna"
                    alt="Semporna Logo" />
            </td>
            <td style="width:33.3%; text-align: right; font-size: 0.7rem; line-height: 0.8rem">
                <div>
                    <strong style="display:inline-block; margin-bottom:3px;">Payment Status: </strong>
                    @include('download-pdf-component.manifest-approved.status-badge', [
                    "type" => $data['manifest']->payment_status == 1 ? 'success' : 'warning',
                    "text" => $data['manifest']->payment_status_text
                    ])
                </div>
                <div style="margin: 3px 0px;">
                    <strong style="display:inline-block; margin-bottom:3px;">Authority Approval: </strong>
                    @include('download-pdf-component.manifest-approved.status-badge', [
                    "type" => $data['manifest']->status == 1 ? 'success' : 'warning',
                    "text" => $data['manifest']->status_text
                    ])
                </div>
                <img
                    style="margin-bottom: -30px;"
                    src="{{ $data['qrcode'] }}"
                    alt="qrcode for approval"
                    width="100px" />

            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left; padding 0px;">
                <div>
                    <strong>Manifest Form No.: </strong>
                    {{ $data['manifest']->form_number }}
                </div>
                <div>
                    <strong>From {{ $data['manifest']->departure_name }} To: </strong>
                    {{ $data['destination'] }}
                </div>
                <div>
                    <strong>Company: </strong>
                    {{ $data['manifest']->company_name }}
                </div>
            </td>
            <td style="vertical-align: bottom; text-align:right">
                <div style=" font-size: 0.8rem;">
                    <strong>Departure Date: </strong>
                    {{ $data['manifest']->departure_date }}
                </div>
                <div style="font-size: 0.8rem;">
                    <strong>Departure Time: </strong>
                    {{ $data['manifest']->departure_time }}
                </div>
            </td>
        </tr>
    </table>
    <hr />


    @include('download-pdf-component.manifest-approved.passenger-table', [
    "arrPassenger" => $data["passenger"]
    ])


    <hr />

    @include('download-pdf-component.manifest-approved.boat-details', [
    "manifest" => $data["manifest"],
    "arrInstructor" => $data["arrInstructor"],
    "arrDivemaster" => $data["arrDivemaster"],
    "arrGuide" => $data["arrGuide"]
    ])


    @if($data["staff"]->count())
    <hr />
    <h4>Staff </h4>
    @include('download-pdf-component.manifest-approved.passenger-table', [
    "arrPassenger" => $data["additionalPassenger"]
    ])
    @endif

    @if($data["additionalPassenger"]->count())
    <hr />
    <h4>Extra Passenger with Permission of Officer of Jabatan Pelabuhan & Dermaga</h4>
    @include('download-pdf-component.manifest-approved.passenger-table', [
    "arrPassenger" => $data["additionalPassenger"]
    ])
    @endif

    <hr />

    <h4>Approved By:</h4>

    <table style="width:100%">
        <tr style="text-align: center;">
            @foreach($data["approvements"] as $approvement)
            <td style="width:{{ 100 / $data['approvements']->count() }}%; border:1px solid #333; position:relative;">
                @include("download-pdf-component.manifest-approved.approved-list", [
                "approvement" => $approvement
                ])
            </td>
            @endforeach
        </tr>
    </table>


    <h6 class="section-header">Overall Fee Details</h6>
    @include('download-pdf-component.manifest.overall-fee-details', [
    "manifestFee" => $data["manifest_fee"]
    ])
</body>

</html>
