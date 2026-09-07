<table style="width: 100%">
    <tr>
        <th style="width: 20%" class="align-left">Company Name</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $manifest->company_name }}">
        </td>
        <th style="width: 20%" class="align-left">Assistant</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $manifest->assistant_name }}">
        </td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left">Boat Number</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $manifest->boat_number }}">
        </td>
        <th style="width: 20%" class="align-left">Mate No.</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $manifest->assistant_mate_no }}">
        </td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left">Boatman</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $manifest->boatman_name }}">
        </td>
        <th style="width: 20%" class="align-left">IC No.</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $manifest->assistant_ic_no }}">
        </td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left">Mate No.</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $manifest->boatman_mate_no }}">
        </td>
        <th style="width: 20%" class="align-left"></th>
        <td style="width: 30%"></td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left">Seaman No.</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $manifest->seaman_no }}">
        </td>
        <th style="width: 20%" class="align-left"></th>
        <td style="width: 30%"></td>
    </tr>
    <tr>
        <th style="width: 20%" class="align-left">IC No.</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $manifest->boatman_ic_no }}">
        </td>
        <th style="width: 20%" class="align-left"></th>
        <td style="width: 30%"></td>
    </tr>
</table>

<hr />

<h6 class="section-subheader">Instructor Details</h6>
<table style="width: 100%">
    @foreach($arrInstructor as $boatman)
    <tr>
        <th style="width: 20%" class="align-left">Instructor Name</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $boatman->name }}" />
        </td>
        <th style="width: 20%" class="align-left">Instructor IC No.</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $boatman->ic_no }}" />
        </td>
    </tr>
    @endforeach
</table>
<hr />

<h6 class="section-subheader">Divemaster Details</h6>
<table style="width: 100%">
    @foreach($arrDivemaster as $boatman)
    <tr>
        <th style="width: 20%" class="align-left">Divemaster Name</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $boatman->name }}" />
        </td>
        <th style="width: 20%" class="align-left">Divemaster IC No.</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $boatman->ic_no }}" />
        </td>
    </tr>
    @endforeach
</table>
<hr />

<h6 class="section-subheader">Guide Details</h6>
<table style="width: 100%">
    @foreach($arrGuide as $boatman)
    <tr>
        <th style="width: 20%" class="align-left">Guide Name</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $boatman->name }}" />
        </td>
        <th style="width: 20%" class="align-left">Guide IC No.</th>
        <td style="width: 30%">
            <input type="text"
                class="form-control"
                value="{{ $boatman->ic_no }}" />
        </td>
    </tr>
    @endforeach
</table>
