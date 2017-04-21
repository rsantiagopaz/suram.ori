<?php
include("../control_acceso_flex.php");
include("../rutinas.php");

$xmlIngreso=new SimpleXMLElement('<rows/>');
$xmlPaciente=new SimpleXMLElement('<rows/>');
$xmlVacunas=new SimpleXMLElement('<rows/>');
$xmlAntecedentes=new SimpleXMLElement('<rows/>');
$xmlPrescripciones=new SimpleXMLElement('<rows/>');
$xmlPracticas=new SimpleXMLElement('<rows/>');
$xmlResultados=new SimpleXMLElement('<rows/>');
$xmlDiagnosticos=new SimpleXMLElement('<rows/>');
$xmlNacimientos=new SimpleXMLElement('<rows/>');
$xmlCoberturas=new SimpleXMLElement('<rows/>');
		
		

$sql="SELECT IF (i.embarazo_fechaterminacion IS NULL,'00/00/0000',DATE_FORMAT(i.embarazo_fechaterminacion,'%d/%m/%Y')) 'embarazo_fechaterminacion', ";
$sql.="IF (i.embarazo_edad_gestacional IS NULL,'0',i.embarazo_edad_gestacional) 'embarazo_edad_gestacional', i.embarazo_paridad, ";
$sql.="embarazo_tipo_parto, IF(embarazo_tipo_parto='S','Simple','Multiple') 'embarazo_tipo_parto_text', ";
$sql.="i.traum_comoseprodujo, tp.descripcion 'tp_desc', tl.descripcion 'tl_desc', ";
$sql.="i.embarazada , i.traumatismo, i.id_nivel, ";
$sql.="i.id_tipo_ingreso, ti.descripcion 'tipo_ingreso', motivo, i.observaciones, ";
$sql.="IF (i.primera_vez='S','Inicial','Ulterior') 'primera_vez', ";
$sql.="peso, talla, perimetro, ";
$sql.="IF (i.peso_edad='0','10-90',IF (i.peso_edad='+','+90','-90')) 'peso_edad', ";
$sql.="IF (i.talla_edad='0','10-90',IF (i.talla_edad='+','+90','-90')) 'talla_edad', ";
$sql.="IF (i.perc_perimetro='0','+2ds-2ds',IF (i.perc_perimetro='+','+2ds','-2ds')) 'perc_perimetro', ";
$sql.="embarazada, trimestre, tension_arterial, ";
$sql.="derivacion, tipo_derivacion, e.especialidad ";
$sql.="FROM ingresos_movimientos im ";
$sql.="INNER JOIN ingresos i USING(id_ingreso) ";
$sql.="LEFT JOIN traumatismo_lugar tl USING(id_traum_lugar) ";
$sql.="LEFT JOIN traumatismo_producido_por tp ON tp.id_trau_producido=i.id_traum_producido ";
$sql.="LEFT JOIN especialidades e ON e.id_especialidad=i.id_especialidad ";
$sql.="LEFT JOIN tipos_ingresos ti ON ti.id_tipo_ingreso=i.id_tipo_ingreso ";
$sql.="WHERE im.id_ingreso_movimiento='$id_ingreso_movimiento' ";
toXML($xmlIngreso, $sql, "datosingreso");

$sql="SELECT p.id_prescripcion, CONCAT(monodroga, ' ',presentacion, ' ',concentracion) 'descrip', ";
$sql.="v.id_vademecum, p.posologia, v.monodroga, v.concentracion, v.presentacion, DATE_FORMAT(fecha_prescripcion,'%d/%m/%Y') 'fecha_prescripcion' ";
$sql.="FROM ingresos_movimientos im ";
$sql.="INNER JOIN prescripciones p USING(id_ingreso_movimiento) ";
$sql.="INNER JOIN vademecum v USING(id_vademecum) ";		
$sql.="WHERE im.id_ingreso_movimiento='$id_ingreso_movimiento' ";
$sql.="ORDER BY fecha_prescripcion";	
toXML($xmlPrescripciones, $sql, "prescripcion");

$sql="SELECT id.id_item_diagnostico, id.id_ingreso_movimiento, id.id_diagnostico, cie10.descripcion4 'descripcion', tipo_diagnostico ";
$sql.="FROM ingresos_movimientos im ";
$sql.="INNER JOIN items_diagnosticos id USING(id_ingreso_movimiento) ";
$sql.="INNER JOIN cie10 USING(id_diagnostico) ";		
$sql.="WHERE im.id_ingreso_movimiento='$id_ingreso_movimiento' ";
$sql.="ORDER BY cie10.descripcion4";	
toXML($xmlDiagnosticos, $sql, "diagnostico");

$sql="SELECT ib.id_ingreso_bebe, ib.id_ingreso, ib.peso, ";
$sql.="ib.condicion_alnacer, IF(ib.condicion_alnacer='V','Nacido Vivo','Defuncion Fetal') 'condicion_alnacer_text', ";
$sql.="ib.terminacion, IF(ib.terminacion='V','Vaginal','Cesárea') 'terminacion_text', ";
$sql.="ib.sexo,(CASE WHEN sexo='M' THEN 'MASCULINO' WHEN sexo='F' THEN 'FEMENINO' WHEN sexo='I' THEN 'INDEFINIDO' END) 'sexo_text' ";
$sql.="FROM ingresos_movimientos im ";
$sql.="INNER JOIN ingresos i USING(id_ingreso) ";
$sql.="INNER JOIN ingresos_bebes ib USING(id_ingreso) ";		
$sql.="WHERE im.id_ingreso_movimiento='$id_ingreso_movimiento' ";
$sql.="ORDER BY peso";	
toXML($xmlNacimientos, $sql, "nacimiento");



$sql="SELECT s.id_solicitudes, np.procedimiento 'descripcion', s.estado, s.resultados, id_practica, id_ingreso_movimiento_solicitud, DATE_FORMAT(fecha_solicitud,'%d/%m/%Y') as 'fecha_solicitud'  ";
$sql.="FROM solicitudes s ";
$sql.="INNER JOIN nomenclador_practicas np USING(id_practica) ";
$sql.="INNER JOIN  ingresos_movimientos im ON  s.id_ingreso_movimiento_solicitud = im.id_ingreso_movimiento ";
$sql.="WHERE im.id_ingreso_movimiento='$id_ingreso_movimiento' ";
$sql.="ORDER BY fecha_solicitud";
toXML($xmlPracticas, $sql, "practica");
				
$query = "SELECT id_persona FROM ingresos INNER JOIN ingresos_movimientos im USING(id_ingreso) ";
$query.="WHERE im.id_ingreso_movimiento='$id_ingreso_movimiento' ";
$result = mysql_query($query);

if ($row = mysql_fetch_array($result)){						
	$id_persona = $row['id_persona'];

	$sql="SELECT persona_id 'id_persona',persona_nombre 'apeynom', CASE p.persona_tipodoc WHEN 'D' THEN 'DNI' WHEN 'C' THEN 'LC' WHEN 'E' THEN 'LE' WHEN 'F' THEN 'CI' END as 'tipo_doc', persona_dni 'nrodoc', IF( persona_sexo = 'M', 'MASCULINO', 'FEMENINO' ) as 'sexo', ";
	$sql.="IF (persona_fecha_nacimiento <> '0000-00-00',DATE_FORMAT(persona_fecha_nacimiento,'%d/%m/%Y'),'') as 'fechanac', ";
	$sql.="IF (persona_fecha_nacimiento <> '0000-00-00',(YEAR(CURRENT_DATE)-YEAR(persona_fecha_nacimiento))-(RIGHT(CURRENT_DATE,5)<RIGHT(persona_fecha_nacimiento,5)),'') 'edad', persona_domicilio 'domicilio', localidad, departamento, provincias ";
	$sql.="FROM $salud._personas p ";			
	$sql.="LEFT JOIN $salud._localidades l ON p.localidad_id = l.localidad_id ";
	$sql.="LEFT JOIN $salud._departamentos d USING(departamento_id) ";
	$sql.="LEFT JOIN $salud._provincias pr ON d.provincia_id = pr.provincias_id ";
	$sql.="WHERE p.persona_id='$id_persona'";	
	toXML($xmlPaciente, $sql, "paciente");
	
	$sql="SELECT f.id_financiador, f.nombre, f.codigo_anssal, tf.descripcion ";
	$sql.="FROM $salud._personas p, ";		
	$sql.="$suram.padrones pa, ";
	$sql.="$suram.financiadores f, ";
	$sql.="$suram.tipos_financiadores tf ";
	$sql.="WHERE p.persona_id='$id_persona' ";
	$sql.="AND tf.id_tipo_financiador=f.id_tipo_financiador ";
	$sql.="AND f.id_financiador=pa.id_financiador ";
	$sql.="AND (p.persona_dni=pa.nrodoc AND p.persona_tipodoc=pa.tipo_doc) ";
	$sql.="ORDER BY nombre";
	toXML($xmlCoberturas, $sql, "cobertura");	
	
	$sql="SELECT 'V' as 'estado', id_vacunacion, id_dosis, CONCAT(nombre,' - dosis ',denominacion) as nombre, enfermedades, ";
	$sql.=" DATE_FORMAT(id_fecha,'%d/%m/%Y') 'fecha' ";
	$sql.="FROM $salud.027_vacunaciones ";
	$sql.="JOIN $salud.027_dosis USING(id_dosis) ";
	$sql.="JOIN $salud.027_vacunas USING(id_vacuna) ";
	$sql.="WHERE id_persona='$id_persona' ";
	$sql.="ORDER BY fecha";
	toXML($xmlVacunas, $sql, "vacunaciones");
	
	$sql="SELECT s.id_solicitudes, np.procedimiento 'descripcion', s.estado, s.resultados, id_practica, id_ingreso_movimiento_solicitud, DATE_FORMAT(fecha_solicitud,'%d/%m/%Y') as 'fecha_solicitud'  ";
	$sql.="FROM solicitudes s ";
	$sql.="INNER JOIN nomenclador_practicas np USING(id_practica) ";
	$sql.="INNER JOIN ingresos_movimientos im ON  s.id_ingreso_movimiento_solicitud = im.id_ingreso_movimiento ";
	$sql.="INNER JOIN ingresos i USING(id_ingreso) ";
	$sql.="WHERE i.id_persona='$id_persona' ";
	$sql.="AND s.estado='S' ";
	$sql.="ORDER BY fecha_solicitud";
	toXML($xmlResultados, $sql, "resultado");
	
	$sql="SELECT 'V' as 'estado', a.id_antec_persona, descripcion, id_antecedente, antecedente, observaciones, persona_nombre as medico, ";
	$sql.="CASE accion WHEN 'A' THEN 'Alta' WHEN 'B' THEN 'Baja' WHEN 'M' THEN 'Modificación' END as 'accion', ";
	$sql.=" DATE_FORMAT(fecha,'%d/%m/%Y') 'fecha' ";
	$sql.="FROM $salud.027_antecedentes_personas a ";
	$sql.="JOIN $salud.027_antecedentes USING(id_antecedente) ";
	$sql.="JOIN $salud.027_tipo_antec USING(id_tipo_antec) ";
	$sql.="JOIN $salud._usuarios u ON a.usuario = u.SYSusuario ";
	$sql.="JOIN $salud._personas p ON u.id_persona = p.persona_id ";
	$sql.="WHERE a.id_persona='$id_persona' ";
	$sql.="ORDER BY fecha";
	toXML($xmlAntecedentes, $sql, "antecedente");	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
body{font-size:13px;font-family: times New Roman;margin: 0 auto; width: 850px;}
h1,h2,h3{text-align:center;background-color:#999999;}
h1{font-size:20px;}
h2{font-size:18px;}
h3{font-size:15px;}
table{width:100%; border-collapse: collapse;}
th{font-weight: bold;text-align:center;background-color:#CCCCCC;}
.title{background-color:#CCCCCC;font-weight:bold;}
</style>
</head>
<body>
<h1>Resumen de Atencion a Paciente</h1>
<h2>Datos del Paciente</h2>
<table>
	<tr>
		<td class="title">Nombre:</td>
		<td><?php echo $xmlPaciente->paciente['apeynom']; ?></td>
		<td class="title">Tipo Doc:</td>
		<td><?php echo $xmlPaciente->paciente['tipo_doc']; ?></td>
		<td class="title">Nro Doc:</td>
		<td><?php echo $xmlPaciente->paciente['nrodoc']; ?></td>
	</tr>
	<tr>
		<td class="title">Fecha de Nacimiento:</td>
		<td><?php echo $xmlPaciente->paciente['fechanac']; ?></td>
		<td class="title">Sexo:</td>
		<td><?php echo $xmlPaciente->paciente['sexo']; ?></td>
		<td class="title">Edad:</td>
		<td><?php echo $xmlPaciente->paciente['edad']; ?></td>
	</tr>
	<tr>
		<td class="title">Domicilio:</td>
		<td><?php echo $xmlPaciente->paciente['domicilio']; ?></td>
		<td class="title">Localidad:</td>
		<td><?php echo $xmlPaciente->paciente['localidad']; ?></td>
		<td class="title">Departamento:</td>
		<td><?php echo $xmlPaciente->paciente['departamento']; ?></td>
	</tr>
</table>
<?php

	if($xmlIngreso->datosingreso['id_nivel']==1){?>
	<h2>Datos de la Consulta</h2>
		<table>
			<tr>
				<td class="title">Tipo de Ingreso:</td>
				<td><?php echo $xmlIngreso->datosingreso['tipo_ingreso']; ?></td>
				<td class="title">Consulta:</td>
				<td><?php echo $xmlIngreso->datosingreso['primera_vez']; ?></td>
			</tr>
			<tr>
				<td class="title">Motivo de la Consulta</td>
				<td colspan="3"><?php echo $xmlIngreso->datosingreso['motivo']; ?></td>
			</tr>
			<tr>
				<td class="title">Observaciones</td>
				<td colspan="3"><?php echo $xmlIngreso->datosingreso['observaciones']; ?></td>
			</tr>
			<?php if($xmlPaciente->paciente['edad'] <= 12){?>
			<tr>
				<td class="title">Peso:</td>
				<td><?php echo $xmlIngreso->datosingreso['peso']; ?></td>
				<td class="title">Talla:</td>
				<td><?php echo $xmlIngreso->datosingreso['talla']; ?></td>
				<td class="title">Perimero</td>
				<td><?php echo $xmlIngreso->datosingreso['perimetro']; ?></td>
			</tr>
			<tr>
				<td class="title">Perc. Talla:</td>
				<td><?php echo $xmlIngreso->datosingreso['peso_edad']; ?></td>
				<td class="title">Perc. Peso:</td>
				<td><?php echo $xmlIngreso->datosingreso['talla_edad']; ?></td>
				<td class="title">Perc. Perimero</td>
				<td><?php echo $xmlIngreso->datosingreso['perc_perimetro']; ?></td>
			</tr>
			</table>
			<?php } ?>
			<?php if($xmlIngreso->datosingreso['embarazada'] == 'S'){?>
			<tr>
				<td class="title">Trimestre:</td>
				<td><?php echo $xmlIngreso->datosingreso['trimestre']; ?></td>
				<td class="title">Presion Arterial:</td>
				<td><?php echo $xmlIngreso->datosingreso['tension_arterial']; ?></td>
			</tr>
			<?php } ?>
		</table>
	<?php }
	if($xmlIngreso->datosingreso['id_nivel']==2){
		echo '<h2>Datos de Internacion</h2>';	
	}
	if($xmlDiagnosticos->diagnostico){?>
		<h3>Diagnosticos</h3>
		<table>	
			<tr>
				<th>Descripcion</th>
				<th>Tipo</th>
			</tr>
		<?php 		
		foreach ($xmlDiagnosticos->diagnostico as $diag){?>
			<tr>
				<td><?php echo $diag['descripcion']; ?></td>
				<td align="center"><?php echo $diag['tipo_diagnostico']; ?></td>
			</tr> 
		<?php } ?>
		</table>
	<?php }
	if($xmlIngreso->datosingreso['id_nivel']==2){
		$trau = ($xmlIngreso->datosingreso['traumatismo'] == 'N') ? 'No' : 'Si';
		$emb = ($xmlIngreso->datosingreso['embarazada'] == 'N') ? 'No' : 'Si';
		?>
		<h3>Datos Sobre Traumatismos</h3>	
		<table>
			<tr>
				<td>Presenta Traumatismo?:</td>
				<td colspan="3"><?php echo $trau; ?></td>
			</tr>
			<?php if ($xmlIngreso->datosingreso['traumatismo'] == 'S'){?>
			<tr>
				<td>Traumatismo Producido Por:</td>
				<td><?php echo $xmlIngreso->datosingreso['tp_desc']; ?></td>
				<td>Traumatismo Lugar:</td>
				<td><?php echo $xmlIngreso->datosingreso['tl_desc']; ?></td>
			</tr>
			<tr>
				<td>Descripcion:</td>
				<td colspan="3"><?php echo $xmlIngreso->datosingreso['traum_comoseprodujo']; ?></td>
			</tr>
			<?php } ?>
		</table>
		<h3>Datos de Embarazo</h3>	
		<table width="500">
			<tr>
				<td>Embarazada?:</td>
				<td colspan="7"><?php echo $emb; ?></td>
			</tr>
			<?php if ($xmlIngreso->datosingreso['embarazada'] == 'S'){?>
			<tr>
				<td>Fecha Term.:</td>
				<td><?php echo $xmlIngreso->datosingreso['embarazo_fechaterminacion']; ?></td>
				<td>Edad Gest.:</td>
				<td><?php echo $xmlIngreso->datosingreso['embarazo_edad_gestacional']; ?></td>
				<td>Tipo:</td>
				<td><?php echo $xmlIngreso->datosingreso['embarazo_tipo_parto_text']; ?></td>
				<td>Paridad:</td>
				<td><?php echo $xmlIngreso->datosingreso['embarazo_paridad']; ?></td>
			</tr>
			<tr>
				<td colspan="8">
				<table>	
					<tr>
						<th>Peso</th>
						<th>Cond. al Nacer</th>
						<th>Terminacion</th>
						<th>Sexo</th>
					</tr>
				<?php 		
				foreach ($xmlNacimientos->nacimiento as $nac){?>
					<tr>
						<td align="center"><?php echo $nac['peso']; ?></td>
						<td><?php echo $nac['condicion_alnacer_text']; ?></td>
						<td><?php echo $nac['terminacion_text']; ?></td>
						<td><?php echo $nac['sexo_text']; ?></td>
					</tr> 
				<?php } ?>
				</table>
				</td>
			</tr>
			<?php } ?>
		</table>
	<?php }

	if($xmlPrescripciones->prescripcion){?>
		<h3>Prescripciones</h3>
		<table>	
			<tr>
				<th>Fecha</th>
				<th>Monodroga</th>
				<th>Presentacion</th>
				<th>Concentracion</th>
				<th>Posologia</th>
			</tr>
		<?php 		
		foreach ($xmlPrescripciones->prescripcion as $prec){?>
			<tr>
				<td align="center"><?php echo $prec['fecha_prescripcion']; ?></td>
				<td><?php echo $prec['monodroga']; ?></td>
				<td><?php echo $prec['presentacion']; ?></td>
				<td><?php echo $prec['concentracion']; ?></td>
				<td><?php echo $prec['posologia']; ?></td>
			</tr> 
		<?php } ?>
		</table>
	<?php }
	
	if($xmlPracticas->practica){ ?>
		<h3>Practicas y Procedimientos</h3>
		<table>	
			<tr>
				<th>Fecha de Solicitud</th>
				<th>Practica</th>
				<th>Resultado</th>
			</tr>
		<?php 		
		foreach ($xmlPracticas->practica as $prac){?>
			<tr>
				<td align="center"><?php echo $prac['fecha_solicitud']; ?></td>
				<td><?php echo $prac['descripcion']; ?></td>
				<td><?php echo $prac['resultados']; ?></td>
			</tr> 
		<?php } ?>
		</table>
	<?php }

	if($xmlAntecedentes->antecedente){?>
		<h3>Antecedentes</h3>
		<table>	
			<tr>
				<th>Tipo de Antecedente</th>
				<th>Descripcion</th>
				<th>Observaciones</th>
				<th>Fecha</th>
				<th>Medico</th>
				<th>Accion</th>
			</tr>
		<?php 		
		foreach ($xmlAntecedentes->antecedente as $ant){?>
			<tr>
				<td><?php echo $ant['descripcion']; ?></td>
				<td><?php echo $ant['antecedente']; ?></td>
				<td><?php echo $ant['observaciones']; ?></td>
				<td align="center"><?php echo $ant['fecha']; ?></td>
				<td><?php echo $ant['medico']; ?></td>
				<td><?php echo $ant['accion']; ?></td>
			</tr> 
		<?php } ?>
		</table>
	<?php }
	
	if($xmlVacunas->vacunaciones){?>
		<h3>Vacunaciones</h3>
		<table>	
			<tr>
				<th>Vacuna</th>
				<th>Enfermedades</th>
				<th>Fecha</th>
			</tr>
		<?php 		
		foreach ($xmlVacunas->vacunaciones as $vac){?>
			<tr>
				<td><?php echo $vac['nombre']; ?></td>
				<td><?php echo $vac['enfermedades']; ?></td>
				<td align="center"><?php echo $vac['fecha']; ?></td>
			</tr> 
		<?php } ?>
		</table>
	<?php }
?>
</body>
</html>