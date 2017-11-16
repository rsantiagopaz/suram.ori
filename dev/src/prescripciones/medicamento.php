<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

switch ($_REQUEST['rutina'])
{
	case "traer_medicamentos":{
		$descrip = strtoupper($descrip);
		$xml=new SimpleXMLElement('<rows/>');
		$sql= "SELECT CONCAT(monodroga, ' ',presentacion, ' ',concentracion) 'descrip', ";
		$sql.= "id_vademecum,  presentacion, monodroga, concentracion ";
		$sql.= "FROM vademecum ";
		$sql.= "WHERE CONCAT(monodroga, ' ',presentacion, ' ',concentracion) LIKE '%$descrip%' ";
		$sql.= "AND activo='1' ";
		$sql.= "ORDER BY descrip ";
		
		$SELECT = $mysqli->query($sql);
		toXML($xml, $sql, "medicamento");
		header('Content-Type: text/xml');
		echo $xml->asXML();

		break;
	}
}
?>