<? 
// ------------ CONTROL DE ACCESO -----------------
$SYSsistema_id='000';
$SYSpathraiz='';
 while (!file_exists($SYSpathraiz.'_raiz.php'))	
  {
   $SYSpathraiz='../'.$SYSpathraiz;
  }
$SYSpatharchivo='http://'.$HTTP_SERVER_VARS["HTTP_HOST"].$HTTP_SERVER_VARS["REQUEST_URI"];

include($SYSpathraiz.'_scripts/_controlacceso.php'); 
// ------------ FIN CONTROL DE ACCESO -----------------
?>


<html><title>CONSULTA DE AREAS DE ORGANISMOS < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < </title>
<body bgcolor="#F7F7F7" leftmargin="0" topmargin="0">


<p> 
  <?



// CONEXION A LA BASE DE DATOS
include($SYSpathraiz.'config.php');
// FIN: CONEXION A LA BASE DE DATOS



// *********************** I N I C I O ***********************************************


// DATOS DE ENTRADA $  = $_REQUEST[""]; -----------------
$organismo_id                = $_REQUEST["organismo_id"];
$_variable_organismo_area_id = $_REQUEST["_variable_organismo_area_id"];
$_variable_organismo_area    = $_REQUEST["_variable_organismo_area"];
$_area_a_buscar              = trim($_REQUEST["_area_a_buscar"]);
// ------------------------------------------------------


?>
  <script>
function escogeArea()
 {
   opener.document.f1.<?php print $_variable_organismo_area_id; ?>.value=_organismo_area_id.value; 
   opener.document.f1.<?php print $_variable_organismo_area; ?>.value=_organismo_area_id.options[_organismo_area_id.selectedIndex].text;   
   window.close();
 }
</script>
  <?php

// Lista las areas del organismo organismo_id y que respondan a la consulta _area_a_buscar
$result=mysql_query("SELECT * FROM _organismos_areas  
                               JOIN _organismos
							     ON _organismos.organismo_id = _organismos_areas.organismo_id                            
                             WHERE _organismos_areas.organismo_id='$organismo_id' 
							       AND 
							       _organismos_areas.organismo_area LIKE '%$_area_a_buscar%'
						     ORDER BY _organismos_areas.organismo_area		   
							  "); 
$total=mysql_numrows($result);


if (mysql_errno()>0)
 {
  print '<br>Error: '.mysql_errno().": ".mysql_error()."<BR><BR>";
 }


print '<br><center><font color="#FF0000" size="2" face="Arial">';
if ($total>0)
 if ($total==1) 
   print '<b>'.$total.'</b> area ';
  else
   print '<b>'.$total.'</b> areas';
 print ' con <b>"'.$_area_a_buscar.'"</b></font></center>';
if ($row=mysql_fetch_array($result) )
 {
  print '<center>';
  print '<select name="_organismo_area_id" size="10" onblur="escogeArea();">';

  $contador=0;
  do
  {
    $contador++;
    print '<option value="'.$row["organismo_area_id"].'"';
	if ($contador==1)
	 print ' selected ';
	 
//	print " onselected=alert(_organismo_area_id.value); ";
	print '>'.$row["organismo_area"].'</option>';
  }
 while ($row=mysql_fetch_array($result));
 
 print '</select>';
 print '<script>_organismo_area_id.focus();</script>';

 print '<br><font face="arial" size="2">(Pulse TABULADOR para aceptar el area seleccionada)</font>'; 

 // Boton aceptar 
 print '<br>'; 
 print '<input type="button" value="Seleccionar Area" onclick="escogeArea();">';   
 print '</center>';

 }
 else
 {
  print '<br><br><center><font face="arial" size="2" color="#FF0000"><b>¡ No se encontraron areas que respondan a la consulta !</b></font></center><br><br>';
  print '<center>';
  print '<input name="Bot&oacute;n" id="botonCerrarVentana" type="button" style="font-family: Arial; font-size: 8 pt; color: #FFFFFF; background-color: #003399" value="Cerrar ventana" onClick="window.close()" >';
  print '<center>';
  print '
         <script>
		  botonCerrarVentana.focus();
		 </script> 
		';
 }

 



if (!empty($_mensaje))
 {
  print '<center><font face="arial" size="2" color="#FF0000">';
  print $_mensaje;
  print '</font></center>';

 }

?>
</p>
</body>
</html>