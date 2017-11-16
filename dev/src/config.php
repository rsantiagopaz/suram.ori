<?php

$aux = new mysqli_driver;
$aux->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

/*
   $SERVIDOR = "localhost";
   $USUARIO  = "root";
   $PASSWORD = "";
   $BASE     = "suram";
   $mysqli = new mysqli($SERVIDOR, $USUARIO, $PASSWORD, $BASE);
   $mysqli->query("SET NAMES 'utf8'");
   $GLOBALS["mysqli"] = $mysqli;
   
   $salud = "salud1";
   $suram = "suram";
*/
   
   
   
   $SERVIDOR = "localhost";
   $USUARIO  = "root";
   $PASSWORD = "";
   $BASE     = "ramon_prueba_suram";
   $mysqli = new mysqli($SERVIDOR, $USUARIO, $PASSWORD, $BASE);
   $mysqli->query("SET NAMES 'utf8'");
   $GLOBALS["mysqli"] = $mysqli;
   
   $salud = "ramon_prueba_suram";
   $suram = "ramon_prueba_suram";
?>