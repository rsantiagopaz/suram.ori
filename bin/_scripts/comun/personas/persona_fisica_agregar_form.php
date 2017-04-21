<?php

$SYSsistema_id='006';
$SYSpathraiz='';
 while (!file_exists($SYSpathraiz.'_raiz.php'))	
  {
   $SYSpathraiz='../'.$SYSpathraiz;
  }
$SYSpatharchivo='http://'.$HTTP_SERVER_VARS["HTTP_HOST"].$HTTP_SERVER_VARS["REQUEST_URI"];
include($SYSpathraiz.'_scripts/_controlacceso.php'); 



// PARAMETROS DE ENTRADA
$_variable_dni = $_REQUEST["_variable_dni"];


// -----------------------

if (empty($accion))
 $accion='AGREGAR';

if (!empty($denuncia_id))
 {
  $accion="MODIFICAR";}

// Parámetros de entrada

if (isset($_REQUEST["SYSpatharchivo"]))
  $SYSpatharchivo = $_REQUEST["SYSpatharchivo"];
 else
  $SYSpatharchivo = '';

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>ALTA DE PERSONA FISICA</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<table border="0" align="center" cellpadding="4" cellspacing="0" bgcolor="#000080">
  <tr>
    <td> <p align="center"><font face="Arial" color="#FFFFFF"> <b> AGREGAR PERSONA 
        FISICA </b></font></td>
  </tr>
  <tr> 
    <td width="100%"> <div align="center"> 
        <table border="0" cellpadding="4" cellspacing="0" width="100%">
          <tr> 
            <td width="100%" bgcolor="#AECBEA"> 

			<? include ("persona_fisica_agregar_form_formato.php"); ?>
			
			  </td>
          </tr>
        </table>
      </div></td>
  </tr>
</table>
<font color="#000080" size="2" face="Arial"> </font> 
</body>
</html>

