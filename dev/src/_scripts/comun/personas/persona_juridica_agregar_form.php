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


if (isset($_REQUEST["denuncia_id"]))
  $numero = $_REQUEST["denuncia_id"];
 else
  $numero = '';
if (isset($_REQUEST["accion"]))  
  $accion = $_REQUEST["accion"];
 else
  $accion = 'AGREGAR';
  
// -----------------------

if (empty($accion))
 $accion='AGREGAR';

if (!empty($denuncia_id))
 {
  $accion="MODIFICAR";}
// Es una modificación. 

// Crea sesion para el usuario 
/*
      $terminar="NO"; 
      while ($terminar=="NO") 
      {
       $longitud=50;
       $codigo=""; 
       $key="ABCDEFGHIJKLMNOPQRSTUWXYZabcdefghijklmnopqrstuvwxyz";
       $key.="1234567890";
       for ($index=0; $index<$longitud;$index++)
        {  
         $codigo.=substr($key,(rand() % (strlen($key))), 1);
        }

       // Veo si la sesion existe en tabla "_sesiones"
	   $HTTP_SESSION_VARS['_sessionid']=$codigo;
	   $_sessionid=$HTTP_SESSION_VARS['_sessionid'];
	   //print 'Sesion: '.$_sessionid;
       $result=mysql_query("SELECT _sessionid FROM _sesiones WHERE _sessionid = '$_sessionid' ");
       $num=mysql_numrows($result);
       if ($num<=0)
         $terminar="SI";
       // Fin veo
      }      

// Fin Creacion de id de sesión
*/

/*

// Destruyo la sesión

session_start();

$_anterior=session_id();

session_destroy();

session_start();

$_actual=session_id();

print $_anterior;

print '<br>';

print $_actual;

//

*/





// Parámetros de entrada

if (isset($_REQUEST["SYSpatharchivo"]))
  $SYSpatharchivo = $_REQUEST["SYSpatharchivo"];
 else
  $SYSpatharchivo = '';

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>ALTA DE PERSONA JURIDICA</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<table border="0" align="center" cellpadding="4" cellspacing="0" bgcolor="#000080">
  <tr>
    <td> <p align="center"><font face="Arial" color="#FFFFFF"> <b> AGREGAR PERSONA 
        JURIDICA </b></font></td>
  </tr>
  <tr> 
    <td width="100%"> <div align="center"> 
        <table border="0" cellpadding="4" cellspacing="0" width="100%">
          <tr> 
            <td width="100%" bgcolor="#AECBEA"> 

			<? include ("persona_juridica_agregar_form_formato.php"); ?>
			
			  </td>
          </tr>
        </table>
      </div></td>
  </tr>
</table>
<font color="#000080" size="2" face="Arial"> </font> 
</body>
</html>

