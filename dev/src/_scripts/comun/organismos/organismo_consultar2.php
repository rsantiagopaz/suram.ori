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


<html><title>CONSULTA DE ORGANISMOS < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < </title>
<body bgcolor="#F7F7F7" leftmargin="0" topmargin="0">
<p> 

  <?



// CONEXION A LA BASE DE DATOS
include($SYSpathraiz.'config.php');
// FIN: CONEXION A LA BASE DE DATOS



// *********************** I N I C I O ***********************************************


// DATOS DE ENTRADA $  = $_REQUEST[""]; -----------------
$desde                         = $_REQUEST["desde"];
$_tipoconsulta                 = $_REQUEST["_tipoconsulta"];
$organismo_tipo                = $_REQUEST["organismo_tipo"];
$organismo                     = trim(strtoupper($_REQUEST["organismo"]));
$_variable_organismo_id        = $_REQUEST["_variable_organismo_id"];
$_variable_organismo           = $_REQUEST["_variable_organismo"];
$organismo_id                  = $_REQUEST["organismo_id"];
$organismo_txt                 = $_REQUEST["organismo_txt"];
$_variable_donde_va_el_focus   = $_REQUEST["_variable_donde_va_el_focus"]; 
$_variable_donde_van_las_areas = $_REQUEST["_variable_donde_van_las_areas"];


if (empty($organismo))
  {
   $organismo = $organismo_txt;
  }  
 else
  {
   $organismo_txt = $organismo;
  }
 

if (!empty($organismo_txt) AND empty($organismo_id))
  {
   $_tipoconsulta=1;
  }
 else
   if (!empty($organismo_id) AND empty($organismo_txt))
     {
      $_tipoconsulta=2;
     }
    else	
	 {
	  $_tipoconsulta=0;
	 }

/*
print "Organismo_id  = $organismo_id<br>";
print "Organismo_txt = $organismo_txt<br>";
print "Organismo     = $organismo<br>";
print "Organismo_tipo= $organismo_tipo<br>";
print "Variable focus = $_variable_donde_va_el_focus";
print "Variable areas = $_variable_donde_van_las_areas";
*/




// ------------------------------------------------------


// Opciones de navegaci�n.

if (empty($desde))
 $desde=0;

//if (empty($maxmostrar))
 $maxmostrar=20;

// fin de opciones de navegaci�n

// Determino tipo de consulta ---------------------------------------------

$subtitulo=''; 
if (empty($_tipoconsulta))
 {
  $_tipoconsulta=0;
 } 
 
/*
if (empty($organismo) AND empty($organimo_id) AND empty($organismo_txt)) 
 {
  $_tipoconsulta=0;
 } 
*/


 
switch($_tipoconsulta)
 {
  case '1':
   // Lista por nombre de organismo
   $result=$mysqli->query("SELECT * FROM _organismos                              
                              WHERE organismo_tipo='$organismo_tipo'  AND estado='1' AND organismo LIKE '%$organismo%'"); 
   $total=$result->num_rows;
   $result=$mysqli->query("SELECT * FROM _organismos
                              WHERE organismo_tipo='$organismo_tipo'  AND estado='1' AND organismo LIKE '%$organismo%'
						      LIMIT $desde,$maxmostrar");
   $subtitulo='(Organismo: '.$organismo.')';
   break;


  case '2':
   // Lista por id dele organismo
   $result=$mysqli->query("SELECT organismo_id FROM _organismos                              
                              WHERE organismo_id = '$organismo_id'  AND estado='1' "); 
   $total=$result->num_rows;
   $result=$mysqli->query("SELECT * FROM _organismos
                              WHERE organismo_id = '$organismo_id'  AND estado='1' 
						      LIMIT $desde,$maxmostrar");
   $subtitulo='(Id del Organismo: '.$organismo_id.')';
   break;


 }

if ($mysqli->errno>0)
 {
  print '<br>Error: '.$mysqli->errno.": ".$mysqli->error."<BR><BR>";
 }
// Fin: Determino tipo de consulta ------------------------------------------------




if ($_tipoconsulta!=0)
{

$AuxLink.='&_tipoconsulta='.$_tipoconsulta;
$AuxLink.='&organismo_tipo='.$organismo_tipo;
$AuxLink.='&organismo='.$organismo;
$AuxLink.='&_variable_organismo_id='.$_variable_organismo_id;
$AuxLink.='&_variable_organismo='.$_variable_organismo;


// Encabezado general
print '<center><font color="#000080" size="3" face="Arial"><strong><u>';
print 'LISTADO DE ORGANISMOS</u></strong></font></center>';

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
   print '<br><center><font color="#FF0000" size="2" face="Arial"><b>'.$total.'</b> organismo</font></center>';
  else
   print '<br><center><font color="#FF0000" size="2" face="Arial"><b>'.$total.'</b> organismos</font></center>';

if ($row=$result->fetch_array() )
 {

   $contador=0;

   // Bandera determina si es la primera vez que entra al ciclo
   $AUXbandera=0;

   //Imprimo encabezado de tabla 
   print '<center>';
   print '<table border="0" cellpadding="2">';
   print '<tr>';

   print ' <td bgcolor="#0099cc" align="center"><b><font face="Arial" size="2" color="#FFFFFF">NOMBRE</font></b></td>';
   print ' <td bgcolor="#0099cc" align="center"><b><font face="Arial" size="2" color="#FFFFFF">CODIGO</font></b></td>';
   print ' <td bgcolor="#0099cc" align="center"><b><font face="Arial" size="2" color="#FFFFFF">.</font></b></td>';   

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

   print '<td bgcolor="'.$colorfondofila.'" align="left" valign="top"><font face="Arial" size="2">';
   if ($_tipoconsulta=='1')
     {
       print ereg_replace($organismo, "<b>".$organismo."</b>", $row["organismo"]);
     }	
    else
     {
      print $row["organismo"];
     }
   print '</font></td>';  

   print '<td bgcolor="'.$colorfondofila.'" align="left" valign="top"><font face="Arial" size="2">'.$row["organismo_id"].'</font></td>';

   print '<td bgcolor="'.$colorfondofila.'" align="left" valign="top"><font face="Arial" size="2">';
   if (!empty($_variable_organismo_id) AND !empty($_variable_organismo)) 
    {

	 $AUXorganismo_id=$row["organismo_id"];

	 //$OnClick = "actualizoAreas($AUXorganismo_id,'$_variable_donde_van_las_areas');";

	 $OnClick= 'parent.document.f1.'.$_variable_organismo_id.".value='".$row["organismo_id"]."'; "; 
	 $OnClick.= 'parent.document.f1.'.$_variable_organismo.".value='".$row["organismo"]."'; ";
	 $OnClick.= "parent.CapaOrganismoConsultar2.style.visibility='hidden';";
	 $OnClick.= 'parent.document.f1.'.$_variable_donde_va_el_focus.".focus();"; 
	 print "<script>alert($OnClick)</script>";
     print '<input type="button" name="boton'.$contador.'" id="boton'.$contador.'" value="<--" onclick="'.$OnClick.'">';   
    }
   print '</font></td>';
   


   print '</tr>';



  }
 while ($row=$result->fetch_array());
 print '</table>';
 print '</center>';

 if ($contador>0)
  print '<script>boton1.focus();</script>';

 // Botones de navegaci�n.

 print '<center>';

 // Primero
 if ($desde>0)
  print '<b>[ <a href="organismo_consultar2.php?desde=0&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">Primera</font></a> ] </b>';

 // Anterior
 if ($desde>0)
   print '<b>[ <a href="organismo_consultar2.php?desde='.($desde-$maxmostrar).'&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">Anterior</font></a> ] </b>';

 // Siguiente
 if (($desde+$maxmostrar)<$total)
  {
   print '<b>[ <a href="organismo_consultar2.php?desde='.($desde+$maxmostrar).'&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">Siguiente</font></a> ] </b>';
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
     $linkpaginas=$linkpaginas.'<a href="organismo_consultar2.php?desde='.($i-1).'&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">'.$pagina.'</font></a>';

  }

 // Ultimo
 if (($desde+$maxmostrar)<$total)
  {
   print '<b>[ <a href="organismo_consultar2.php?desde='.(($pagina-1)*$maxmostrar).'&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">Ultima</font></a> ]</b>';
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



if (!empty($_mensaje))
 {
  print '<center><font face="arial" size="2" color="#FF0000">';
  print $_mensaje;
  print '</font></center>';
 }

?>
<table border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><form name="form1" method="post" action="organismo_consultar2.php">
        <table border="1" align="center" cellpadding="2" cellspacing="0">
          <tr> 
            <td colspan="4" align="left" valign="middle" bgcolor="#003399"> <div align="center"><font color="#FFFFFF" size="1" face="Arial, Helvetica, sans-serif">B&uacute;squeda 
                </font></div></td>
          </tr>
          <tr> 
            <td width="104" align="left" valign="middle" bgcolor="#BFEBFF"> <div align="center"><font size="1" face="Arial, Helvetica, sans-serif">T&iacute;po: 
                </font></div></td>
            <td width="79" bgcolor="#BFEBFF"><font size="1" face="Arial, Helvetica, sans-serif">C&oacute;digo: 
              </font></td>
            <td width="7" bgcolor="#BFEBFF"><font size="1" face="Arial, Helvetica, sans-serif">Nombre: 
              </font></td>
            <td width="142" align="left" valign="middle" bgcolor="#BFEBFF"> <div align="center">&nbsp;</div></td>
          </tr>
          <tr> 
            <td align="left" valign="middle"><select name="organismo_tipo" id="select2">
                <option value="A" <? if ($organismo_tipo=='A') print 'selected'; ?>>Administrativo</option>
                <option value="P" <? if ($organismo_tipo=='P') print 'selected'; ?>>Policial</option>
                <option value="E" <? if ($organismo_tipo=='E') print 'selected'; ?>>Escuela</option>
              </select></td>
            <td><font size="1" face="Arial, Helvetica, sans-serif"> 
              <input name="organismo_id" type="text" id="organismo_id" value="<? print $organismo_id; ?>" size="3" maxlength="3" onChange="organismo.value=''; organismo_txt.value='';">
              </font></td>
            <td><font size="1" face="Arial, Helvetica, sans-serif">
              <input name="organismo" type="text" id="organismo" value="<? print $organismo; ?>" size="15" maxlength="100" onChange="organismo_id.value='';">
              </font></td>
            <td align="left" valign="middle"><input type="submit" name="Submit" style="font-family: Arial; font-size: 8 pt; color: #FFFFFF; background-color: #003399" value="Buscar"> 
              <input name="_variable_organismo_id" type="hidden" id="_variable2" value="<? print $_variable_organismo_id; ?>"> 
              <input name="_variable_organismo" type="hidden" id="_variable_organismo_id" value="<? print $_variable_organismo; ?>"> 
              <input name="_tipoconsulta" type="hidden" id="_tipoconsulta" value="<? print $_tipoconsulta; ?>">
              <input name="organismo_txt" type="hidden" id="organismo_txt" value="<? print $organismo_txt; ?>">
              <input name="_variable_donde_va_el_focus " type="hidden" id="_variable_donde_va_el_focus " value="<? print $_variable_donde_va_el_focus; ?>">
              <input name="_variable_donde_va_el_focus" type="hidden" id="_variable_donde_va_el_focus" value="<? print $_variable_donde_va_el_focus; ?>"> 
              <?
			   if ($total==0)
			    {
			     print '<script>document.form1.organismo.focus();</script></td>';
				}  
			  ?>
          </tr>
        </table>
      </form></td>
  </tr>
</table>
<CENTER> 
  <input name="Bot&oacute;n" type="button" style="font-family: Arial; font-size: 8 pt; color: #FFFFFF; background-color: #003399" value="Cerrar ventana" onClick="parent.CapaOrganismoConsultar2.style.visibility='hidden';" >
</CENTER>
</body>
</html>