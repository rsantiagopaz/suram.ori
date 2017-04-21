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


<html><title>CARGA AREAS DEL ORGANISMO SELECCIONADO < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < < </title>
<body bgcolor="#F7F7F7" leftmargin="0" topmargin="0">



  <?



// CONEXION A LA BASE DE DATOS
include($SYSpathraiz.'config.php');
// FIN: CONEXION A LA BASE DE DATOS



// *********************** I N I C I O ***********************************************


// DATOS DE ENTRADA $  = $_REQUEST[""]; -----------------
$_organismo_id                = $_REQUEST["_organismo_id"];
$_variable_organismo_area_id = $_REQUEST["_variable_organismo_area_id"];
$_variable_organismo_area    = $_REQUEST["_variable_organismo_area"];


// Lista las areas del organismo organismo_id y que respondan a la consulta _area_a_buscar
$result=mysql_query("SELECT * FROM _organismos_areas  
                             WHERE organismo_id='$_organismo_id' 
						     ORDER BY organismo_area		   
							  "); 
$total=mysql_numrows($result);


if (mysql_errno()>0)
 {
  print '<br>Error: '.mysql_errno().": ".mysql_error()."<BR><BR>";
 }
else
 {
  if ($row=mysql_fetch_array($result))
   {

    //$_variable_organismo_area_id="parent.document.f1.organismo_area_id";
    $_variable_organismo_area_id="parent.document.f1.$_variable_organismo_area_id";

    $contador=0;
	print "<script>";
	$AUXtotal=$total+1;
   	print $_variable_organismo_area_id.".length=$AUXtotal;"; 

    $aux1= $_variable_organismo_area_id."[0].text='';";
	$aux2= $_variable_organismo_area_id."[0].value='';";

    print $aux1;
	print "\n";
  	print $aux2;
	print "\n";	  

	
    do
    {
      $contador++;	  
	  
	  $aux1= $_variable_organismo_area_id."[$contador].text='".$row["organismo_area"]."';";
	  $aux2= $_variable_organismo_area_id."[$contador].value='".$row["organismo_area_id"]."';";

	  print $aux1;
	  print "\n";
  	  print $aux2;
	  print "\n";	  
	  

	  
    }
    while ($row=mysql_fetch_array($result));

    print "</script>"; 
	print "Contador=$contador";
   }
  else
   {
    // no se encontraron areas para ese organismo
	print 'No se encontraron areas';
   } 
 }



?>

</body>
</html>