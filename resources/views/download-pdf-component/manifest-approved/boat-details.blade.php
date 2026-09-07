<table style="width: 100%">
    <tr>
        <th style="width: 20%" class="align-left">Boatman</th>
        <td style="width: 30%">: {{ $manifest->boatman_name }}</td>
        <th style="width: 20%" class="align-left">Boat Number</th>
        <td style="width: 30%">: {{ $manifest->boat_number }}</td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left">Seaman No.</th>
        <td style="width: 30%">: {{ $manifest->seaman_no }}</td>
        <th style="width: 20%" class="align-left"></th>
        <td style="width: 30%"> </td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left">IC No.</th>
        <td style="width: 30%">: {{ $manifest->boatman_ic_no }}</td>
        <th style="width: 20%" class="align-left"></th>
        <td style="width: 30%"></td>
    </tr>

</table>

<table style="width: 100%; margin-top: 20px">
    <tr>
        <th style="width: 20%" class="align-left">Assistant Boatman</th>
        <td style="width: 30%">: {{ $manifest->assistant_name }}</td>
        <th style="width: 20%" class="align-left"></th>
        <td style="width: 30%"></td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left">Seaman No.</th>
        <td style="width: 30%">: {{ $manifest->assistant_seaman_no }}</td>
        <th style="width: 20%" class="align-left"></th>
        <td style="width: 30%"></td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left">IC Number</th>
        <td style="width: 30%">: {{ $manifest->assistant_ic_no }}</td>
        <th style="width: 20%" class="align-left"></th>
        <td style="width: 30%"></td>
    </tr>
</table>
