<?php
 session_start();

 
 //print 'Sesion: '.$HTTP_SESSION_VARS['_sessionid']; 
 
 // Comentado el 13/3/2009:
 //$_sessionid = $HTTP_SESSION_VARS['_sessionid'];
 // -----------------------
 
 $_sessionid = $_SESSION['_sessionid'];
?>


<table border="0" align="center" cellpadding="4" cellspacing="0" bgcolor="#000080">
  <tr> 
    <td> <p align="center"><font face="Arial" color="#FFFFFF"> <b> DATOS DE ACCESO 
        </b></font></td>
  </tr>
  <tr> 
    <td width="100%"> <div align="center"> 
        <table border="0" cellpadding="4" cellspacing="0" width="100%">
          <tr> 
            <td width="100%" bgcolor="#AECBEA"> 
<? 

// --------------------------------------------
$SYSusuario     = $_REQUEST["SYSusuario"];
$SYSpassword    = $_REQUEST["SYSpassword"];
$SYSpathraiz    = $_REQUEST["SYSpathraiz"];
$SYSpatharchivo = $_REQUEST["SYSpatharchivo"];
$mensaje        = $_REQUEST["mensaje"];
// --------------------------------------------


include('../config.php');

$_mensaje='';

if (empty($SYSusuario))
 $_mensaje.='<br>No ingresó su nombre de usuario.';

if (empty($SYSpassword))
 $_mensaje.='<br>No ingresó su contraseña.';

// Verifico si el suario es válido
if (!empty($SYSusuario) AND !empty($SYSpassword))
 {
  // Antes del 3/5/2007: $result=mysql_query("SELECT * FROM _usuarios WHERE SYSusuario= BINARY '$SYSusuario' AND SYSpassword = BINARY '$SYSpassword'");
  // Desde el 3/5/2007
  $result=mysql_query("SELECT * FROM _usuarios WHERE SYSusuario= BINARY '$SYSusuario' AND SYSpassword = MD5('$SYSpassword')");
  // ---------------
  $num=mysql_numrows($result);
  if ($num==1)
	   {
	    $SYSusuarionombre=mysql_result($result,0,SYSusuarionombre);
		// Veo si la sesión existe en tabla _sesiones:
        $result=mysql_query("SELECT * FROM _sesiones WHERE _sessionid='$_sessionid'");
        $num=mysql_numrows($result);
        if ($num>=1)
		  {
		   $_mensaje.='<br>Sesión ya iniciada. <br>Cierre todas las ventanas del navegador, <br>abralo nuevamente e ingrese sus datos de acceso.';
		  }
		 else
		  {
		   include('fechayhora.inc');
		   $SYSsesionfecha = $_fechaactual;
		   $SYSsesionhora  = $_horaactual;
           // Ingreso en tabla _sesiones los campos _sessionid y SYSusuario

		   // Comentado el 13/3/2009
		   $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
		   // ----------------------
		   
		   // Agregado el 13/3/2009
		   if( empty($_SERVER['HTTP_X_FORWARDED_FOR']) )
		      {
			   $ip = $_SERVER['REMOTE_ADDR'];
			  }
			 else
			  {
			    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			  } 
		   // ---------------------
		   
		   mysql_query ("INSERT INTO _sesiones 
           (
	         _sessionid,
		     SYSusuario,
			 SYSsesionfecha,
			 SYSsesionhora,
			 SYSsesiondetalle,
			 ip
		     )  
           VALUES 
            (
    		 '$_sessionid',	  
		     '$SYSusuario',
			 '$SYSsesionfecha',
			 '$SYSsesionhora',
			 '$SYSsesiondetalle',
			 '$ip'
		    )");
         // Verifico si se grabaron los datos
         $result=mysql_query("SELECT * FROM _sesiones WHERE _sessionid='$_sessionid' AND SYSusuario='$SYSusuario'");
         $num=mysql_numrows($result);
         if ($num<=0)
					 $_mensaje.='<br>No se pudo grabar la sesión y el usuario en tabla _sesiones. <br>Contáctese con el administrador del sistema.';
	      }
		 }
	 else
		{
		 $_mensaje.='<br>Sus datos de acceso no son válidos. <br>Verifique haber ingresado bien su nombre de usuario y contraseña, <b>respetando mayúsculas y minúsculas</b>.<br>(Ej, si su nombre de usuario es <b>juanperez</b>, no intente escribir <b>JuanPerez</b> ni <b>JUANPEREZ</b>, u otra forma)';
	  }
 }


 if (!empty($_mensaje))
  {
		print '<font face="arial" size="2" color="#000080">';
		print '<B>¡ ERROR !</B><br>';
		print '</font>';
		print '<font face="arial" size="2" color="#000080">';
		print $_mensaje;
		print '</font>';
		print '<br><br>';
        include('login_form_formato.php');
	}
 else
  {
    print '<center>';
	print '<font face="arial" color= "#000088" size="3">';
    print 'Bienvenido <b>'.$SYSusuarionombre.'</b>';
	print '</font>';
	print '</center>';
	print '<br><br>';
	print '<font face="arial" color= "#000088" size="2">';
	print 'Puede comenzar a trabajar. <br>Recuerde CERRAR SESION cuando termine o si desea cambiar de usuario. <BR><BR><b>¡ NUNCA DEJE EL NAVEGADOR ABIERTO Y SE RETIRE !</b>';
    print '</font>';
	print '<br><br><br>';
	//print '<a href="../'.$SYSpatharchivo.'">De un click aquí para continuar !</a>';
	print '<a href="'.$SYSpatharchivo.'"><font face="arial" size="2">De un click aqu&iacute; para continuar !</font></a>';
	print '<br><br>';
	
  }

 
 
 ?>
              <br>
            </td>
          </tr>
        </table>
      </div></td>
  </tr>
</table>
