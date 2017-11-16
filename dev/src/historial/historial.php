<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

switch ($_REQUEST['rutina'])
{
	case 'traer_historial': {
		$xml=new SimpleXMLElement('<rows/>');
		
		$sql="SELECT id_ingreso, motivo, observaciones, ";
			$sql.="IF (id_ingreso IN (SELECT id_ingreso FROM ingresos_movimientos JOIN prescripciones USING(id_ingreso_movimiento)),'SI','NO') 'tiene_prescripciones', ";
			$sql.="IF (id_ingreso IN (SELECT id_ingreso FROM ingresos_movimientos JOIN solicitudes ON id_ingreso_movimiento = id_ingreso_movimiento_solicitud),'SI','NO') 'tiene_practicas', ";
			$sql.=" DATE_FORMAT(fecha_consulta_ingreso,'%d/%m/%Y') 'fecha' ";
			$sql.="FROM ingresos ";			
			$sql.="WHERE id_persona='$id_persona' ";
			$sql.="ORDER BY fecha";
			//toXML($xml, $sql, "ingresos");
			
			$sql="SELECT id_ingreso, id_nivel, ";
			$sql.="DATE_FORMAT(fecha_consulta_ingreso,'%d/%m/%Y') 'fecha', organismo_area 'establecimiento' ";
			$sql.="FROM ingresos JOIN $salud._organismos_areas USING(organismo_area_id) ";			
			$sql.="WHERE id_persona='$id_persona' ";
			$sql.="ORDER BY fecha_consulta_ingreso DESC";
						
  			$result = $mysqli->query($sql);
  			
  			$nodo=$xml->addChild("ingresos2");
  			
            while($row = $result->fetch_array())
           	{
           		$id_ingreso = $row['id_ingreso'];
           		
           		$nodo2=$nodo->addChild("item_ingreso");				
				foreach($row as $key => $value) {						 
					if (!is_numeric($key)) $nodo2->addAttribute($key, $value);
				}          		
           		
           		$sql="SELECT im.id_ingreso_movimiento, ";
				$sql.="DATE_FORMAT(fecha_movimiento_ingreso,'%d/%m/%Y') 'fecha_movimiento_ingreso', denominacion 'servicio', ";
				$sql.="IF (fecha_movimiento_egreso IS NULL,'',DATE_FORMAT(fecha_movimiento_egreso,'%d/%m/%Y')) 'fecha_egreso', ";
//				$sql.="IF (id_ingreso_movimiento IN(SELECT id_ingreso_movimiento FROM items_diagnosticos),(SELECT descripcion4 FROM items_diagnosticos JOIN cie10 USING(id_diagnostico) WHERE tipo_diagnostico = 'P' LIMIT 1)," .
	//					"'') 'diagnostico' ";
				$sql.="IF (descripcion4 IS NULL,'',descripcion4) 'diagnostico' ";
				
				$sql.="FROM ingresos_movimientos im ";
				$sql.="INNER JOIN $salud._servicios s ON im.id_area_servicio_ingreso = s.id_servicio ";				
				$sql.="LEFT JOIN items_diagnosticos i_d ON ((im.id_ingreso_movimiento = i_d.id_ingreso_movimiento) AND (i_d.tipo_diagnostico = 'P')) ";
				$sql.="LEFT JOIN cie10 ON cie10.id_diagnostico=i_d.id_diagnostico ";
				$sql.="WHERE id_ingreso='$id_ingreso' ";
				$sql.="ORDER BY fecha_movimiento_ingreso";
				
				$result2 = $mysqli->query($sql);
				
				while($row2 = $result2->fetch_array())
				{
					$nodo3=$nodo2->addChild("servicio");				
					foreach($row2 as $key => $value) {						 
						if (!is_numeric($key)) $nodo3->addAttribute($key, $value);
					}	
				}
           	}	
		
		toXML($xml, $sql, "cobertura");
		header('Content-Type: text/xml');
		echo $xml->asXML();
	
		break;
	}
	
}
?>