<?php
$count = count($arrPassenger);
?>

<table class="table-border compact" style="width: 100%; font-size: 0.8rem">
    <tr>
        <th class="nowrap">No.</th>
        <th class="align-left ">Guest Name</th>
        <th class="align-left">IC/Passport No.</th>
        <th class="align-left nowrap">Nationality</th>
        <th class="align-left nowrap">Gender</th>
        <th class="align-left nowrap">Age</th>
        <th class="align-left nowrap">Activity</th>
        <th class="align-left ">Next Of Kin</th>
        <th class="align-left ">Emergency No.</th>
    </tr>
    @foreach($arrPassenger as $key => $pasengger)
    <tr>
        <td class="align-left">{{ $key + 1 }}</td>
        <td class="align-left">{{ $pasengger->name }}</td>
        <td class="align-left">{{ $pasengger->ic_no }}</td>
        <td class="align-left">{{ $pasengger->nationality_name }}</td>
        <td class="align-left">{{ $pasengger->gender_text }}</td>
        <td class="align-left">{{ $pasengger->age }}</td>
        <td class="align-left">{{ $pasengger->activity_name }}</td>
        <td class="align-left">{{ $pasengger->next_of_kin }}</td>
        <td class="align-left">{{ $pasengger->emergency_contact }}</td>
    </tr>
    @endforeach
</table>
