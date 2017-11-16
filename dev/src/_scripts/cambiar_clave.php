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
 $mensaje.='<br>No ingres� su nombre de usuario.';

if (empty($SYSpassword))
 $mensaje.='<br>No ingres� su password.';

// Verifico si el suario es v�lido
if (!empty($SYSusuario) AND !empty($SYSpassword))
 {
  // Modificado el 3/5/2007 para uso de MD5
  $result=$mysqli->query("SELECT * FROM _usuarios WHERE SYSusuario = BINARY '$SYSusuario' AND SYSpassword = MD5('$SYSpassword')");
  $num=$result->num_rows;
  if ($num==1)
	   {
	    if ($SYSpassword_nuevo1==$SYSpassword_nuevo2)
		  {
	        if (strlen($SYSpassword_nuevo1)>20 OR strlen($SYSpassword_nuevo1)<5)
			  {
			   $mensaje.='<br>La contrase�a nueva no debe exceder los 20 caracteres ni debe ser menor que 5 caracteres.';   
			  }
			 else
			  {
			   $CaracteresNoValidos="&/*!��?+[]()%�$\=@'".'"';
               // VERIFICO CARACTERES NO VALIDOS EN LA CONTRASE�A
               for ($i = 0; $i < (strlen($CaracteresNoValidos)); $i++)
                {
	             // Busco en el campo a validar si existe un caracter no v�lido
                 if(strstr($SYSpassword_nuevo1,$CaracteresNoValidos[$i]))
                     $mensaje.='<br>No se permitre el caracter " '.$CaracteresNoValidos[$i].' " en la contrase�a.';
                 }
              // Fin VERIFICO CARACTERES NO VALIDOS 
   

			  }
		  }
		 else
		  {
		   $mensaje.='<br>No hay coincidencia en la confirmaci�n de la contrase�a nueva.';
		  }  	
	   }
	 else
	   {
		 $mensaje.='Sus datos de acceso no son v�lidos. Verifique haber ingresado bien su nombre de usuario y contrase�a.';
	   }
 }


 if (!empty($mensaje))
  {
		print '<center>';
		print '<font face="arial" size="2" color="#000080">';
		print '<B>� ERROR !</B>';
		print '</font>';
		print '</center>';
		print '<br>';
		print '<font face="arial" size="2" color="#000080">';
		print $mensaje;
		print '<br><br>';
		print '<a href="javascript:history.back();">De un click aqu� para Reintentar...</a>';
		print '</font>';
		print '<br><br><br>';

	}
 else
  {
/* Antes del 3/5/2007
	$mysqli->query ("UPDATE _usuarios SET 
	        SYSpassword='$SYSpassword_nuevo1'
           WHERE SYSusuario='$SYSusuario'"); 
*/
 // Desde el 3/5/2007
	$mysqli->query ("UPDATE _usuarios SET 
	        SYSpassword=MD5('$SYSpassword_nuevo1') 
           WHERE SYSusuario= BINARY '$SYSusuario'"); 
 // ---------------------------------------------		   
   if ($mysqli->errno>0)	
    {
     print '<br>Error: '.$mysqli->errno.": ".$mysqli->error."<BR><BR>";	 
	} 
    else     
    {
     print '<center><br><br>';
	 print '<font face="arial" size="2" color="#000080">';
     print '<b>� CAMBIO DE CONTRASE�A EXITOSO !</b>';
	 print '</font>';
	 print '</center>';
	 print '<br><br><br>';
	 if (!empty($SYSpatharchivo))
	  {
	   print '<a href="'.$SYSpatharchivo.'"><font face="arial" size="2">De un click aqu� para continuar !</font></a>';
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