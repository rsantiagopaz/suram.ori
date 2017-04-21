<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

switch ($_REQUEST['rutina'])
{
	case 'insert': 
	{
		$_REQUEST["xmlMedicamento"] = str_replace('\"','"',$_REQUEST["xmlMedicamento"]);
		$xml_Medicamento = loadXML($_REQUEST["xmlMedicamento"]);	
		
		$xml=new SimpleXMLElement('<rows/>');
				
		$sql="INSERT vademecum ";
		$sql.="SET monodroga='".$xml_Medicamento["monodroga"]."', ";
		$sql.="concentracion='".$xml_Medicamento["concentracion"]."', ";
		$sql.="presentacion='".$xml_Medicamento["presentacion"]."' ";			
		toXML($xml, $sql, "add");		
						
		header('Content-Type: text/xml');
		echo $xml->asXML();
		break;
	}
	case 'update': 
	{
		$_REQUEST["xmlMedicamento"] = str_replace('\"','"',$_REQUEST["xmlMedicamento"]);
		$xml_Medicamento = loadXML($_REQUEST["xmlMedicamento"]);	
				
		$xml=new SimpleXMLElement('<rows/>');
				
		$sql="UPDATE vademecum ";
		$sql.="SET monodroga='".$xml_Medicamento["monodroga"]."', ";
		$sql.="concentracion='".$xml_Medicamento["concentracion"]."', ";
		$sql.="presentacion='".$xml_Medicamento["presentacion"]."' ";
		$sql.="WHERE id_vademecum='".$xml_Medicamento["id_vademecum"]."' ";
		toXML($xml, $sql, "upd");			
				
		header('Content-Type: text/xml');
		echo $xml->asXML();
		break;
	}
	case 'delete': 
	{
		$_REQUEST["xmlMedicamento"] = str_replace('\"','"',$_REQUEST["xmlMedicamento"]);
		$xml_Medicamento = loadXML($_REQUEST["xmlMedicamento"]);	
		
		
		$xml=new SimpleXMLElement('<rows/>');
		
		$sql="SELECT count(id_vademecum) 'cc' FROM prescripciones WHERE id_vademecum ='".$xml_Medicamento["id_vademecum"]."'";
		
		$result = mysql_query($sql);
		
		$row = mysql_fetch_array($result);
		
		if ($row['cc'] == 0) {
			$sql="DELETE FROM vademecum ";
			$sql.="WHERE id_vademecum='".$xml_Medicamento["id_vademecum"]."' ";
			toXML($xml, $sql, "del");
					
			header('Content-Type: text/xml');
			echo $xml->asXML();	
		} else {
			$error = "El Medicamento no puede ser eliminado una vez que se han registrado prescripciones para el mismo";
			$xml = "<?xml version='1.0' encoding='UTF-8' ?>";
			$xml .= "<xml>";
			$xml .= "<error>$error</error>";			
			$xml.= "</xml>";
			header('Content-Type: text/xml');
			print $xml;
		}
				
		break;
	}
	case "traer_vademecum":{
		$xml=new SimpleXMLElement('<rows/>');
		$sql= "SELECT id_vademecum, monodroga, presentacion, concentracion ";
		$sql.= "FROM vademecum ";
		$sql.= "WHERE monodroga LIKE '%$filter%' ";
		$sql.= "AND activo = '1' ";
		$sql.= "ORDER BY monodroga ";
		
		$SELECT = mysql_query($sql);
		toXML($xml, $sql, "vademecum");
		header('Content-Type: text/xml');
		echo $xml->asXML();

		break;
	}
}
?>