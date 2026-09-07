<?php

$ARR_AUTHORITIES = [
    "role_3" => [
        "name" => "Polis Diraja Malaysia",
        "logo" => public_path("/assets/images/pdrm-logo.png"),
        "stamp" => public_path("/assets/images/pdrm-stamp.png"),
    ],
    "role_4" => [
        "name" => "Jabatan Laut Malaysia",
        "logo" => public_path("/assets/images/jabatan-laut-logo.png"),
        "stamp" => public_path("/assets/images/jabatan-laut-stamp.png"),
    ],
    "role_5" => [
        "name" => "Sabah Parks",
        "logo" => public_path("/assets/images/sabah-parks-logo.jpeg"),
        "stamp" => public_path("/assets/images/jabatan-laut-stamp.png"),
    ],
    "role_6" => [
        "name" => "Jabatan Pelabuhan Dan Dermaga Sabah",
        "logo" => public_path("/assets/images/jabatan-pelabuhan-logo.png"),
        "stamp" => public_path("/assets/images/jabatan-pelabuhan-stamp.png"),
    ],
];

$STATUS_APPROVED = 1;
$STATUS_REJECTED = -1;

$selAuthorities = $ARR_AUTHORITIES["role_" . $approvement->role_id] ?? [];

$statusIcon = $approvement->status == $STATUS_APPROVED
    ? public_path("/assets/images/icon_approved.png")
    : public_path("/assets/images/icon_rejected.png");
?>

<div style="z-index:0">
    <div class="text-center">{{ $selAuthorities["name"] ?? '' }}</div>
    <div class="">
        <img
            src="{{ $selAuthorities['logo'] ?? '' }}"
            style="max-height: 130px; max-width: 100%;"
            alt="{{ $selAuthorities['name'] ?? ''}}" />
    </div>
</div>
<div style="position:absolute; z-index: 1; top:125px; right:-5px;">
    <img src="{{ $statusIcon }}" style="width: 50px" />
</div>
