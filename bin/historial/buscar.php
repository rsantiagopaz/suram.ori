<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

switch ($_REQUEST['rutina'])
{
	case "buscar_persona":{
		$xml=new SimpleXMLElement('<rows/>');
		
		$sql = "SELECT persona_id FROM $salud._personas WHERE persona_tipodoc = '$tipo_doc' AND persona_dni = '$nro_doc'";
		toXML($xml, $sql, "persona");						
		header('Content-Type: text/xml');
		echo $xml->asXML();
		break;
	}
}
?>