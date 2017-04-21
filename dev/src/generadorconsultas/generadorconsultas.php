<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

switch ($_REQUEST['rutina'])
{
	case 'traer_servicios':
	{
		$xml=new SimpleXMLElement('<rows/>');
		
		$sql="SELECT id_servicio, denominacion ";
		$sql.="FROM $salud._organismos_areas_servicios ";
		$sql.="JOIN $salud._servicios ";
		$sql.="USING ( id_servicio ) ";
		$sql.="WHERE id_organismo_area = '".$_SESSION['usuario_organismo_area_id']."' ";
		$sql.="ORDER BY denominacion";
		toXML($xml, $sql, "servicio");
		
		header('Content-Type: text/xml');
		echo $xml->asXML();
		break;
	}
}
?>