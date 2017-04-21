<?php
include ("../control_acceso_flex.php");
include ("../rutinas.php");

if (!isset($filtro_espera))
	$filtro_espera = '';
if (!isset($filtro_atendido))
	$filtro_atendido = '';

switch ($_REQUEST['rutina']) {
	case 'lista_espera' :
		{
			$xml = new SimpleXMLElement('<rows/>');

			$sql = "SELECT i.id_ingreso, i.id_turno, ";
			$sql .= "p.persona_nombre, DATE_FORMAT(t.hora_consulta,'%H:%i') 'hora_consulta' ";
			$sql .= "FROM turnos t ";
			$sql .= "INNER JOIN ingresos i USING(id_turno) ";
			$sql .= "INNER JOIN $salud._personas p ON i.id_persona=p.persona_id ";			
			$sql .= "WHERE i.id_medico='" . $_SESSION['id_oas_usuario'] . "' ";
			$sql .= "AND fecha_consulta=CURDATE() ";
			$sql .= "AND estado_turno='AD' ";
			$sql .= "AND p.persona_nombre LIKE '%$filtro_espera%' ";
			$sql .= "ORDER BY hora_consulta";

			toXML($xml, $sql, "pacientes");
			header('Content-Type: text/xml');
			echo $xml->asXML();

			break;
		}
	case 'lista_atendido' :
		{

			$xml = new SimpleXMLElement('<rows/>');

			$sql = "SELECT i.id_ingreso, i.id_turno, ";
			$sql .= "p.persona_nombre, DATE_FORMAT(t.hora_consulta,'%H:%i') 'hora_consulta', estado_turno ";
			$sql .= "FROM turnos t ";
			$sql .= "INNER JOIN ingresos i USING(id_turno) ";
			$sql .= "INNER JOIN $salud._personas p ON i.id_persona=p.persona_id ";
			$sql .= "WHERE i.id_medico='" . $_SESSION['id_oas_usuario'] . "' ";
			$sql .= "AND fecha_consulta=CURDATE() ";
			$sql .= "AND (estado_turno='AT' OR estado_turno='AU') ";
			$sql .= "AND p.persona_nombre LIKE '%$filtro_atendido%' ";
			$sql .= "ORDER BY hora_consulta";

			toXML($xml, $sql, "pacientes");

			header('Content-Type: text/xml');
			echo $xml->asXML();

			break;
		}
	case 'poner_ausente' :
		{

			$xml = new SimpleXMLElement('<rows/>');

			$sql = "UPDATE turnos SET estado_turno='AU' WHERE id_turno='$id_turno'";

			toXML($xml, $sql, "pacientes");

			header('Content-Type: text/xml');
			echo $xml->asXML();

			break;
		}
	case 'recuperar_turno' :
		{

			$xml = new SimpleXMLElement('<rows/>');

			$sql = "UPDATE turnos SET estado_turno='AD' WHERE id_turno='$id_turno'";

			toXML($xml, $sql, "pacientes");

			header('Content-Type: text/xml');
			echo $xml->asXML();

			break;
		}
}
?>