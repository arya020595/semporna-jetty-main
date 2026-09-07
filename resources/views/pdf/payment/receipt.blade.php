<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title ?? 'Payment Receipts' }}</title>
    <style>
        @page {
            margin: 15mm 15mm 30mm 15mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
        }

        .company-info {
            float: left;
            width: 60%;
        }

        .receipt-box {
            float: right;
            width: 30%;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
        }

        .receipt-title {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        .receipt-details {
            font-size: 9px;
        }

        .clear {
            clear: both;
        }

        .bill-to {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        th {
            background-color: #f0f0f0;
            border-bottom: 2px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            border-bottom: 1px solid #eee;
            padding: 8px;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .amount-words-line {
            border-bottom: 1px solid #333;
            padding: 5px 0;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .footer-section {
            margin-top: 20px;
            width: 100%;
        }

        .notes {
            float: left;
            width: 55%;
            font-size: 10px;
        }

        .notes p {
            margin: 0 0 5px 0;
        }

        .notes ol {
            margin: 0;
            padding-left: 15px;
        }

        .notes li {
            margin-bottom: 2px;
        }

        .bank-details {
            float: right;
            width: 40%;
            text-align: left;
            color: #0000FF;
            /* Blue color as in image */
            font-weight: bold;
            margin-bottom: 20px;
        }

        .bank-details .company-name {
            font-size: 12px;
        }

        .bank-details .account-no {
            font-size: 12px;
        }

        .grand-total-row {
            font-weight: bold;
            padding: 5px 0;
        }

        .disclaimer-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px 15px;
            border-top: 1px solid #ccc;
            background-color: #f9f9f9;
            font-size: 8pt;
            font-style: italic;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="company-info">
            <strong>HARTAWAN STABIL SDN. BHD.</strong> (Co. 877086-H)<br>
            PEJABAT PENGURUSAN JETI, JETI PELANCONG SEMPORNA<br>
            P.O.BOX 116, JALAN BANGAU-BANGAU 91307 SEMPORNA, SABAH<br>
            TEL NO: 089-784481 FAX NO: 089-784482
        </div>
        <div class="receipt-box">
            <div class="receipt-title">PAYMENT RECEIPTS</div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="bill-to">
        <div style="float: left; width: 60%;">
            <strong>Company: </strong>{{ $company->name }}
        </div>
        <div style="float: right; width: 35%; text-align: left;">
            <div style="margin-bottom: 5px;">
                <strong>From - To: </strong>{{ $startDate }} - {{ $endDate }}
            </div>
            {{-- <div>
                <strong>Issued By: </strong>{{ $issuedBy }}
            </div> --}}
        </div>
        <div class="clear"></div>
    </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%" class="text-center">NO.</th>
                <th style="width: 15%" class="text-center">TRANSACTION DATE</th>
                <th style="width: 20%" class="text-center">MANIFEST NO.</th>
                <th style="width: 15%" class="text-center">BOAT NO.</th>
                <th style="width: 25%" class="text-center">REMARKS</th>
                <th style="width: 20%" class="text-right">AMOUNT (RM)</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse($receipts as $index => $receipt)
                @php $total += $receipt['amount']; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}.</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($receipt['transaction_date'])->format('d/m/Y') }}
                    </td>
                    <td class="text-center">{!! $receipt['manifest_number'] !!}</td>
                    <td class="text-center">{!! $receipt['boat_number'] !!}</td>
                    <td class="text-center">{!! $receipt['remarks'] !!}</td>
                    <td class="text-right">{{ number_format($receipt['amount'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No payment records found for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="amount-words-line">
        {{ $amountWords }}
    </div>

    <table style="margin-top: 5px;">
        <tbody>
            <tr>
                <td style="width: 80%; border: none;"></td>
                <td style="width: 20%; border-top: 1px solid #333; border-bottom: 3px double #333; padding: 5px 0;">
                    TOTAL
                </td>
                <td
                    style="width: 20%; font-weight: bold; text-align: right; border-top: 1px solid #333; border-bottom: 3px double #333; padding: 5px 0;">
                    : RM {{ number_format($totalAmount, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- <div class="footer-section">
        <div class="notes">
            <p><strong>Note:</strong></p>
            <ol>
                <li>Please indicate our Receipt on your remittance.</li>
                <li>Any discrepancies regarding this bill should be lodged within seven (7) days from the date hereof.
                </li>
                <li>We reserve the right to charge interest at the rate of 1.5% per month on all overdue accounts.</li>
                <li>All payments by cheques must be crossed and made in favour of <strong>HARTAWAN STABIL
                        SDN.BHD.</strong>
            </ol>
        </div>
        <div class="clear"></div>
    </div> --}}

    <div class="disclaimer-footer">
        <strong>Disclaimer:</strong> This is not the final receipt. For final receipt please contact Hartawan Stabil (089-784481).
    </div>

</body>

</html>
