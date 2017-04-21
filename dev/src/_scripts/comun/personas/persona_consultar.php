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


<html><title>CONSULTA DE PERSONAS < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < </title>
<body bgcolor="#F7F7F7" leftmargin="0" topmargin="0">
<p><br>
  <?



// CONEXION A LA BASE DE DATOS
include($SYSpathraiz.'config.php');
// FIN: CONEXION A LA BASE DE DATOS



// *********************** I N I C I O ***********************************************


// DATOS DE ENTRADA $  = $_REQUEST[""]; -----------------
$desde            = $_REQUEST["desde"];
$_accion          = $_REQUEST["_accion"];
$_tipoconsulta    = $_REQUEST["_tipoconsulta"];
$persona_tipo     = $_REQUEST["persona_tipo"];
$persona_nombre   = strtoupper($_REQUEST["persona_nombre"]);
$persona_dni_cuit = $_REQUEST["persona_dni_cuit"];
$_variable        = $_REQUEST["_variable"];
// ------------------------------------------------------


$total=0;

// Opciones de navegación.

if (empty($desde))
 $desde=0;

//if (empty($maxmostrar))
 $maxmostrar=20;

// fin de opciones de navegación



// Determino tipo de consulta ---------------------------------------------

if (empty($_tipoconsulta))
 {
  if (( !empty($persona_nombre) OR !empty($persona_dni_cuit) ) AND !empty($persona_tipo) )
    {

	 // para determinar busqueda por nombre
	 if (!empty($persona_nombre))
	  {
	   if ($persona_tipo=='F')
	     {
		  // Consulta persona fisica por nombre
		  $_tipoconsulta=4;
		 }
		else
	     {
		  // Consulta persona jurídica por nombre
		  $_tipoconsulta=5;
		 }
	   if (strlen($persona_nombre)<3)
	    {
		 $_tipoconsulta=0;
		 $_mensaje.='<br>¡ Debe escribir más de 2 caracteres en el nombre !';
		}
		 
	  }
	  
     // para determinar busqueda por dni o cuit segun sea persona fisica o juridica
	 if (!empty($persona_dni_cuit))
	  {
	   if ($persona_tipo=='F')
	     {
		  // Consulta persona fisica por dni
		  $_tipoconsulta=6;
		 }
		else
	     {
		  // Consulta persona jurídica por cuit
		  $_tipoconsulta=7;
		 }
	  }
	  
	}
   else	
    {
	 $_tipoconsulta=0;
	}
 }  
$subtitulo=''; 
switch($_tipoconsulta)
 {
  case '1':
   // Lista TODO
   //include('persona_consultar01.inc');
   $result=mysql_query("SELECT persona_id FROM _personas"); 
   $total=mysql_numrows($result);
   $result=mysql_query("SELECT * FROM _personas
						      LIMIT $desde,$maxmostrar");
   $subtitulo='(TODAS LAS PERSONAS FISICAS Y JURIDICAS)';
   break;

  case '2':
   // Lista personas Físicas
   //include('persona_consultar02.inc');
   $result=mysql_query("SELECT persona_id FROM _personas
                              WHERE persona_tipo='F'"); 

   $total=mysql_numrows($result);

   $result=mysql_query("SELECT * FROM _personas
                              WHERE persona_tipo='F' 
	                          ORDER BY persona_nombre
						      LIMIT $desde,$maxmostrar");
   $subtitulo='(PERSONAS FISICAS)';
   
   break;

  case '3':
   // Lista personas Jurídicas
   $result=mysql_query("SELECT * FROM _personas
                              WHERE persona_tipo='J'    
                       ");
   $total=mysql_numrows($result);

   $result=mysql_query("SELECT * FROM _personas
                              WHERE persona_tipo='J' 
	                          ORDER BY persona_nombre
						      LIMIT $desde,$maxmostrar");
   $subtitulo='(PERSONAS FISICAS)';
   
   break;

  case '4':
   // Busca persona Física ingresada en variable $persona_nombre
   $result=mysql_query("SELECT * FROM _personas
                              WHERE persona_tipo='F'  AND persona_nombre LIKE '%$persona_nombre%'     
                       ");
   $total=mysql_numrows($result);

   $result=mysql_query("SELECT * FROM _personas
                              WHERE persona_tipo='F'  AND persona_nombre LIKE '%$persona_nombre%'  
	                          ORDER BY persona_nombre
						      LIMIT $desde,$maxmostrar");
   $subtitulo='(Persona Física consultada: '.$persona_nombre.')';
   break;

  case '5':
   // Busca persona Jurídica ingresada en variable $persona_nombre
   $result=mysql_query("SELECT * FROM _personas
                                 WHERE persona_tipo='J'  AND persona_nombre LIKE '%$persona_nombre%'
                       ");
   $total=mysql_numrows($result);

   $result=mysql_query("SELECT * FROM _personas
                              WHERE persona_tipo='J'  AND persona_nombre LIKE '%$persona_nombre%'
	                          ORDER BY persona_nombre
						      LIMIT $desde,$maxmostrar");
   $subtitulo='(Persona Jurídica consultada: '.$persona_nombre.')';
   
   break;

  case '6':
   // Consulta persona fisica por dni
   $result=mysql_query("SELECT * FROM _personas
                              WHERE persona_tipo='F'  AND persona_dni ='$persona_dni_cuit'   
                       ");
   $total=mysql_numrows($result);

   $result=mysql_query("SELECT * FROM _personas
                              WHERE persona_tipo='F'  AND persona_dni ='$persona_dni_cuit'
	                          ORDER BY persona_nombre
						      LIMIT $desde,$maxmostrar");
   $subtitulo='(DNI Consultado: '.$persona_dni_cuit.')';
   
   break;


  case '7':
    // Consulta persona juridica por cuit
   $result=mysql_query("SELECT * FROM _personas
                          WHERE persona_tipo='J'  AND persona_cuit ='$persona_dni_cuit'");
   $total=mysql_numrows($result);

   $result=mysql_query("SELECT * FROM _personas
                              WHERE persona_tipo='J'  AND persona_cuit ='$persona_dni_cuit'
	                          ORDER BY persona_nombre
						      LIMIT $desde,$maxmostrar");
   $subtitulo='(CUIT Consultado: '.$persona_dni_cuit.')';
   
   break;

 }

if (mysql_errno()>0)
 {
  print '<br>Error: '.mysql_errno().": ".mysql_error()."<BR><BR>";
 }
// Fin: Determino tipo de consulta ------------------------------------------------




if ($_tipoconsulta!=0)
{

   $AuxLink.='&_tipoconsulta='.$_tipoconsulta;
   $AuxLink.='&persona_tipo='.$persona_tipo;
   $AuxLink.='&persona_nombre='.$persona_nombre;
   $AuxLink.='&persona_dni_cuit='.$persona_dni_cuit;
   $AuxLink.='&_variable='.$_variable;


// Encabezado general
print '<center><font color="#000080" size="3" face="Arial"><strong><u>';
print 'LISTADO DE PERSONAS</u></strong></font></center>';

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
   print '<br><center><font color="#FF0000" size="2" face="Arial"><b>'.$total.'</b> persona</font></center>';
  else
   print '<br><center><font color="#FF0000" size="2" face="Arial"><b>'.$total.'</b> personas</font></center>';

if ($row=mysql_fetch_array($result) )
 {

   $contador=0;

   // Bandera determina si es la primera vez que entra al ciclo
   $AUXbandera=0;

   //Imprimo encabezado de tabla 
   print '<center>';
   print '<table border="0" cellpadding="2">';
   print '<tr>';

   print ' <td bgcolor="#0099cc" align="center"><b><font face="Arial" size="2" color="#FFFFFF">';
   if ($persona_tipo=='F') 
     {
      print 'DOCUMENTO';
	 }
   if ($persona_tipo=='J') 
     {
      print 'CUIT';
	 }
   print '</font></b></td>';
   
   print ' <td bgcolor="#0099cc" align="center"><b><font face="Arial" size="2" color="#FFFFFF">NOMBRE</font></b></td>';
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

   print '<td bgcolor="'.$colorfondofila.'" align="right" valign="top"><font face="Arial" size="2">';
   if ($persona_tipo=='F') 
     {
      print $row["persona_dni"];
	 }
   if ($persona_tipo=='J') 
     {
      print $row["persona_cuit"];
	 }
   print '</font></td>';
   
   print '<td bgcolor="'.$colorfondofila.'" align="left" valign="top"><font face="Arial" size="2">';
    if ($_tipoconsulta == 4 OR $_tipoconsulta == 5)
	   {
	    print ereg_replace($persona_nombre, "<b>".$persona_nombre."</b>", $row["persona_nombre"]);
	   }
	  else
	   {
	    print $row["persona_nombre"];
	   }	
   print '</font></td>';

   print '<td bgcolor="'.$colorfondofila.'" align="left" valign="top">';
   if ($persona_tipo=='F') 
     {
      print '<input type="button" value="<--" onclick="opener.document.f1.'.$_variable.'.value='.$row["persona_dni"].'; window.close();">';
	 }
   if ($persona_tipo=='J') 
     {
      print '<input type="button" value="<--" onclick="opener.document.f1.'.$_variable.'.value='.$row["persona_cuit"].'; window.close();">';
	 }
   print '</font></td>';
   print '</td>';	
   
   // Imprimo links de "Modificar" y "Quitar"
   if ($_accion=="MQ")
     {
      print '<td><font face="Arial" size="1">';
      print '<a href="licitacion_agregar_modificar_form.php?numero='.$row["numero"].'&accion=MODIFICAR">Editar</a> ';
	  
      // Muestro link "Quitar"   
      $AuxPregunta="'¿ Está seguro de QUITAR Licitación Nº ".$row["numero"].' ('.$row["proyecto_nombre"].") ?'";
      $AuxUrl="'".'licitacion_quitar.php?numero='.$row["numero"]."'";
      $AuxUrl2="'".'persona_consultar.php?_accion=MQ'."'";

      print ' <a href="" onclick="javascript:pregunta('.$AuxPregunta.','.$AuxUrl.','.$AuxUrl2.');return false;"';
      print '><font face= "arial" size="1">Quitar</font></a>';
      print '<br><br>';

      print '</font><td>';
     }


   print '</tr>';

  }
 while ($row=mysql_fetch_array($result));
 print '</table>';
 print '</center>';



 // Botones de navegación.

 print '<center>';

 // Primero
 if ($desde>0)
  print '<b>[ <a href="persona_consultar.php?desde=0&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">Primera</font></a> ] </b>';

 // Anterior
 if ($desde>0)
   print '<b>[ <a href="persona_consultar.php?desde='.($desde-$maxmostrar).'&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">Anterior</font></a> ] </b>';

 // Siguiente
 if (($desde+$maxmostrar)<$total)
  {
   print '<b>[ <a href="persona_consultar.php?desde='.($desde+$maxmostrar).'&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">Siguiente</font></a> ] </b>';
  }

 // Páginas
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
     $linkpaginas=$linkpaginas.'<a href="persona_consultar.php?desde='.($i-1).'&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">'.$pagina.'</font></a>';

  }

 // Ultimo
 if (($desde+$maxmostrar)<$total)
  {
   print '<b>[ <a href="persona_consultar.php?desde='.(($pagina-1)*$maxmostrar).'&maxmostrar='.$maxmostrar.$AuxLink.'"><font face="Arial" color="#800080" size="2">Ultima</font></a> ]</b>';
  }
 
 print '</center>';


 print '<center><font color="#000080" face="Arial" size="2">Visualizando de '.$maxmostrar.' en '.$maxmostrar.'</b><br>';
 print '</center>';
 print '<center>Ir a página: '.$linkpaginas.'<br></font></center>';

 print '<br><br>';


 }
 else
 {
  print '<br><br><center><font face="arial" size="2" color="#FF0000"><b>¡ No se encontraron registros que respondan a la consulta !</b></font></center><br><br>';
 }


} 



if (!empty($_mensaje))
 {
  print '<center><font face="arial" size="2" color="#FF0000">';
  print $_mensaje;
  print '</font></center>';
 }

?>
</p>
<table border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><form name="form1" method="post" action="persona_consultar.php">
        <table border="1" align="center" cellpadding="2" cellspacing="0">
          <tr bgcolor="#003399"> 
            <td colspan="4" align="left" valign="middle"> 
              <div align="center"><font color="#FFFFFF" size="1" face="Arial, Helvetica, sans-serif">B&uacute;squeda 
                </font></div></td>
          </tr>
          <tr> 
            <td align="left" valign="middle" bgcolor="#BFEBFF"> 
              <div align="center"><font size="1" face="Arial, Helvetica, sans-serif">Persona: 
                </font></div></td>
            <td bgcolor="#BFEBFF"> 
              <div align="center"><font size="1" face="Arial, Helvetica, sans-serif">Dni/Cuit: 
                </font></div></td>
            <td align="center" valign="middle" bgcolor="#BFEBFF"><font size="1" face="Arial, Helvetica, sans-serif">Nombre: 
              </font></td>
            <td align="left" valign="middle" bgcolor="#BFEBFF"> 
              <div align="center">&nbsp;</div></td>
          </tr>
          <tr> 
            <td align="left" valign="middle"><select name="persona_tipo" id="select2">
                <option value=""  <? if (empty($persona_tipo)) print 'selected'; ?>></option>
                <option value="F" <? if ($persona_tipo=='F') print 'selected'; ?>>F&iacute;sica</option>
                <option value="J" <? if ($persona_tipo=='J') print 'selected'; ?>>Jur&iacute;dica</option>
              </select></td>
            <td><font size="1" face="Arial, Helvetica, sans-serif"> 
              <input name="persona_dni_cuit" type="text" id="persona_dni_cuit4" size="11" maxlength="11" onfocus="document.form1.persona_nombre.value='';">
              </font></td>
            <td align="left" valign="middle"><font size="1" face="Arial, Helvetica, sans-serif"> 
              <input name="persona_nombre" type="text" id="persona_nombre3" size="15"  onfocus="document.form1.persona_dni_cuit.value='';">
              </font></td>
            <td align="left" valign="middle"><input type="submit" name="Submit" style="font-family: Arial; font-size: 8 pt; color: #FFFFFF; background-color: #003399" value="Buscar"> 
              <input name="_variable" type="hidden" id="_variable2" value="<? print $_variable; ?>"> 
			  <? 
			   if ($total==0)
			    { 
				 print '<script>document.form1.persona_nombre.focus();</script></td>'; 
				}
			  ?>	
          </tr>
        </table>
      </form></td>
  </tr>
</table>
<CENTER> 
  <input name="Bot&oacute;n" type="button" style="font-family: Arial; font-size: 8 pt; color: #FFFFFF; background-color: #003399" value="Cerrar ventana" onClick="window.close()" >
</CENTER>
</body>
</html>