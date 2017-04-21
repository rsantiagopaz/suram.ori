<?php
include ("../control_acceso_flex.php");
include ("../rutinas.php");

if (!isset($filtro_espera))
	$filtro_espera = '';
if (!isset($filtro_espera_medico))
	$filtro_espera_medico = '';
if (!isset($filtro_atendido))
	$filtro_atendido = '';
if (!isset($fecha_desde_espera))
	$fecha_desde_espera = '';
if (!isset($fecha_hasta_espera))
	$fecha_hasta_espera = '';
if (!isset($fecha_desde_atendido))
	$fecha_desde_atendido = '';
if (!isset($fecha_hasta_atendido))
	$fecha_hasta_atendido = '';

switch ($_REQUEST['rutina']) {
	case 'lista_espera' :
		{
			$xml = new SimpleXMLElement('<rows/>');

			$sql = "SELECT i.id_ingreso, i.id_turno, ";
			$sql .= "p.persona_nombre, DATE_FORMAT(t.hora_consulta,'%H:%i') 'hora_consulta', ";
			$sql .= "DATE_FORMAT(fecha_consulta,'%d/%m/%Y') 'fecha_consulta', pe.persona_nombre 'medico' ";
			$sql .= "FROM turnos t ";
			$sql .= "INNER JOIN ingresos i USING(id_turno) ";
			$sql .= "INNER JOIN $salud._personas p ON i.id_persona=p.persona_id ";			
			$sql .= "INNER JOIN $salud.oas_usuarios ou ON i.id_medico = ou.id_oas_usuario ";
			$sql .= "INNER JOIN $salud._usuarios ON ou.SYSusuario = _usuarios.SYSusuario ";
			$sql .= "INNER JOIN $salud._personas pe ON _usuarios.id_persona = pe.persona_id ";
			$sql .= "INNER JOIN $salud._organismos_areas_servicios oas ON ou.id_organismo_area_servicio = oas.id_organismo_area_servicio ";
			$sql .= "INNER JOIN $salud._organismos_areas oa ON oas.id_organismo_area = oa.organismo_area_id ";	
			$sql .= "WHERE fecha_consulta<CURDATE() ";
			$sql .= "AND estado_turno='AD' ";
			$sql .= "AND p.persona_nombre LIKE '%$filtro_espera%' ";
			$sql .= "AND pe.persona_nombre LIKE '%$filtro_espera_medico%' ";
			$sql .= "AND oa.organismo_area_id = '".$_SESSION['usuario_organismo_area_id']."' ";
			if ($fecha_desde_espera == "" && $fecha_hasta_espera == "")
				$sql.="ORDER BY hora_consulta DESC LIMIT 100";
			else {
				if ($fecha_desde_espera != "")
					$sql.="AND fecha_consulta >= '".YYYYDM($fecha_desde_espera)."' ";
				if ($fecha_hasta_espera != "")
					$sql.="AND fecha_consulta <= '".YYYYDM($fecha_hasta_espera)."' ";
				$sql.="ORDER BY hora_consulta DESC";
			}			
			toXML($xml, $sql, "pacientes");
			header('Content-Type: text/xml');
			echo $xml->asXML();

			break;
		}
	case 'lista_atendido' :
		{

			$xml = new SimpleXMLElement('<rows/>');

			$sql = "SELECT i.id_ingreso, i.id_turno, ";
			$sql .= "p.persona_nombre, DATE_FORMAT(t.hora_consulta,'%H:%i') 'hora_consulta', ";
			$sql .= "DATE_FORMAT(fecha_consulta,'%d/%m/%Y') 'fecha_consulta', estado_turno ";
			$sql .= "FROM turnos t ";
			$sql .= "INNER JOIN ingresos i USING(id_turno) ";
			$sql .= "INNER JOIN $salud._personas p ON i.id_persona=p.persona_id ";
			$sql .= "INNER JOIN $salud.oas_usuarios ou ON i.id_medico = ou.id_oas_usuario ";
			$sql .= "INNER JOIN $salud._usuarios ON ou.SYSusuario = _usuarios.SYSusuario ";
			$sql .= "INNER JOIN $salud._personas pe ON _usuarios.id_persona = pe.persona_id ";
			$sql .= "INNER JOIN $salud._organismos_areas_servicios oas ON ou.id_organismo_area_servicio = oas.id_organismo_area_servicio ";
			$sql .= "INNER JOIN $salud._organismos_areas oa ON oas.id_organismo_area = oa.organismo_area_id ";		
			$sql .= "WHERE fecha_consulta<CURDATE() ";
			$sql .= "AND (estado_turno='AT' OR estado_turno='AU') ";
			$sql .= "AND p.persona_nombre LIKE '%$filtro_atendido%' ";
			$sql .= "AND oa.organismo_area_id = '".$_SESSION['usuario_organismo_area_id']."' ";
			if ($fecha_desde_atendido == "" && $fecha_hasta_atendido == "")
				$sql.="ORDER BY hora_consulta DESC LIMIT 100";
			else {
				if ($fecha_desde_atendido != "")
					$sql.="AND fecha_consulta >= '".YYYYDM($fecha_desde_atendido)."' ";
				if ($fecha_hasta_atendido != "")
					$sql.="AND fecha_consulta <= '".YYYYDM($fecha_hasta_atendido)."' ";
				$sql.="ORDER BY hora_consulta DESC";
			}

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