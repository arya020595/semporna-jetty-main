<?php
if ($type == "success") {
    $style = "background-color: rgba( 25, 135, 84, 1 );";
} elseif ($type == "danger") {
    $style = "background: red;";
} else {
    $style = "background-color: rgba( 255, 193, 7, 1 );";
}

?>

<span
    style="color:white;
        padding: 3px 8px;
        display: inline-block;
        width: 120px;
        text-align:center;
        font-size: 1rem;
        line-height: 0.9rem;
        {{ $style }} ">
    {{ $text }}
</span>
