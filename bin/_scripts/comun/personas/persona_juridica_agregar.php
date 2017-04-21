<?
$SYSsistema_id='006';
$SYSpathraiz='';
 while (!file_exists($SYSpathraiz.'_raiz.php'))	
  {
   $SYSpathraiz='../'.$SYSpathraiz;
  }
$SYSpatharchivo='http://'.$HTTP_SERVER_VARS["HTTP_HOST"].$HTTP_SERVER_VARS["REQUEST_URI"];

include($SYSpathraiz.'_scripts/_controlacceso.php'); 

include('../../../config.php');

?>

<HTML><BODY>
<title>ALTA DE PERSONA JURIDICA</title>



<table border="0" align="center" cellpadding="4" cellspacing="0" bgcolor="#000080">
  <tr> 
    <td> <p align="center"><font face="Arial" color="#FFFFFF"> <b> AGREGAR PERSONA</b></font></td>
  </tr>
  <tr> 
    <td width="100%"> <div align="center"> 
        <table border="0" cellpadding="4" cellspacing="0" width="100%">
          <tr> 
            <td width="100%" bgcolor="#AECBEA"> 
<? 
// --------------------------------------------
$_variable_dni = $_REQUEST["_variable_dni"];


$persona_nombre     = $_REQUEST["persona_nombre"];
$persona_domicilio  = $_REQUEST["persona_domicilio"];
$persona_tipo       = $_REQUEST["persona_tipo"];
//$persona_dni        = $_REQUEST["persona_dni"];
//$persona_cuil       = $_REQUEST["persona_cuil"];
$persona_cuit       = $_REQUEST["persona_cuit"];
//$persona_sexo       = $_REQUEST["persona_sexo"];
//$persona_nacionalidad = $_REQUEST["persona_nacionalidad"];
//$persona_estado_civil = $_REQUEST["persona_estado_civil"];
$persona_localidad_id = $_REQUEST["persona_localidad_id"];
// --------------------------------------------



$mensaje='';

if (empty($persona_nombre))
 $mensaje.='<br>No ingresó la RAZÓN SOCIAL.';

if (empty($persona_cuit))
	 $mensaje.='<br>No ingresó el número de CUIT.';
else
	{
	 // Veo si el CUIT existe en _personas
	 $result=mysql_query("SELECT *  FROM _personas 
	 					WHERE persona_cuit = '$persona_cuit'
						");
	 $num=mysql_numrows($result);
	 if ($num>=1)		
	 	  {
		  $persona_nombre  = mysql_result($result,0,persona_nombre);    		  	
		  $mensaje.= '<br>Ya se dió de alta a ' . $persona_nombre. ' con CUIT ' . $persona_cuit;
		  }
	}

//--------TIPO DE PERSONA
if ($persona_tipo!="J")
	 $mensaje.='<br>Está usando un formulario para dar de alta un PERSONA JURÍDICA!.';

//----------LOCALIDAD
if ($persona_localidad_id=="")
	 $mensaje.='<br>Indique en qué Localidad reside la persona.';



 if (!empty($mensaje))
  {  
		
		print '<font face="arial" size="3" color="#FF0000"><b>ERROR(ES):</b></font><BR>';
		print '<font face="arial" size="2" color="#FF0000">';
		print $mensaje;
//		print '<br><br>';
//		print '<a href="javascript:history.back();">De un click aquí para Reintentar...</a>';
		print '</font>';
		print '<br>';
		include ("persona_juridica_agregar_form_formato.php"); 
	}
 else
  {
  // Crea id --------------------------------------------
      $_terminar="NO"; 
      while ($_terminar=="NO") 
      {
       $_longitud=5;
       $_id=""; 
       $_key="ABCDEFGHIJKLMNOPQRSTUWXYZ";
       $_key.="1234567890";
       for ($_index=0; $_index<$_longitud;$_index++)
        {  
         $_id.=substr($_key,(rand() % (strlen($_key))), 1);
        }
       // Veo si el id existe en tabla
       $result=mysql_query("SELECT persona_id FROM _personas WHERE persona_id = '$_id'");
       $num=mysql_numrows($result);
       if ($num<=0)
         $_terminar="SI";
       // Fin veo
      }      
	  $persona_id = $_id;	  

      // Fin: crea id ---------------------------------------
	  include($SYSpathraiz.'_scripts/fechayhora.inc'); 
	  $fecha = $_fechaactual;
	  $hora  = $_horaactual;

      mysql_query ("INSERT INTO _personas
            (
			persona_id,
    	     persona_tipo,
		     persona_dni,
		     persona_cuil,
		     persona_cuit,			 
		     persona_nombre,
		     persona_domicilio,			 
		     persona_sexo,
		     persona_nacionalidad,
		     persona_estadocivil,			 
		     persona_instruccion,			 
		     persona_clase,
			 localidad_id,
			 SYSusuario,
			 SYSusuario_carga_fecha,
			 SYSusuario_carga_hora   			 			 
		    ) 
           VALUES 
           (
			 '$persona_id',
    	     '$persona_tipo',
		     '',
			 '', 
			 '$persona_cuit', 
			 '$persona_nombre', 
			 '$persona_domicilio', 
			 '', 
			 '', 
			 '', 
			 '',
			  '',			 
			  '$persona_localidad_id',
			  '$SYSusuario',
			  '$fecha',
			  '$hora'
			  )"); 


   if (mysql_errno()>0)	
    {
     print '<br>Error: '.mysql_errno().": ".mysql_error()."<BR><BR>";	 
	} 
    else     
    {
     print '<center><br><br>';
	 print '<font face="arial" size="2">';
     print '<b>¡ SE AGREGÓ LA PERSONA CON EXITO !</b><br>';
	 print '<br><b>Razón Social:</b>'. $persona_nombre;
 	 print '<br><b>CUIT:</b>'. $persona_cuit;
     print '<br><br><input type="button" value="Copiar y pegar DNI" onclick="opener.document.f1.'.$_variable_dni.'.value='.$persona_cuit.'; window.close();">';
	 print '</font>';
	 print '</center>';
	 print '<br><br><br>';
	 if (!empty($SYSpatharchivo))
	  {
	   //print '<a href="'.$SYSpatharchivo.'"><font face="arial" size="2">De un click aquí para continuar !</font></a>';
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
<center><a href="javascript:window.close();">Cerrar esta ventana</a>
</center>

</BODY></HTML>