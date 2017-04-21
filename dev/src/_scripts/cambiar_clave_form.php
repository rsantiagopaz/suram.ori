<?php
header("Cache-Control: no-cache, must-revalidate");

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
       $result=mysql_query("SELECT _sessionid FROM _sesiones WHERE _sessionid = '$_sessionid' ");
       $num=mysql_numrows($result);
       if ($num<=0)
         $terminar="SI";
       // Fin veo
      }      
// Fin Creacion de id de sesión



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
<title>CAMBIO DE CONTRASE&Ntilde;A</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<p>&nbsp;</p><table border="0" align="center" cellpadding="4" cellspacing="0" bgcolor="#000080">
  <tr>
    <td> <p align="center"><font face="Arial" color="#FFFFFF"> <b> CAMBIO DE CONTRASE&Ntilde;A</b></font></td>
  </tr>
  <tr> 
    <td width="100%"> <div align="center"> 
        <table border="0" cellpadding="4" cellspacing="0" width="100%">
          <tr> 
            <td width="100%" bgcolor="#AECBEA"> <form action="cambiar_clave.php" method="POST" enctype="multipart/form-data" name="f1" id="f1">
                <div align="center"> 
                  <table border="0" cellpadding="4" cellspacing="0">
                    <tr> 
                      <td align="right" valign="middle"> 
                        <p align="right"><b><font face="Arial" size="2" color="#000080">Usuario:</font></b></p></td>
                      <td valign="top"><font color="#000080" size="2" face="Arial"> 
                        <input name="SYSusuario" type="text" id="SYSusuario" size="10" maxlength="20">
						<script>
						 document.f1.SYSusuario.focus();
						</script>
                        </font> </td>
                    </tr>
                    <tr> 
                      <td align="right" valign="middle"> 
                        <p align="right"><b><font face="Arial" size="2" color="#000080">Contrase&ntilde;a 
                          Actual:</font></b></p></td>
                      <td valign="top"><font color="#000080" size="2" face="Arial"> 
                        <input name="SYSpassword" type="password" id="SYSpassword" size="10" maxlength="10">
                        </font> </td>
                    </tr>
                    <tr> 
                      <td align="right" valign="middle"> 
                        <p align="right"><b><font face="Arial" size="2" color="#000080">Contrase&ntilde;a 
                          Nueva:</font></b></p></td>
                      <td valign="top"><font color="#000080" size="2" face="Arial"> 
                        <input name="SYSpassword_nuevo1" type="password" id="SYSpassword_nuevo1" size="10" maxlength="10">
                        </font> </td>
                    </tr>
                    <tr> 
                      <td align="right" valign="middle"> 
                        <p align="right"><b><font face="Arial" size="2" color="#000080">Confirme 
                          Contrase&ntilde;a Nueva:</font></b></p></td>
                      <td valign="top"><font color="#000080" size="2" face="Arial"> 
                        <input name="SYSpassword_nuevo2" type="password" id="SYSpassword_nuevo2" size="10" maxlength="10">
                        </font> </td>
                    </tr>
                    <tr> 
                      <td valign="top"></td>
                      <td valign="top"><font color="#000080" size="2" face="Arial"> 
                        <input type="submit" value="Cambiar" name="B1">
                        </font></td>
                    </tr>
                  </table>
                </div>
                <input name="SYSpatharchivo" type="hidden" id="SYSpatharchivo" value="<? print $SYSpatharchivo; ?>">
              </form>

			  </td>
          </tr>
        </table>
      </div></td>
  </tr>
</table>
</body>
</html>
