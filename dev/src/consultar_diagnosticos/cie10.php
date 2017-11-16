<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

switch ($_REQUEST['rutina'])
{
	case "traer_cie10":{
		$xml=new SimpleXMLElement('<rows/>');
		$sql= "SELECT cod_4 'codigo', CONCAT(cod_4,' - ',descripcion4) 'coddescrip', descripcion4 'descripcion' ";
		$sql.= "FROM cie10 ";
		$sql.= "WHERE CONCAT(cod_4,' - ',descripcion4) LIKE '%$filter%' ";
		$sql.= "ORDER BY descripcion4 ";
		
		$SELECT = $mysqli->query($sql);
		toXML($xml, $sql, "diagnostico");
		header('Content-Type: text/xml');
		echo $xml->asXML();

		break;
	}
}
?>