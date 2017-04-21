<?php
include("../control_acceso_flex.php");
include("../rutinas.php");
require($SYSpathraiz.'fpdf17/mc_table/mc_table.php');

ini_set('memory_limit', '256M');
set_time_limit(900);

class PDF extends PDF_MC_Table
{	
	var $xmlPaciente;
	//Page header
	function Header()
	{
	    //Title	    
	    $this->SetFont('Arial','',14);
	    $this->Cell(0,6,'Historia Clinica',0,1,'C');	    
	    $this->Ln(10);	    
	    //Ensure table header is output
	    parent::Header();
	}
	
	//Page footer
	function Footer()
	{
	    //Position at 1.5 cm from bottom
	    $this->SetY(-15);
	    //Arial italic 8
	    $this->SetFont('Arial','I',8);
	    //Page number
	    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
	
	//Table header
	function SetTableHeader($header)
	{
		$this->header = $header;
	}
	
	function PrintTableHeader()
	{
		$this->SetFont('','B');
		$this->Row($this->header);
		$this->SetFont('');
	}
	
	// Cargar los datos
	function LoadData()
	{
		$salud = $this->salud;
		$suram = $this->suram;
		$xmlPaciente=new SimpleXMLElement('<rows/>');
		$xmlVacunas=new SimpleXMLElement('<rows/>');
		$xmlAntecedentes=new SimpleXMLElement('<rows/>');
		$xmlDiagnosticos=new SimpleXMLElement('<rows/>');
		$xmlResultados=new SimpleXMLElement('<rows/>');
		$xmlCoberturas=new SimpleXMLElement('<rows/>');
						
		$query = "SELECT id_persona FROM ingresos ";
		$query.="WHERE id_ingreso='".$_REQUEST['id_ingreso']."'";
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
			
			$sql="SELECT id.id_item_diagnostico, id.id_ingreso_movimiento, id.id_diagnostico, cie10.descripcion4 'descripcion', tipo_diagnostico ";
			$sql.="FROM ingresos_movimientos im ";
			$sql.="INNER JOIN items_diagnosticos id USING(id_ingreso_movimiento) ";
			$sql.="INNER JOIN cie10 USING(id_diagnostico) ";
			$sql.="INNER JOIN ingresos ON im.id_ingreso = ingresos.id_ingreso ";		
			$sql.="WHERE id_persona='$id_persona' ";
			$sql.="ORDER BY cie10.descripcion4";	
			toXML($xmlDiagnosticos, $sql, "diagnostico");
			
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
			$this->id_persona = $id_persona;			
			$this->xmlPaciente = $xmlPaciente;
			$this->xmlAntecedentes = $xmlAntecedentes;
		}
	}
	
	// Una tabla más completa
	function ImprovedTable($header, $data)
	{
		// Anchuras de las columnas		
		// Datos
		foreach($data as $row)
		{
			$this->Row(array($row[0],$row[1],$row[2],$row[3]));
		}		
	}		
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->salud = $salud;
$pdf->suram = $suram;

// Carga de datos
$data = $pdf->LoadData();

$pdf->AddPage();

$pdf->SetFont('Arial','',6);
$pdf->SetFont('','B');
$pdf->Cell(0,6,'DATOS DEL PACIENTE',0,1,'C');
$pdf->SetWidths(array(35,20,20,20,15,10,30,20,20));
$pdf->Row(array('Nombre','Tipo Doc','Nro Doc','Fecha Nac','Sexo','Edad','Domicilio','Localidad','Departamento'));
$pdf->SetFont('');
$pdf->Row(array($pdf->xmlPaciente->paciente['apeynom'],$pdf->xmlPaciente->paciente['tipo_doc'],$pdf->xmlPaciente->paciente['nro_doc'],
	            $pdf->xmlPaciente->paciente['fecha_nac'],$pdf->xmlPaciente->paciente['sexo'],$pdf->xmlPaciente->paciente['edad'],
				$pdf->xmlPaciente->paciente['domicilio'],$pdf->xmlPaciente->paciente['localidad'],$pdf->xmlPaciente->paciente['departamento']));
					

if ($pdf->xmlAntecedentes->antecedente) {
	$pdf->SetFont('','B');
	$pdf->Cell(0,6,'ANTECEDENTES',0,1,'C');

	$pdf->SetWidths(array(35,40,45,20,40,10));
	$pdf->Row(array('Tipo de Antecedente','Descripcion','Observaciones','Fecha','Medico','Accion'));
	$pdf->SetFont('');
	
	foreach ($pdf->xmlAntecedentes->antecedente as $ant) {
		$pdf->Row(array(utf8_decode($ant['descripcion']),$ant['antecedente'],$ant['observaciones'],$ant['fecha'],$ant['medico'],$ant['accion']));
	}
}

$sql="SELECT id_ingreso, id_nivel, ";
$sql.="DATE_FORMAT(fecha_consulta_ingreso,'%d/%m/%Y') 'fecha', organismo_area 'establecimiento' ";
$sql.="FROM ingresos JOIN $salud._organismos_areas USING(organismo_area_id) ";			
$sql.="WHERE id_persona='$pdf->id_persona' ";
$sql.="ORDER BY fecha_consulta_ingreso DESC";

$result = mysql_query($sql);

$pdf->SetFont('','B');
$pdf->Cell(0,6,'INGRESOS',0,1,'C');

while ($row = mysql_fetch_array($result)) {	
	$pdf->SetWidths(array(30,35));
	$pdf->SetFont('','B');
	$pdf->Row(array('Establecimiento',$row['establecimiento']));
	$pdf->SetFont('');
	$pdf->Ln(1);
	
	$sql="SELECT im.id_ingreso_movimiento, ";
	$sql.="DATE_FORMAT(fecha_movimiento_ingreso,'%d/%m/%Y') 'fecha_movimiento_ingreso', denominacion 'servicio', ";
	$sql.="IF (fecha_movimiento_egreso IS NULL,'',DATE_FORMAT(fecha_movimiento_egreso,'%d/%m/%Y')) 'fecha_egreso', ";
	$sql.="IF (descripcion4 IS NULL,'',descripcion4) 'diagnostico' ";								
	$sql.="FROM ingresos_movimientos im ";
	$sql.="INNER JOIN $salud._servicios s ON im.id_area_servicio_ingreso = s.id_servicio ";				
	$sql.="LEFT JOIN items_diagnosticos i_d ON ((im.id_ingreso_movimiento = i_d.id_ingreso_movimiento) AND (i_d.tipo_diagnostico = 'P')) ";
	$sql.="LEFT JOIN cie10 ON cie10.id_diagnostico=i_d.id_diagnostico ";
	$sql.="WHERE id_ingreso='".$row['id_ingreso']."' ";
	$sql.="ORDER BY fecha_movimiento_ingreso";
	
	$result2 = mysql_query($sql);
				
	while($row2 = mysql_fetch_array($result2)) {
		$pdf->SetWidths(array(30,45,45,30,30));
		$pdf->Row(array('','Servicio: ' . $row2['servicio'],'Diagnostico Principal: ' . $row2['diagnostico'],
						'Fecha Ingreso: ' . $row2['fecha_movimiento_ingreso'],'Fecha Egreso: ' . $row2['fecha_egreso']));
		
		$xmlIngreso=new SimpleXMLElement('<rows/>');
		$xmlPrescripciones=new SimpleXMLElement('<rows/>');			
		$xmlDiagnosticos=new SimpleXMLElement('<rows/>');
		$xmlNacimientos=new SimpleXMLElement('<rows/>');
		$xmlPracticas=new SimpleXMLElement('<rows/>');	
		
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
		$sql.="WHERE im.id_ingreso_movimiento='".$row2['id_ingreso_movimiento']."' ";		
		toXML($xmlIngreso, $sql, "datosingreso");
		
		$sql="SELECT p.id_prescripcion, CONCAT(monodroga, ' ',presentacion, ' ',concentracion) 'descrip', ";
		$sql.="v.id_vademecum, p.posologia, v.monodroga, v.concentracion, v.presentacion, DATE_FORMAT(fecha_prescripcion,'%d/%m/%Y') 'fecha_prescripcion' ";
		$sql.="FROM ingresos_movimientos im ";
		$sql.="INNER JOIN prescripciones p USING(id_ingreso_movimiento) ";
		$sql.="INNER JOIN vademecum v USING(id_vademecum) ";		
		$sql.="WHERE im.id_ingreso_movimiento='".$row2['id_ingreso_movimiento']."' ";
		$sql.="ORDER BY fecha_prescripcion";	
		toXML($xmlPrescripciones, $sql, "prescripcion");
		
		$sql="SELECT id.id_item_diagnostico, id.id_ingreso_movimiento, id.id_diagnostico, cie10.descripcion4 'descripcion', tipo_diagnostico ";
		$sql.="FROM ingresos_movimientos im ";
		$sql.="INNER JOIN items_diagnosticos id USING(id_ingreso_movimiento) ";
		$sql.="INNER JOIN cie10 USING(id_diagnostico) ";		
		$sql.="WHERE im.id_ingreso_movimiento='".$row2['id_ingreso_movimiento']."' ";
		$sql.="ORDER BY cie10.descripcion4";	
		toXML($xmlDiagnosticos, $sql, "diagnostico");
		
		$sql="SELECT ib.id_ingreso_bebe, ib.id_ingreso, ib.peso, ";
		$sql.="ib.condicion_alnacer, IF(ib.condicion_alnacer='V','Nacido Vivo','Defuncion Fetal') 'condicion_alnacer_text', ";
		$sql.="ib.terminacion, IF(ib.terminacion='V','Vaginal','Cesárea') 'terminacion_text', ";
		$sql.="ib.sexo,(CASE WHEN sexo='M' THEN 'MASCULINO' WHEN sexo='F' THEN 'FEMENINO' WHEN sexo='I' THEN 'INDEFINIDO' END) 'sexo_text' ";
		$sql.="FROM ingresos_movimientos im ";
		$sql.="INNER JOIN ingresos i USING(id_ingreso) ";
		$sql.="INNER JOIN ingresos_bebes ib USING(id_ingreso) ";		
		$sql.="WHERE im.id_ingreso_movimiento='".$row2['id_ingreso_movimiento']."' ";
		$sql.="ORDER BY peso";	
		toXML($xmlNacimientos, $sql, "nacimiento");			
		
		$sql="SELECT s.id_solicitudes, np.procedimiento 'descripcion', s.estado, s.resultados, id_practica, id_ingreso_movimiento_solicitud, DATE_FORMAT(fecha_solicitud,'%d/%m/%Y') as 'fecha_solicitud'  ";
		$sql.="FROM solicitudes s ";
		$sql.="INNER JOIN nomenclador_practicas np USING(id_practica) ";
		$sql.="INNER JOIN  ingresos_movimientos im ON  s.id_ingreso_movimiento_solicitud = im.id_ingreso_movimiento ";
		$sql.="WHERE im.id_ingreso_movimiento='".$row2['id_ingreso_movimiento']."' ";
		$sql.="ORDER BY fecha_solicitud";
		toXML($xmlPracticas, $sql, "practica");		
				
		if ($xmlIngreso->datosingreso['id_nivel']==1) {
			$pdf->SetFont('','B');
			$pdf->Cell(0,6,'Datos de la Consulta',0,1,'L');
			$pdf->SetFont('');
			
			$pdf->SetWidths(array(30,40,35));
			$pdf->Row(array('','Tipo de Ingreso: ' . utf8_decode($xmlIngreso->datosingreso['tipo_ingreso']),'Consulta: ' . $xmlIngreso->datosingreso['primera_vez']));
			
			$pdf->SetWidths(array(30,75));
			$pdf->Row(array('','Motivo de la Consulta: ' . utf8_decode($xmlIngreso->datosingreso['motivo'])));
			$pdf->Row(array('','Observaciones: ' . utf8_decode($xmlIngreso->datosingreso['observaciones'])));									
								
			if ($pdf->xmlPaciente->paciente['edad'] <= 12){
				$pdf->SetWidths(array(30,40,35));
				$pdf->Row(array('','Peso: ' . $xmlIngreso->datosingreso['motivo'],'Talla: '.$xmlIngreso->datosingreso['talla'],
								   'Perimetro: ' . $xmlIngreso->datosingreso['perimetro']));
				
				$pdf->Row(array('','Perc. Talla: ' . $xmlIngreso->datosingreso['peso_edad'],'Perc. Peso: '.$xmlIngreso->datosingreso['talla_edad'],
								   'Perc. Perimetro: ' . $xmlIngreso->datosingreso['perc_perimetro']));								
					
			}
			if ($xmlIngreso->datosingreso['embarazada'] == 'S') {
				$pdf->SetWidths(array(30,70));
				$pdf->Row(array('','Trimestre: ' . $xmlIngreso->datosingreso['trimestre'],'Presión Arterial: '.$xmlIngreso->datosingreso['tension_arterial']));				
			}
		}
		
		if ($xmlPrescripciones->prescripcion) {
			$pdf->SetFont('','B');
			$pdf->Cell(0,6,'Prescripciones',0,1,'L');			
			
			$pdf->SetWidths(array(30,30,30,30,30,30));
			
			$pdf->Row(array('','Fecha','Monodroga','Presentacion','Concentracion','Posologia'));
			$pdf->SetFont('');
			 		
			foreach ($xmlPrescripciones->prescripcion as $prec) {
				$pdf->Row(array('',$prec['fecha_prescripcion'],$prec['monodroga'],$prec['presentacion'],
								   $prec['concentracion'],$prec['posologia']));
			}
		}
		
		if ($xmlPracticas->practica) {
			$pdf->SetFont('','B');
			$pdf->Cell(0,6,'Practicas y Procedimientos',0,1,'L');			
			
			$pdf->SetWidths(array(30,30,60,60));
			
			$pdf->Row(array('','Fecha de Solicitud','Practica','Resultado'));
			$pdf->SetFont('');					
			
			foreach ($xmlPracticas->practica as $prac) {
				$pdf->Row(array('',$prac['fecha_solicitud'],utf8_decode($prac['descripcion']),$prac['resultados']));
			}		
		}
	}
	$pdf->Ln(2);
}
$pdf->Output();
?>