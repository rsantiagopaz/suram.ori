<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

switch ($_REQUEST['rutina'])
{
	case 'traer_especialidades': {

		$xml=new SimpleXMLElement('<rows/>');
		
		$sql="SELECT * ";
		$sql.="FROM especialidades ";
		$sql.="ORDER BY especialidad";
		
		
		toXML($xml, $sql, "especialidad");
		header('Content-Type: text/xml');
		echo $xml->asXML();
	
		break;
	}
}
?>