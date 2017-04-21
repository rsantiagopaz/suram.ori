<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

switch ($_REQUEST['rutina'])
{
	case 'insert': 
	{
		$_REQUEST["xmlPrescripcion"] = str_replace('\"','"',$_REQUEST["xmlPrescripcion"]);
		$xml_Prescripcion = loadXML($_REQUEST["xmlPrescripcion"]);	
		
		$fecha_prescripcion = YYYYDM($xml_Prescripcion["fecha_prescripcion"]);
		$xml=new SimpleXMLElement('<rows/>');
		
		$sql="INSERT prescripciones ";
		$sql.="SET id_ingreso_movimiento='".$xml_Prescripcion["id_ingreso_movimiento"]."', ";
		$sql.="id_vademecum='".$xml_Prescripcion["id_vademecum"]."', ";
		$sql.="posologia='".$xml_Prescripcion["posologia"]."', ";
		$sql.="fecha_prescripcion='$fecha_prescripcion' ";
		toXML($xml, $sql, "add");
				
		header('Content-Type: text/xml');
		echo $xml->asXML();
		break;
	}
	case 'update': 
	{
		$_REQUEST["xmlPrescripcion"] = str_replace('\"','"',$_REQUEST["xmlPrescripcion"]);
		$xml_Prescripcion = loadXML($_REQUEST["xmlPrescripcion"]);	
		
		$fecha_prescripcion = YYYYDM($xml_Prescripcion["fecha_prescripcion"]);
		$xml=new SimpleXMLElement('<rows/>');
		
		$sql="UPDATE prescripciones ";
		$sql.="SET id_vademecum='".$xml_Prescripcion["id_vademecum"]."', ";
		$sql.="posologia='".$xml_Prescripcion["posologia"]."', ";
		$sql.="fecha_prescripcion='$fecha_prescripcion' ";
		$sql.="WHERE id_prescripcion='".$xml_Prescripcion["id_prescripcion"]."' ";
		toXML($xml, $sql, "upd");
				
		header('Content-Type: text/xml');
		echo $xml->asXML();
		break;
	}
	case 'delete': 
	{
		$_REQUEST["xmlPrescripcion"] = str_replace('\"','"',$_REQUEST["xmlPrescripcion"]);
		$xml_Prescripcion = loadXML($_REQUEST["xmlPrescripcion"]);	
		
		
		$xml=new SimpleXMLElement('<rows/>');
		
		$sql="DELETE FROM prescripciones ";
		$sql.="WHERE id_prescripcion='".$xml_Prescripcion["id_prescripcion"]."' ";
		toXML($xml, $sql, "del");
				
		header('Content-Type: text/xml');
		echo $xml->asXML();
		break;
	}
}
?>