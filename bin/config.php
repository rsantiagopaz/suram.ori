<?php
   $SERVIDOR = "localhost";
   $USUARIO  = "root";
   $PASSWORD = "";
   $BASE     = "suram";
   $link = @mysql_connect($SERVIDOR, $USUARIO, $PASSWORD);
   @mysql_select_db($BASE, $link);
   mysql_query("SET NAMES 'utf8'");
   
   $salud = "salud1";
   $suram = "suram";
?>