<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

switch ($_REQUEST['rutina'])
{
	case "traer_diagnosticos":{
		$xml=new SimpleXMLElement('<rows/>');
		$sql= "SELECT id_diagnostico, CONCAT(cod_4,' - ',descripcion4) 'coddescrip', descripcion4 'descripcion', cod_4 'codigo' ";
		$sql.= "FROM cie10 ";
		$sql.= "WHERE CONCAT(cod_4,' - ',descripcion4) LIKE '%$descripcion%' ";
		$sql.= "ORDER BY descripcion ";			
		
		$SELECT = $mysqli->query($sql);
		toXML($xml, $sql, "diagnostico");
		header('Content-Type: text/xml');
		echo $xml->asXML();

		break;
	}
}
?>