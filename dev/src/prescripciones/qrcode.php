<?php
include("../control_acceso_flex.php");
include($SYSpathraiz.'phpqrcode/phpqrcode.php');

$texto = "aa";

QRcode::png($texto,'codigo.png');
?>
