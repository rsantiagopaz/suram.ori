<?php
header("Cache-Control: no-cache, must-revalidate");
// Borra sesi�n actual
session_start();
// Agregado el 13/3/2009
session_destroy();
session_start();
// ---------------------
$HTTP_SESSION_VARS['_sessionid']='';
$_sessionid=$HTTP_SESSION_VARS['_sessionid'];
// -------------------

include('../config.php');

// Crea sesion para el usuario 
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
       $result=$mysqli->query("SELECT _sessionid FROM _sesiones WHERE _sessionid = '$_sessionid' ");
       $num=$result->num_rows;
       if ($num<=0)
         $terminar="SI";
       // Fin veo
      }      
// Fin Creacion de id de sesi�n



/*
// Destruyo la sesi�n
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


// Par�metros de entrada
if (isset($_REQUEST["SYSpatharchivo"]))
  $SYSpatharchivo = $_REQUEST["SYSpatharchivo"];
 else
  $SYSpatharchivo = '';

$mensaje = $_REQUEST["mensaje"];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>INGRESO DE DATOS DE ACCESO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<p>&nbsp;</p><table border="0" align="center" cellpadding="4" cellspacing="0" bgcolor="#000080">
  <tr>
    <td> <p align="center"><font face="Arial" color="#FFFFFF"> <b> INGRESE SUS 
        DATOS DE ACCESO</b></font></td>
  </tr>
  <tr> 
    <td width="100%"> <div align="center"> 
        <table border="0" cellpadding="4" cellspacing="0" width="100%">
          <tr> 
            <td width="100%" bgcolor="#AECBEA"> 
			
			
			<?php
			 if (!empty($mensaje))
			   {
			    print '<font face="arial" size="2" color="#000088">';
				print $mensaje;
				print '</font><br><br>';
			   }
			 include('login_form_formato.php');
			?>

			  </td>
          </tr>
        </table>
      </div></td>
  </tr>
</table>
</body>
</html>
