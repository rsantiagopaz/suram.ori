<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

switch ($_REQUEST['rutina'])
{
	case 'insert': 
	{
		$_REQUEST["xmlPractica"] = str_replace('\"','"',$_REQUEST["xmlPractica"]);
		$xml_Practica = loadXML($_REQUEST["xmlPractica"]);	
		
		$xml=new SimpleXMLElement('<rows/>');
				
		$sql="INSERT nomenclador_practicas ";
		$sql.="SET procedimiento='".$xml_Practica["descripcion"]."' ";				
		toXML($xml, $sql, "add");		
						
		header('Content-Type: text/xml');
		echo $xml->asXML();
		break;
	}
	case 'update': 
	{
		$_REQUEST["xmlPractica"] = str_replace('\"','"',$_REQUEST["xmlPractica"]);
		$xml_Practica = loadXML($_REQUEST["xmlPractica"]);	
				
		$xml=new SimpleXMLElement('<rows/>');
				
		$sql="UPDATE nomenclador_practicas ";
		$sql.="SET procedimiento='".$xml_Practica["descripcion"]."' ";
		$sql.="WHERE id_practica='".$xml_Practica["id_practica"]."' ";
		toXML($xml, $sql, "upd");			
				
		header('Content-Type: text/xml');
		echo $xml->asXML();
		break;
	}
	case 'delete': 
	{
		$_REQUEST["xmlPractica"] = str_replace('\"','"',$_REQUEST["xmlPractica"]);
		$xml_Practica = loadXML($_REQUEST["xmlPractica"]);	
		
		
		$xml=new SimpleXMLElement('<rows/>');
		
		$sql="SELECT count(id_practica) 'cc' FROM solicitudes WHERE id_practica ='".$xml_Practica["id_practica"]."'";
		
		$result = mysql_query($sql);
		
		$row = mysql_fetch_array($result);
		
		if ($row['cc'] == 0) {
			$sql="DELETE FROM nomenclador_practicas ";
			$sql.="WHERE id_practica='".$xml_Practica["id_practica"]."' ";
			toXML($xml, $sql, "del");
					
			header('Content-Type: text/xml');
			echo $xml->asXML();	
		} else {
			$error = "La Práctica no puede ser eliminada una vez que se han registrado solicitudes para la misma";
			$xml = "<?xml version='1.0' encoding='UTF-8' ?>";
			$xml .= "<xml>";
			$xml .= "<error>$error</error>";			
			$xml.= "</xml>";
			header('Content-Type: text/xml');
			print $xml;
		}
				
		break;
	}
	case "traer_practicas":{
		$xml=new SimpleXMLElement('<rows/>');
		$sql= "SELECT id_practica, procedimiento 'descripcion', codigo ";
		$sql.= "FROM nomenclador_practicas ";
		$sql.= "WHERE procedimiento LIKE '%$filter%' ";
		$sql.= "AND activo='1' ";
		$sql.= "ORDER BY procedimiento ";
		
		$SELECT = mysql_query($sql);
		toXML($xml, $sql, "practica");
		header('Content-Type: text/xml');
		echo $xml->asXML();

		break;
	}
}
?>