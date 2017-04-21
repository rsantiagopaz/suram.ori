<?php
include("../control_acceso_flex.php");
include($SYSpathraiz.'phpqrcode/phpqrcode.php');

$texto = $_GET['texto'];

QRcode::png($texto);
?>
