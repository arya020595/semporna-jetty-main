<?php
$totalLocal = $manifestFee['local_adult'] * $manifestFee['local_adult_fee'];
$totalForeign = $manifestFee['foreign_adult'] * $manifestFee['foreign_adult_fee'];

?>

<h6 class="section-subheader">Local Passengers</h6>
<hr />
<table style="width: 100%">
    <tr>
        <th style="width: 20%;padding-right: 20px;" class="align-right">Adult</th>
        <td style="width: 30%;">
            <input type="text"
                class="form-control"
                value="{{ $manifestFee['local_adult'] }}">
        </td>
        <td style="width: 20%;" class="align-left"> x {{ $manifestFee['local_adult_fee'] }} RM</td>
        <td style="width: 30%;"></td>
    </tr>
    <tr>
        <th style="width: 20%;padding-right: 20px;" class="align-right">Child</th>
        <td style="width: 30%;">
            <input type="text"
                class="form-control"
                value="{{ $manifestFee['local_child'] }}">
        </td>
        <td style="width: 20%;" class="align-left"> x {{ $manifestFee['local_child_fee'] > 0 ? $manifestFee['local_child_fee'] . ' RM' : 'FREE' }}</td>
        <td style="width: 30%;"></td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left"></th>
        <td colspan="2">
            <hr />
        </td>
        <td style="width: 30%"></td>
    </tr>
    <tr>
        <th style="width: 20%; padding-right: 20px;" class="align-right">TOTAL</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control bg-primary"
                value="{{ number_format($totalLocal, 2) }}">
        </td>
        <th style="width: 20%" class="align-left"></th>
        <td style="width: 30%"></td>
    </tr>
</table>

<h6 class="section-subheader">Foreign Passengers</h6>
<hr />
<table style="width: 100%">
    <tr>
        <th style="width: 20%;padding-right: 20px;" class="align-right">Adult</th>
        <td style="width: 30%;">
            <input type="text"
                class="form-control"
                value="{{ $manifestFee['foreign_adult'] }}">
        </td>
        <td style="width: 20%;" class="align-left"> x {{ $manifestFee['foreign_adult_fee'] }} RM</td>
        <td style="width: 30%;"></td>
    </tr>
    <tr>
        <th style="width: 20%;padding-right: 20px;" class="align-right">Child</th>
        <td style="width: 30%;">
            <input type="text"
                class="form-control "
                value="{{ $manifestFee['foreign_child'] }}">
        </td>
        <td style="width: 20%;" class="align-left"> x {{ $manifestFee['foreign_child_fee'] > 0 ? $manifestFee['foreign_child_fee'] . ' RM' : 'FREE' }}</td>
        <td style="width: 30%;"></td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left"></th>
        <td colspan="2">
            <hr />
        </td>
        <td style="width: 30%"></td>
    </tr>
    <tr>
        <th style="width: 20%; padding-right: 20px;" class="align-right">TOTAL</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control bg-primary"
                value="{{ number_format($totalForeign, 2) }}">
        </td>
        <th style="width: 20%" class="align-left"></th>
        <td style="width: 30%"></td>
    </tr>
</table>

<h6 class="section-subheader">Boat Fee</h6>
<hr />
<table style="width: 100%">
    <tr>
        <th style="width: 20%; padding-right: 20px;" class="align-right">Boat Fee</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control bg-primary"
                value="{{ $manifestFee['boat_fee'] }}">
        </td>
        <th style="width: 20%" class="align-left"></th>
        <td style="width: 30%"></td>
    </tr>
</table>

<hr />
<div class="align-right">
    <span type="text"
        class="form-control bg-primary"
        style="font-weight:bold; font-size:12pt; display: inline-block">
        Subtotal : <strong>RM {{ $manifestFee['total'] }}<strong>
    </span>
</div>
