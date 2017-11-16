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


<html><title>CONSULTA DE LOCALIDADES < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < </title>
<body bgcolor="#F7F7F7" leftmargin="0" topmargin="0">
<p><br>
  <?



// CONEXION A LA BASE DE DATOS
include($SYSpathraiz.'config.php');
// FIN: CONEXION A LA BASE DE DATOS



// *********************** I N I C I O ***********************************************


// DATOS DE ENTRADA $  = $_REQUEST[""]; ----------------------------------------
$desde                  = $_REQUEST["desde"];
$_accion                = $_REQUEST["_accion"];
$_tipoconsulta          = $_REQUEST["_tipoconsulta"];
$localidad_nombre       = strtoupper ($_REQUEST["localidad_nombre"]);
// Variables donde poner el resultado
$_variable_localidad    = $_REQUEST["_variable_localidad"];
$_variable_localidad_id = $_REQUEST["_variable_localidad_id"];

// -----------------------------------------------------------------------------


// Opciones de navegaci�n.

if (empty($desde))
 $desde=0;

//if (empty($maxmostrar))
 $maxmostrar=20;

// fin de opciones de navegaci�n



// Determino tipo de consulta ---------------------------------------------

$subtitulo=''; 
$localidad_nombre = trim($localidad_nombre);
if (empty($_tipoconsulta) OR empty($localidad_nombre) OR strlen($localidad_nombre)<=2 )
 {
  $_tipoconsulta=0;
 }
  
switch($_tipoconsulta)
 {
  case '1':
   // Lista aquellas localidades que en su nombre est� el texto de $localidad_nombre
   $result=$mysqli->query("SELECT localidad_id FROM _localidades WHERE localidad LIKE '%$localidad_nombre%'"); 
   $total=$result->num_rows;
   $result=$mysqli->query("SELECT * FROM _localidades
                            LEFT JOIN _departamentos
							       ON _departamentos.departamento_id = _localidades.departamento_id 
							    WHERE localidad LIKE '%$localidad_nombre%'
				             ORDER BY localidad
						        LIMIT $desde,$maxmostrar");
   $subtitulo='(Listando: '.$localidad_nombre.')';
   $AuxLink = '&_tipoconsulta='.$_tipoconsulta.'&_variable_localidad_id='.$_variable_localidad_id.'&_variable_localidad='.$_variable_localidad.'&localidad_nombre='.$localidad_nombre;
   break;

 }

if ($mysqli->errno>0)
 {
  print '<br>Error: '.$mysqli->errno.": ".$mysqli->error."<BR><BR>";
 }
// Fin: Determino tipo de consulta ------------------------------------------------




if ($_tipoconsulta!=0)
{


// Encabezado general
print '<center><font color="#000080" size="3" face="Arial"><strong><u>';
print 'LISTADO DE LOCALIDADES</u></strong></font></center>';

if (!empty($subtitulo))
 {
  print '<center><font color="#000080" size="2" face="Arial"><strong>';
  print $subtitulo;
  print '</strong></font></center>';
 }  
// Fin.

//**************************************************


if ($total>0)
 if ($total==1) 
   print '<br><center><font color="#FF0000" size="2" face="Arial"><b>'.$total.'</b> localidad</font></center>';
  else
   print '<br><center><font color="#FF0000" size="2" face="Arial"><b>'.$total.'</b> localidades</font></center>';

if ($row=$result->fetch_array() )
 {

   $contador=0;

   // Bandera determina si es la primera vez que entra al ciclo
   $AUXbandera=0;

   //Imprimo encabezado de tabla 
   print '<center>';
   print '<table border="0" cellpadding="2">';
   print '<tr>';

   print ' <td bgcolor="#0099cc" align="center"><b><font face="Arial" size="2" color="#FFFFFF">LOCALIDAD</font></b></td>';
   print ' <td bgcolor="#0099cc" align="center"><b><font face="Arial" size="2" color="#FFFFFF">DEPARTAMENTO</font></b></td>';   

   // Imprimo encabezado de links de "Modificar" y "Quitar"
   if ($_accion=="MQ")
       {
        print ' <td></td>';
		$AuxLink='&_accion=MQ';
	   }	

   print ' </tr>';
  
   
  do
  {
   $contador++;
   

  
   $division=$contador/2;
   if( $division == (int) $division )
       $colorfondofila='#D0E8FF';
     else
       $colorfondofila='#D0E0F2';

   print '<tr>';
   
   print '<td bgcolor="'.$colorfondofila.'" align="left" valign="top"><font face="Arial" size="2">'.ereg_replace($localidad_nombre, "<b>".$localidad_nombre."</b>", $row["localidad"]).'</font></td>';
   print '<td bgcolor="'.$colorfondofila.'" align="left" valign="top"><font face="Arial" size="2">'.$row["departamento"].'</font></td>';

   print '<td bgcolor="'.$colorfondofila.'" align="left" valign="top">';
   print '<input type="button" value="<--" onclick="opener.document.f1.'.$_variable_localidad.'.value='."'".$row["localidad"].' ('.$row["departamento"].')'."'".'; opener.document.f1.'.$_variable_localidad_id.'.value='."'".$row["localidad_id"]."'".'; window.close();">';   
   print '</td>';   

   print '</tr>';

  }
 while ($row=$result->fetch_array());
 print '</table>';
 print '</center>';



 // Botones de navegaci�n.

 print '<center>';

 // Primero
 if ($desde>0)
  print '<b>[ <a href="localidad_consultar.php?desde=0&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">Primera</font></a> ] </b>';

 // Anterior
 if ($desde>0)
   print '<b>[ <a href="localidad_consultar.php?desde='.($desde-$maxmostrar).'&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">Anterior</font></a> ] </b>';

 // Siguiente
 if (($desde+$maxmostrar)<$total)
  {
   print '<b>[ <a href="localidad_consultar.php?desde='.($desde+$maxmostrar).'&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">Siguiente</font></a> ] </b>';
  }

 // P�ginas
 $linkpaginas="";
 $pagina=0;
 for ($i=1;$i<=$total;$i=$i+$maxmostrar) 
  {
   $pagina++;

   if ($pagina>1)
     $linkpaginas=$linkpaginas.', ';

   if (($i-1)==$desde)
     $linkpaginas=$linkpaginas.'<b>'.$pagina.'</b>';
    else 
     $linkpaginas=$linkpaginas.'<a href="localidad_consultar.php?desde='.($i-1).'&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">'.$pagina.'</font></a>';

  }

 // Ultimo
 if (($desde+$maxmostrar)<$total)
  {
   print '<b>[ <a href="localidad_consultar.php?desde='.(($pagina-1)*$maxmostrar).'&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">Ultima</font></a> ]</b>';
  }
 
 print '</center>';


 print '<center><font color="#800080" face="Arial" size="2">Visualizando de '.$maxmostrar.' en '.$maxmostrar.'</b><br>';
 print '</center>';
 print '<center>Ir a p�gina: '.$linkpaginas.'<br></font></center>';

 print '<br><br>';


 }
 else
 {
  print '<br><br><center><font face="arial" size="2" color="#FF0000"><b>� No se encontraron registros que respondan a la consulta !</b></font></center><br><br>';
 }


} 



?>
</p>
<table border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><form name="form1" method="post" action="localidad_consultar.php">
        <table border="1" align="center" cellpadding="2" cellspacing="0">
          <tr> 
            <td colspan="2" align="left" valign="middle" bgcolor="#003399"> <div align="center"><font color="#FFFFFF" size="1" face="Arial, Helvetica, sans-serif">B&uacute;squeda 
                de Localidad:</font></div></td>
          </tr>
          <tr> 
            <td align="center" valign="top"> <input name="localidad_nombre" type="text" size="30"><BR> <font size="1" face="Arial, Helvetica, sans-serif">(ingrese al menos 5 caracteres) </font></td>
            <td align="center" valign="top"> <input type="submit" name="Submit" style="font-family: Arial; font-size: 8 pt; color: #FFFFFF; background-color: #003399" value="Buscar"> 
              <input name="_variable_localidad_id" type="hidden" id="_variable_localidad_id2" value="<? print $_variable_localidad_id; ?>"> 
              <input name="_variable_localidad" type="hidden" id="_variable_localidad2" value="<? print $_variable_localidad; ?>"> 
              <input name="_tipoconsulta" type="hidden" id="_tipoconsulta2" value="1"> 
              <? 
			     if ($total==0)
				  {
                    print '
					     <script language="">
                          document.form1.localidad_nombre.focus();
                         </script>
						  ';
                  }
			  ?>
            </td>
          </tr>
        </table>
      </form></td>
  </tr>
</table>
<center>
  <input name="Bot&oacute;n" style="font-family: Arial; font-size: 8 pt; color: #FFFFFF; background-color: #003399" type="button" value="Cerrar ventana" onClick="window.close()">
</CENTER>
</body>
</html>