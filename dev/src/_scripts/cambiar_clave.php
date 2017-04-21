<?
 session_start();
 //print 'Sesion: '.$HTTP_SESSION_VARS['_sessionid']; 
 $_sessionid = $HTTP_SESSION_VARS['_sessionid'];
?>

<HTML>


<title>CAMBIO DE CONTRASE&Ntilde;A</title>

<BODY>
<table border="0" align="center" cellpadding="4" cellspacing="0" bgcolor="#000080">
  <tr> 
    <td> <p align="center"><font face="Arial" color="#FFFFFF"> <b> CAMBIO DE CONTRASE&Ntilde;A</b></font></td>
  </tr>
  <tr> 
    <td width="100%"> <div align="center"> 
        <table border="0" cellpadding="4" cellspacing="0" width="100%">
          <tr> 
            <td width="100%" bgcolor="#AECBEA"> 
<? 

// --------------------------------------------
$SYSusuario         = $_REQUEST["SYSusuario"];
$SYSpassword        = $_REQUEST["SYSpassword"];
$SYSpassword_nuevo1 = $_REQUEST["SYSpassword_nuevo1"];
$SYSpassword_nuevo2 = $_REQUEST["SYSpassword_nuevo2"];
$SYSpathraiz        = $_REQUEST["SYSpathraiz"];
$SYSpatharchivo     = $_REQUEST["SYSpatharchivo"];
// --------------------------------------------


include('../config.php');

$mensaje='';

if (empty($SYSusuario))
 $mensaje.='<br>No ingresó su nombre de usuario.';

if (empty($SYSpassword))
 $mensaje.='<br>No ingresó su password.';

// Verifico si el suario es válido
if (!empty($SYSusuario) AND !empty($SYSpassword))
 {
  // Modificado el 3/5/2007 para uso de MD5
  $result=mysql_query("SELECT * FROM _usuarios WHERE SYSusuario = BINARY '$SYSusuario' AND SYSpassword = MD5('$SYSpassword')");
  $num=mysql_numrows($result);
  if ($num==1)
	   {
	    if ($SYSpassword_nuevo1==$SYSpassword_nuevo2)
		  {
	        if (strlen($SYSpassword_nuevo1)>20 OR strlen($SYSpassword_nuevo1)<5)
			  {
			   $mensaje.='<br>La contraseña nueva no debe exceder los 20 caracteres ni debe ser menor que 5 caracteres.';   
			  }
			 else
			  {
			   $CaracteresNoValidos="&/*!¡¿?+[]()%·$\=@'".'"';
               // VERIFICO CARACTERES NO VALIDOS EN LA CONTRASEÑA
               for ($i = 0; $i < (strlen($CaracteresNoValidos)); $i++)
                {
	             // Busco en el campo a validar si existe un caracter no válido
                 if(strstr($SYSpassword_nuevo1,$CaracteresNoValidos[$i]))
                     $mensaje.='<br>No se permitre el caracter " '.$CaracteresNoValidos[$i].' " en la contraseña.';
                 }
              // Fin VERIFICO CARACTERES NO VALIDOS 
   

			  }
		  }
		 else
		  {
		   $mensaje.='<br>No hay coincidencia en la confirmación de la contraseña nueva.';
		  }  	
	   }
	 else
	   {
		 $mensaje.='Sus datos de acceso no son válidos. Verifique haber ingresado bien su nombre de usuario y contraseña.';
	   }
 }


 if (!empty($mensaje))
  {
		print '<center>';
		print '<font face="arial" size="2" color="#000080">';
		print '<B>¡ ERROR !</B>';
		print '</font>';
		print '</center>';
		print '<br>';
		print '<font face="arial" size="2" color="#000080">';
		print $mensaje;
		print '<br><br>';
		print '<a href="javascript:history.back();">De un click aquí para Reintentar...</a>';
		print '</font>';
		print '<br><br><br>';

	}
 else
  {
/* Antes del 3/5/2007
	mysql_query ("UPDATE _usuarios SET 
	        SYSpassword='$SYSpassword_nuevo1'
           WHERE SYSusuario='$SYSusuario'"); 
*/
 // Desde el 3/5/2007
	mysql_query ("UPDATE _usuarios SET 
	        SYSpassword=MD5('$SYSpassword_nuevo1') 
           WHERE SYSusuario= BINARY '$SYSusuario'"); 
 // ---------------------------------------------		   
   if (mysql_errno()>0)	
    {
     print '<br>Error: '.mysql_errno().": ".mysql_error()."<BR><BR>";	 
	} 
    else     
    {
     print '<center><br><br>';
	 print '<font face="arial" size="2" color="#000080">';
     print '<b>¡ CAMBIO DE CONTRASEÑA EXITOSO !</b>';
	 print '</font>';
	 print '</center>';
	 print '<br><br><br>';
	 if (!empty($SYSpatharchivo))
	  {
	   print '<a href="'.$SYSpatharchivo.'"><font face="arial" size="2">De un click aquí para continuar !</font></a>';
	   print '<br><br>';
	  } 
	}
  }

 
 
 ?>
              <br>
            </td>
          </tr>
        </table>
      </div></td>
  </tr>
</table>


</BODY>
</HTML>