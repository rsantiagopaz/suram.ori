<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

switch ($_REQUEST['rutina'])
{
	case 'traer_datos': {
		$xml=new SimpleXMLElement('<rows/>');
		//combo nueva vacuna
		$sql= "SELECT id_dosis, CONCAT(nombre,' - dosis ',denominacion) as nombre, enfermedades ";		
		$sql.= "FROM $salud.027_vacunas JOIN $salud.027_dosis USING(id_vacuna) ";
		$sql.= "ORDER BY nombre";
		toXML($xml, $sql, "vacunas");
		
		//para la pestaña especialidad
		$sql="SELECT * ";
		$sql.="FROM especialidades ";
		$sql.="ORDER BY especialidad";
		toXML($xml, $sql, "especialidad");
		
		//para la pestaña datos de consulta
		$sql="SELECT * ";
		$sql.="FROM tipos_ingresos ";
		//$sql.="ORDER BY descripcion";
		toXML($xml, $sql, "tipoingreso");
		
		$sql="SELECT id.id_item_diagnostico, id.id_ingreso_movimiento, id.id_diagnostico, CONCAT(cie10.cod_4,' - ',cie10.descripcion4) 'coddescrip', cie10.descripcion4 'descripcion', tipo_diagnostico,  cie10.cod_4 'codigo'  ";
		$sql.="FROM ingresos i ";
		$sql.="INNER JOIN ingresos_movimientos im USING(id_ingreso) ";
		$sql.="INNER JOIN items_diagnosticos id USING(id_ingreso_movimiento) ";
		$sql.="INNER JOIN cie10 USING(id_diagnostico) ";		
		$sql.="WHERE im.id_ingreso='$id_ingreso' ";
		$sql.="ORDER BY cie10.descripcion4";	
		toXML($xml, $sql, "diagnostico");
		
		$sql="SELECT id_tipo_ingreso, primera_vez, motivo, observaciones, ";
		$sql.="peso, talla, perimetro, peso_edad, talla_edad, perc_perimetro, ";
		$sql.="embarazada, trimestre, tension_arterial, ";
		$sql.="derivacion, tipo_derivacion, id_especialidad ";
		$sql.="FROM ingresos i  ";
		$sql.="WHERE i.id_ingreso='$id_ingreso' ";
		toXML($xml, $sql, "datosconsulta");
		
		
		
		
		$sql = "SELECT * FROM ingresos_especialidad WHERE id_ingreso=" . $id_ingreso;
		$rs = $mysqli->query($sql);
		if ($rs->num_rows > 0) {
			$row = $rs->fetch_object();
			
			$nodo = new SimpleXMLElement($row->json);
		} else {
			$nodo = new SimpleXMLElement('<ingresos_especialidad>
				<ingresos_especialidad 
					antecedentes="" 
					enfermedad="" 
					pronostico="" 
					indicaciones="" 
					agudeza_sc_od="No percibe luz" 
					agudeza_sc_oi="No percibe luz" 
					agudeza_cc_od="No percibe luz" 
					agudeza_cc_oi="No percibe luz" 
					esf_od="0" 
					esf_oi="0" 
					cil_od="0" 
					cil_oi="0" 
					eje_od="0" 
					eje_oi="0" 
					pio_od="1" 
					pio_oi="1" 
					biomi_od="" 
					biomi_oi="" 
					fo_od="" 
					fo_oi="" 
					diagnostico_od="" 
					diagnostico_oi="" 
				/>
			</ingresos_especialidad>');
		}
		toXML_mio($xml, $nodo);
		
		
		
		
		//para la pestaña de prescripciones		
		$sql="SELECT p.id_prescripcion, CONCAT(monodroga, ' ',presentacion, ' ',concentracion) 'descrip', ";
		$sql.="v.id_vademecum, p.posologia, v.monodroga, v.concentracion, v.presentacion, DATE_FORMAT(fecha_prescripcion,'%d/%m/%Y') 'fecha_prescripcion' ";
		$sql.="FROM ingresos i ";
		$sql.="INNER JOIN ingresos_movimientos im USING(id_ingreso) ";
		$sql.="INNER JOIN prescripciones p USING(id_ingreso_movimiento) ";
		$sql.="INNER JOIN vademecum v USING(id_vademecum) ";		
		$sql.="WHERE i.id_ingreso='$id_ingreso' ";
		$sql.="ORDER BY fecha_prescripcion";	
		toXML($xml, $sql, "prescripcion");
		
		$sql="SELECT s.id_solicitudes, np.procedimiento 'descripcion', s.estado, s.resultados, id_practica, id_ingreso_movimiento_solicitud, DATE_FORMAT(fecha_solicitud,'%d/%m/%Y') as 'fecha_solicitud'  ";
		$sql.="FROM solicitudes s ";
		$sql.="INNER JOIN nomenclador_practicas np USING(id_practica) ";
		$sql.="INNER JOIN ingresos_movimientos im ON  s.id_ingreso_movimiento_solicitud = im.id_ingreso_movimiento ";
		$sql.="INNER JOIN ingresos i ON  im.id_ingreso = i.id_ingreso ";
		$sql.="WHERE i.id_ingreso='$id_ingreso' ";
		$sql.="ORDER BY fecha_solicitud";
		toXML($xml, $sql, "practica");
				
		$sql = "SELECT id_persona ";
		$sql.="FROM ingresos i ";
		$sql.="WHERE i.id_ingreso='$id_ingreso' ";
		$result = $mysqli->query($sql);
		
		if ($row = $result->fetch_array()){		
			$id_persona = $row['id_persona'];

			$sql="SELECT persona_id 'id_persona',persona_nombre 'apeynom', CASE p.persona_tipodoc WHEN 'D' THEN 'DNI' WHEN 'C' THEN 'LC' WHEN 'E' THEN 'LE' WHEN 'F' THEN 'CI' END as 'tipo_doc', persona_dni 'nrodoc', IF( persona_sexo = 'M', 'MASCULINO', 'FEMENINO' ) as 'sexo', ";		
			$sql.="IF (persona_fecha_nacimiento <> '0000-00-00',DATE_FORMAT(persona_fecha_nacimiento,'%d/%m/%Y'),'') as 'fechanac', ";
			$sql.="IF (persona_fecha_nacimiento <> '0000-00-00',(YEAR(CURRENT_DATE) - YEAR(persona_fecha_nacimiento))-(RIGHT(CURRENT_DATE,5) < RIGHT(persona_fecha_nacimiento,5)),'') 'edad', persona_domicilio 'domicilio', localidad, departamento, provincias ";
			$sql.="FROM $salud._personas p ";			
			$sql.="LEFT JOIN $salud._localidades l ON p.localidad_id = l.localidad_id ";
			$sql.="LEFT JOIN $salud._departamentos d USING(departamento_id) ";
			$sql.="LEFT JOIN $salud._provincias pr ON d.provincia_id = pr.provincias_id ";
			$sql.="WHERE p.persona_id='$id_persona'";	
			toXML($xml, $sql, "paciente");
			
			$sql="SELECT f.id_financiador, f.nombre, f.codigo_anssal, tf.descripcion ";
			$sql.="FROM $salud._personas p, ";		
			$sql.="$suram.padrones pa, ";
			$sql.="$suram.financiadores f, ";
			$sql.="$suram.tipos_financiadores tf ";
			$sql.="WHERE p.persona_id='$id_persona' ";
			$sql.="AND tf.id_tipo_financiador=f.id_tipo_financiador ";
			$sql.="AND f.id_financiador=pa.id_financiador ";
			//$sql.="AND (p.persona_dni=pa.nrodoc AND p.persona_tipodoc=pa.tipo_doc) ";
			$sql.="AND p.persona_dni=pa.nrodoc ";
			$sql.="ORDER BY nombre";
			toXML($xml, $sql, "cobertura");	
			
			$sql="SELECT 'V' as 'estado', id_vacunacion, id_dosis, CONCAT(nombre,' - dosis ',denominacion) as nombre, enfermedades, ";
			$sql.=" DATE_FORMAT(id_fecha,'%d/%m/%Y') 'fecha', id_persona ";
			$sql.="FROM $salud.027_vacunaciones ";
			$sql.="JOIN $salud.027_dosis USING(id_dosis) ";
			$sql.="JOIN $salud.027_vacunas USING(id_vacuna) ";
			$sql.="WHERE id_persona='$id_persona' ";
			$sql.="ORDER BY fecha";
			toXML($xml, $sql, "vacunaciones");
			
			$sql="SELECT 'V' as 'estado', a.id_antec_persona, descripcion, id_antecedente, antecedente, observaciones, persona_nombre as medico, ";
			$sql.="CASE accion WHEN 'A' THEN 'Agregó' WHEN 'B' THEN 'Quitó' WHEN 'M' THEN 'Modificó' END as 'accion', ";
			$sql.=" DATE_FORMAT(fecha,'%d/%m/%Y') 'fecha', a.id_persona, a.usuario ";
			$sql.="FROM $salud.027_antecedentes_personas a ";
			$sql.="JOIN $salud.027_antecedentes USING(id_antecedente) ";
			$sql.="JOIN $salud.027_tipo_antec USING(id_tipo_antec) ";
			$sql.="JOIN $salud._usuarios u ON a.usuario = u.SYSusuario ";
			$sql.="JOIN $salud._personas p ON u.id_persona = p.persona_id ";
			$sql.="WHERE a.id_persona='$id_persona' ";
			$sql.="ORDER BY fecha";
			toXML($xml, $sql, "antecedente");
			
			$sql="SELECT s.id_solicitudes, np.procedimiento 'descripcion', s.estado, s.resultados, id_practica, id_ingreso_movimiento_solicitud, DATE_FORMAT(fecha_solicitud,'%d/%m/%Y') as 'fecha_solicitud'  ";
			$sql.="FROM solicitudes s ";
			$sql.="INNER JOIN nomenclador_practicas np USING(id_practica) ";
			$sql.="INNER JOIN ingresos_movimientos im ON  s.id_ingreso_movimiento_solicitud = im.id_ingreso_movimiento ";
			$sql.="INNER JOIN ingresos i USING(id_ingreso) ";
			$sql.="WHERE i.id_persona='$id_persona' ";
			$sql.="AND (s.estado='S' OR s.estado='R') ";
			$sql.="ORDER BY fecha_solicitud";
			toXML($xml, $sql, "resultado");
		}		
		header('Content-Type: text/xml');
		echo $xml->asXML();
		break;
	}
	case 'guardar_datos':{

		$xml=new SimpleXMLElement('<rows/>');
		$xml2=new SimpleXMLElement('<rows/>');
		$xml_datos = loadXML($_REQUEST["xmlDatos"]);
		
		$xml_diagnosticos = $xml_datos->diagnosticos;
		$xml_datosconsulta = $xml_datos->datosconsulta;
		$xml_antecedentes = $xml_datos->antecedentes;
		$xml_prescripciones = $xml_datos->prescripciones;
		$xml_practicas = $xml_datos->practicas;
		$xml_resultados = $xml_datos->resultados;
		$xml_derivacion = $xml_datos->datosderivacion;
		$xml_vacunaciones = $xml_datos->vacunaciones;
		
		$xml_especialidad = $xml_datos->ingresos_especialidad;
		if ($xml_especialidad->asXML() != "") {
			//$json = json_encode($xml_especialidad);
			$json = $xml_especialidad->asXML();
			$sql = "INSERT ingresos_especialidad SET id_ingreso='" . $xml_datosconsulta->id_ingreso . "', id_especialidad='" . $xml_especialidad->ingresos_especialidad['id_especialidad'] . "', json='" . $json . "'";
			$sql.= " ON DUPLICATE KEY UPDATE id_especialidad='" . $xml_especialidad->ingresos_especialidad['id_especialidad'] . "', json='" . $json . "'";
			$mysqli->query($sql);
		}

		
		$sql = "UPDATE ingresos SET ";
		$sql.= XMLtoSQL($xml_datosconsulta,array('id_ingreso'));
		if ($xml_derivacion){$sql.= ", ". XMLtoSQL($xml_derivacion);}
		$sql.=", fecha_consulta_ingreso=NOW() WHERE id_ingreso='".$xml_datosconsulta->id_ingreso."'";
		toXML($xml, $sql, "upd_ingresos");
		
		if($xml_antecedentes){
			$sql="DELETE FROM $salud.027_antecedentes_personas  ";
			$sql.="WHERE id_persona='$xml_datosconsulta->id_persona'";
			toXML($xml, $sql, "del");
			
			foreach ($xml_antecedentes->antecedente as $antecedente){
				$sql=" INSERT INTO $salud.027_antecedentes_personas SET ";
	    		$sql.="id_antecedente='".$antecedente["id_antecedente"]."', ";
	    		$sql.="observaciones='".$antecedente["observaciones"]."', ";
	    		$sql.="accion='".(($antecedente["accion"]=='Quitó') ? 'B' : (($antecedente["accion"]=='Agregó') ? 'A' : 'M'))."', ";
	    		$sql.="fecha='".YYYYDM($antecedente["fecha"])."', ";
	    		//$sql.="usuario='".($antecedente["usuario"] ? $antecedente["usuario"] : $_SESSION['usuario'])."', ";
	    		$sql.="usuario='".($antecedente["usuario"] ? $antecedente["usuario"] : $_SESSION['SYSusuario'])."', ";
	    		$sql.="id_persona='".$xml_datosconsulta->id_persona."' ";
	    		toXML($xml, $sql, "antecedentes"); 
			}
		}
		
		if ($xml_vacunaciones){
			$sql="DELETE FROM $salud.027_vacunaciones  ";
			$sql.="WHERE id_persona='$xml_datosconsulta->id_persona'";
			toXML($xml, $sql, "del");
			
			foreach ($xml_vacunaciones->vacunaciones as $vacunacion){
				$sql=" INSERT INTO $salud.027_vacunaciones SET ";
	    		$sql.="id_dosis='".$vacunacion["id_dosis"]."', ";
	    		$sql.="id_fecha='".YYYYDM($vacunacion["fecha"])."', ";
	    		$sql.="id_persona='".$xml_datosconsulta->id_persona."' ";
	    		toXML($xml, $sql, "vacunaciones"); 
			}
		}
		
		$sql="SELECT id_ingreso_movimiento ";
		$sql.="FROM ingresos_movimientos ";
		$sql.="INNER JOIN ingresos USING(id_ingreso) ";
		$sql.="WHERE id_ingreso='".$xml_datosconsulta->id_ingreso."' ";
		$row = $mysqli->query($sql);
		if ($rs = $row->fetch_array()){
			$id_ingreso_movimiento = $rs['id_ingreso_movimiento'];	
			//elimino diagnosticos
			if($xml_diagnosticos){
				$sql="DELETE FROM items_diagnosticos ";
				$sql.="WHERE id_ingreso_movimiento='".$id_ingreso_movimiento."'";
				toXML($xml, $sql, "del");
			}
			//elimino practicas
			if($xml_practicas){
				$sql="DELETE FROM solicitudes ";
				$sql.="WHERE id_ingreso_movimiento_solicitud='".$id_ingreso_movimiento."'";
				toXML($xml, $sql, "del");
			}
			if($xml_prescripciones){
				//elimino prescripciones
				$sql="DELETE FROM prescripciones ";
				$sql.="WHERE id_ingreso_movimiento='".$id_ingreso_movimiento."'";
				toXML($xml, $sql, "del");
			}
		}else{
			//agrego un nuevo movimiento
			$sql=" INSERT INTO ingresos_movimientos SET ";
    		$sql.="fecha_movimiento_egreso=NOW(), ";
    		$sql.="fecha_movimiento_ingreso=NOW(), ";
    		$sql.="id_area_servicio_ingreso='".$_SESSION['usuario_servicio_id']."', ";
    		$sql.="id_ingreso='".$xml_datosconsulta->id_ingreso."' ";
    		toXML($xml2, $sql, "ingreso_movimiento");    		
    		$id_ingreso_movimiento = $mysqli->insert_id;
		}
		
		if($xml_diagnosticos){
			foreach ($xml_diagnosticos->diagnostico as $diagnostico) {
	    		$sql=" INSERT INTO items_diagnosticos SET ";
	    		$sql.="id_diagnostico='".$diagnostico["id_diagnostico"]."', ";
	    		$sql.="tipo_diagnostico='".$diagnostico["tipo_diagnostico"]."', ";
	    		$sql.="id_ingreso_movimiento='".$id_ingreso_movimiento."' ";
	    		toXML($xml, $sql, "diagnosticos");
			}
		}
		
		if($xml_practicas){
			foreach ($xml_practicas->practica as $practica) {
	    		$sql=" INSERT INTO solicitudes SET ";
	    		$sql.="id_practica='".$practica["id_practica"]."', ";	    		
	    		$sql.="fecha_solicitud=".( (strlen($practica["fecha_solicitud"]) == 0 ? "NOW()" : "'" . YYYYDM($practica["fecha_solicitud"]) . "'" ) ).", ";	    		
	    		$sql.="estado=".( (strlen($practica["resultados"]) == 0 ? "'S'" : "'R'" ) ).", ";
	    		$sql.="id_ingreso_movimiento_solicitud='".$id_ingreso_movimiento."' ";
	    		if (strlen($practica["resultados"]) != 0) {
	    			$sql.=",resultados ='".$practica["resultados"]."', ";
	    			$sql.="id_ingreso_movimiento_resultado='".$id_ingreso_movimiento."' ";
	    		}	    			
	    		toXML($xml, $sql, "practicas");
			}
		}
		
		if($xml_prescripciones){
			foreach ($xml_prescripciones->prescripcion as $prescripcion) {
	    		$sql=" INSERT INTO prescripciones SET ";
	    		$sql.="id_vademecum ='".$prescripcion["id_vademecum"]."', ";
	    		$sql.="posologia='".$prescripcion["posologia"]."', ";	    		
	    		$sql.="fecha_prescripcion=".( (strlen($prescripcion["fecha_prescripcion"]) == 0 ? "NOW()" : "'" . YYYYDM($prescripcion["fecha_prescripcion"]) . "'" ) ).", ";	    			    		
	    		$sql.="id_ingreso_movimiento='".$id_ingreso_movimiento."' ";
	    		toXML($xml, $sql, "prescripciones");
			}
		}
		
		if($xml_resultados){
			foreach ($xml_resultados->resultado as $resultado) {
				if($resultado['estado']=='R'){
		    		$sql=" UPDATE solicitudes SET ";
		    		$sql.="resultados ='".$resultado["resultados"]."', ";
		    		$sql.="estado='R', ";
		    		$sql.="id_ingreso_movimiento_resultado='".$id_ingreso_movimiento."' ";
		    		$sql.="WHERE id_solicitudes='".$resultado["id_solicitudes"]."' ";
		    		toXML($xml, $sql, "resultados");
				}
			}
		}
   		$sql=" UPDATE turnos INNER JOIN ingresos USING(id_turno) SET ";
   		$sql.="turnos.estado_turno='AT' ";
   		$sql.="WHERE id_ingreso='".$xml_datosconsulta->id_ingreso."' ";
   		toXML($xml, $sql, "turno");
				
		header('Content-Type: text/xml');
		echo $xml->asXML();
		break;
	}
	
	
	case 'leer_especialidad':{
		$sql="SELECT id_especialidad FROM _servicios_especialidades WHERE id_servicio=" . $_REQUEST["id_servicio"];
		$rs = $mysqli->query($sql);
		$row = $rs->fetch_object();
		
		echo $row->id_especialidad;
		break;
	}
}

?>