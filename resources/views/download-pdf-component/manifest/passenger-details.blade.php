<?php
$count = count($arrPassenger);
?>

<table style="width: 100%">
    @foreach($arrPassenger as $key => $pasengger)
    <tr>
        <th style="width: 20%;" class="align-left">Guest Name</th>
        <td style="width: 30%;">
            <input type="text"
                class="form-control"
                value="{{ $pasengger->name }}">
        </td>
        <th style="width: 20%;" class="align-left">Gender</th>
        <td style="width: 30%;">
            <input type="text"
                class="form-control"
                value="{{ $pasengger->gender_text }}">
        </td>
    </tr>
    <tr>
        <th style="width: 20%; vertical-align:top; padding-top: 15px;" class="align-left">IC/Passport No.</th>
        <td style="width: 30%; vertical-align:top;">
            <input type="text"
                class="form-control"
                value="{{ $pasengger->ic_no }}">
        </td>
        <th style="width: 20%; vertical-align:top; padding-top: 15px;" class="align-left">Next of Kin</th>
        <td style="width: 30%; vertical-align:top;">
            <input type="text"
                class="form-control"
                value="{{ $pasengger->next_of_kin }}">
        </td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left">Nationality</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $pasengger->nationality_name }}">
        </td>
        <th style="width: 20%" class="align-left">Emergency Contact</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $pasengger->emergency_contact }}">
        </td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left">Age / YoB</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $pasengger->age }} / {{ $pasengger->year_of_birth ?? '-' }}">
        </td>
        <th style="width: 20%" class="align-left">Activity</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $pasengger->activity_name }}">
        </td>
    </tr>

    @if ($count > $key + 1)
    <tr>
        <td colspan="4">
            <hr />
        </td>
    </tr>
    @endif

    @endforeach
</table>
