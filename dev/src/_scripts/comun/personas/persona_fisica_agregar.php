<?php
$SYSsistema_id='006';
$SYSpathraiz='';
 while (!file_exists($SYSpathraiz.'_raiz.php'))	
  {
   $SYSpathraiz='../'.$SYSpathraiz;
  }
$SYSpatharchivo='http://'.$HTTP_SERVER_VARS["HTTP_HOST"].$HTTP_SERVER_VARS["REQUEST_URI"];

include($SYSpathraiz.'_scripts/_controlacceso.php'); 

include($SYSpathraiz.'config.php');

?>


<?php

// --------------------------------------------
$_variable_dni = $_REQUEST["_variable_dni"];


// Agregado 20/8/2008 -------------------
// A: Alta, M: Modificación
if (isset($_REQUEST["_accion"]))
  $_accion = $_REQUEST["_accion"];
 else 
  $_accion = "A";

// Para el caso modificación
if (isset($_REQUEST["persona_id"]))
  $persona_id = $_REQUEST["persona_id"];
 else 
  $persona_id = "";
// --------------------------------------


$persona_nombre        = $_REQUEST["persona_nombre"];
$persona_domicilio     = $_REQUEST["persona_domicilio"];
$persona_tipo          = $_REQUEST["persona_tipo"];
$persona_dni           = $_REQUEST["persona_dni"];
$persona_cuil          = $_REQUEST["persona_cuil"];
//$persona_cuit       = $_REQUEST["persona_cuit"];
$persona_sexo          = $_REQUEST["persona_sexo"];
$persona_nacionalidad  = $_REQUEST["persona_nacionalidad"];
$persona_estado_civil  = $_REQUEST["persona_estado_civil"];
$persona_localidad_id  = $_REQUEST["persona_localidad_id"];
$persona_localidad_txt = $_REQUEST["persona_localidad_txt"];
// --------------------------------------------


?>


<HTML>
<title><?php if($_accion=="A") print "ALTA"; elseif($_accion=="M") print "MODIFICACION"; else print "?"; ?> DE PERSONA FISICA</title>
<BODY>





<table border="0" align="center" cellpadding="4" cellspacing="0" bgcolor="#000080">
  <tr> 
    <td> <p align="center"><font face="Arial" color="#FFFFFF"> <b> <?php if($_accion=="A") print "AGREGANDO"; elseif($_accion=="M") print "MODIFICANDO"; else print "?"; ?> PERSONA</b></font></td>
  </tr>
  <tr> 
    <td width="100%"> <div align="center"> 
        <table border="0" cellpadding="4" cellspacing="0" width="100%">
          <tr> 
            <td width="100%" bgcolor="#AECBEA"> 
<?php 


$mensaje='';


// Agregado el 20/8/08
// Si es una modificación, se debe controlar q sea el mismo usuario q dio el alta
if ($_accion=='M')
 {
  $query = "SELECT * FROM _personas WHERE persona_id = '$persona_id'";
 }
// -------------------

if (empty($persona_nombre))
 $mensaje.='<br>No ingresó su Apellido y Nombre.';

// Valido persona_dni --------------------------------
if (empty($persona_dni))
	 $mensaje.='<br>No ingresó el DNI de la persona.';
  else
   { 
     $AUXpersona_dni=( ((int) $persona_dni) * -1) * -1;
	 if ($AUXpersona_dni != $persona_dni)
	   {
	    $mensaje.="<br>Escribió mal el número de DNI ($persona_dni)<br>-recuerde no usar el punto o coma para separar las unidades de mil-"; 
	   }
	  else
	   { 
	    $persona_dni=$AUXpersona_dni;
        if ( $persona_dni < 1000000 OR $persona_dni > 99999999)
	     {
	      $mensaje.="<br>El DNI informado ($persona_dni) no debe ser inferior a 1.000.000 ni superior a 99.999.999.";
   	     }
		// Agregado el 20/8/08
		if ($_accion=="A") 
		  {
	        $result = mysql_query("SELECT *  FROM _personas 
                           WHERE persona_tipo = 'F' AND persona_dni = '$persona_dni' 
						 ");
		  }
		 elseif($_accion=="M")
		  {
		   //Caso Modificación
	        $result = mysql_query("SELECT *  FROM _personas 
                           WHERE persona_tipo = 'F' AND persona_dni = '$persona_dni' AND persona_id <> '$persona_id' 
						 ");		  
		  } 				 
		// ------  
	    $num=mysql_numrows($result);
	    if ($num>=1)		
	 	  {
		   $AUXpersona_nombre  = mysql_result($result,0,persona_nombre);    		  	
		   $mensaje.= '<br>Ya se dió de alta a ' . $AUXpersona_nombre. ' con DNI ' . $persona_dni;
		  }
	   }	  
   }
// FIN: Valido persona_dni --------------------------------   

  
// Valido persona_cuil ------------------------------------  	 
if (!empty($persona_cuil))
   { 
     $AUXlongitud = strlen($persona_cuil);
     if (strlen($persona_cuil) != 11)
	   {
	    $mensaje.='<br>La cantidad de números en el CUIL es incorrecta (deben ser 11). No escriba puntos (.) ni guiones (-).';
	   }
	  else
	   {
	    // veo que todos los caracteres sean numéricos
		$AUXno=0;
		for ($i=0;$i<$AUXlongitud; $i++)
		 {
		  $AUXdigito = $persona_cuil[$i];
		  //print "$AUXdigito : posicion ".strpos('01234567890',$AUXdigito).'<br>';
		  if ( strrpos('01234567890',$AUXdigito) == '' )
		   {
		    $AUXno=1;
			$i=$AUXlongitud;
		   }
		 }
		if ($AUXno=='1')
	      {
	       $mensaje.="<br>Escribió mal el número de CUIL ($persona_cuil). No escriba puntos (.) ni guiones (-).";
	      }
	     else
	      {
	        $result=mysql_query("SELECT *  FROM _personas 
                           WHERE persona_tipo = 'F' AND persona_cuil = '$persona_cuil' 
						 ");
	        $num=mysql_numrows($result);
	        if ($num>=1)		
	 	      {
		       $AUXpersona_nombre  = mysql_result($result,0,persona_nombre);    		  	
		       $mensaje.= '<br>Ya se dió de alta a ' . $AUXpersona_nombre. ' con CUIL ' . $persona_cuil;
		      }
		  } 
	   } 
   }
// FIN: Valido persona_cuil ------------------------------------  	 


//--------TIPO DE PERSONA
if ($persona_tipo!="F")
	 $mensaje.='<br>Está usando un formulario para dar de alta/modificar una PERSONA FÍSICA!.';

//----------LOCALIDAD
if (empty($persona_localidad_id))
	 $mensaje.='<br>Indique la Localidad en la que reside la persona.';
  else
   {
     $result=mysql_query("SELECT *  FROM _localidades  
                           WHERE localidad_id = '$persona_localidad_id' 
						 ");
	 $num=mysql_numrows($result);
	 if ($num!=1)		
 	   {
	    $mensaje.= "<br>Código de localidad ($persona_localidad_id) inexistente. De un click en el botón rotulado con '?' para acceder a la consulta de localidades por nombre.";
	   }
   }	 



 if (!empty($mensaje))
  {  
		
		print '<font face="arial" size="3" color="#FF0000"><b>ERROR(ES):</b></font><BR>';
		print '<font face="arial" size="2" color="#FF0000">';
		print $mensaje;
//		print '<br><br>';
//		print '<a href="javascript:history.back();">De un click aquí para Reintentar...</a>';
		print '</font>';
		print '<br><br>';
		include ("persona_fisica_agregar_form_formato.php"); 
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
	  if ($_accion == 'A')
	    {
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
		     '$persona_dni',
			 '$persona_cuil', 
			 '', 
			 '$persona_nombre', 
			  '$persona_domicilio', 
			  '$persona_sexo', 
			  '$persona_nacionalidad', 
			  '$persona_estadocivil', 
			  '$persona_instruccion',
			  '2',			 
			  '$persona_localidad_id',
			  '$SYSusuario',
			  '$fecha',
			  '$hora'
			  )"); 
	    }
	  else
	    {
		 // Es una modificación
		
		}	

   if (mysql_errno()>0)	
    {
     print '<br>Error: '.mysql_errno().": ".mysql_error()."<BR><BR>";	 
	} 
    else     
    {
     print '<center><br><br>';
	 print '<font face="arial" size="2">';
     print '<b>¡ SE AGREGÓ LA PERSONA CON EXITO !</b><br>';
	 print '<br><b>Apellido y Nombre:</b>'. $persona_nombre;
 	 print '<br><b>DNI:</b>'. $persona_dni;
     print '<br><input type="button" value="Copiar y pegar DNI" onclick="opener.document.f1.'.$_variable_dni.'.value='.$persona_dni.'; window.close();">';
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

</BODY>
</HTML>