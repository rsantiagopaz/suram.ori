<?
 header('Expires: Wed, 23 Dec 1980 00:30:00 GMT');
 header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
 header('Cache-Control: no-cache, must-revalidate');
 header('Pragma: no-cache');

 // USO DE SESION
 session_start();
 $_sessionid=$HTTP_SESSION_VARS['_sessionid']; 
 session_unregister('_sessionid');
//-varibale de sesion del sistema de Contactos de Martin
 session_unregister('estadomensaje');
 // Agregado el 13/3/2009
 $HTTP_SESSION_VARS['_sessionid'] = '';
 session_destroy();
 session_start();
 // ---------------------
?>

<html>

<script>
function cerrar()
{
 window.close();
}
</script>


<body>


<p>&nbsp;</p>
<table border="0" align="center" cellpadding="4" cellspacing="0" bgcolor="#000080">
  <tr> 
    <td> <p align="center"><font face="Arial" color="#FFFFFF"> <b> CIERRE DE SESION</b></font></td>
  </tr>
  <tr> 
    <td width="100%"> <div align="center"> 
        <table border="0" cellpadding="4" cellspacing="0" width="100%">
          <tr> 
            <td width="100%" align="left" valign="top" bgcolor="#AECBEA"> 

<?

 include('../config.php');
 $result=mysql_query("SELECT * FROM _sesiones WHERE _sessionid='$_sessionid'");
 $num=mysql_numrows($result);
 $mensaje='';
 if ($num<=0)
   {
	$mensaje.='NO SE ENCONTRO SESION: '.$_sessionid;
   }
  else
   {
    $SYSusuario            = mysql_result($result,0,SYSusuario);
	$SYSsesiondetalle      = mysql_result($result,0,SYSsesiondetalle);
	$SYSsesionfecha_cierre = mysql_result($result,0,SYSsesionfecha_cierre);
	$SYSsesionhora_cierre  = mysql_result($result,0,SYSsesionhora_cierre);
	if (empty($SYSusuario))
	   {
		$_acceso='NO';
		$mensaje.='NO SE EN CONTRO EL USUARIO VINCULADO A LA SESION: '.$_sessionid;
       } 
	  else
	   {
	    if (empty($SYSsesionfecha_cierre) OR $SYSsesionfecha_cierre=='0000-00-00')
		  {
		   // Cierro la sesion ya que está abierta
		   include('fechayhora.inc');
           mysql_query ("UPDATE _sesiones SET 
	          SYSsesionfecha_cierre = '$_fechaactual' ,
			  SYSsesionhora_cierre  = '$_horaactual'
              WHERE _sessionid='$_sessionid'"); 
	       if (mysql_errno()>0)	  
		     {
			  $mensaje.='<br>Error MySQL: '.mysql_errno().": ".mysql_error()."<BR><BR>";			 
			 } 
			else 
			 {
		      $mensaje.='<br>¡ SESION CERRADA EXITOSAMENTE. CIERRE TODAS LAS VENTANAS DE SU NAVEGADOR !';
			 }  
		  }
		 else 
		  {
		   // La sesión ya está cerrada
		   $mensaje.='<br>¡ LA SESION YA SE ENCONTRABA CERRADA ! ('.$SYSsesionfecha_cierre.')';
		  }
	   } 
   }

//print $_sessionid;
print '<center>';
print '<font face="Arial" size="2" color="#000080">';
print '<b>';
print $mensaje;
print '</b>';
print '</font>';
print '</center>';
print '<br><br>';

?>

              <div align="left"></div></td>
          </tr>
        </table>
      </div></td>
  </tr>
</table>



</body>
</html>