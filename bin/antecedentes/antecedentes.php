<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

switch ($_REQUEST['rutina'])
{
	case 'insert': 
	{
		$_REQUEST["xmlAntecedente"] = str_replace('\"','"',$_REQUEST["xmlAntecedente"]);
		$xml_Antecedente = loadXML($_REQUEST["xmlAntecedente"]);
		
		$query = "SELECT id_persona FROM $suram.ingresos INNER JOIN $suram.ingresos_movimientos im USING(id_ingreso) ";
		$query.="WHERE im.id_ingreso_movimiento='".$xml_Antecedente["id_ingreso_movimiento"]."' ";
		$result = mysql_query($query);
		
		$row = mysql_fetch_array($result);
		
		$id_persona = $row['id_persona'];	
		
		$fecha = YYYYDM($xml_Antecedente["fecha"]);
		
		if ($xml_Antecedente["accion"] == 'Alta') {
			$accion = 'A';
		} elseif ($xml_Antecedente["accion"] == 'Modificación') {
			$accion = 'M';
		} else {
			$accion = 'B';
		}
		
		$xml=new SimpleXMLElement('<rows/>');
		
		$sql="INSERT $salud.027_antecedentes_personas ";
		$sql.="SET id_antecedente='".$xml_Antecedente["id_antecedente"]."', ";
		$sql.="observaciones='".$xml_Antecedente["observaciones"]."', ";
		$sql.="id_persona='".$id_persona."', ";
		$sql.="usuario='".$SYSusuario."', ";
		$sql.="accion='".$accion."', ";
		$sql.="fecha='$fecha' ";
		toXML($xml, $sql, "add");
				
		header('Content-Type: text/xml');
		echo $xml->asXML();
		break;
	}
}
?>