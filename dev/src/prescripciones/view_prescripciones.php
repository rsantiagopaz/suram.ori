<?php
include("../control_acceso_flex.php");
include("../rutinas.php");
require($SYSpathraiz.'fpdf17/mc_table/mc_table.php');

ini_set('memory_limit', '256M');
set_time_limit(900);

class PDF
{	
	var $xmlPaciente;	
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
						
		$query="SELECT id_ingreso, id_persona, id_nivel, ";
		$query.="DATE_FORMAT(fecha_consulta_ingreso,'%d/%m/%Y') 'fecha', organismo_area 'establecimiento' ";
		$query.="FROM ingresos JOIN $salud._organismos_areas USING(organismo_area_id) ";
		$query.="WHERE id_ingreso='".$_REQUEST['id_ingreso']."'";
		$result = $GLOBALS["mysqli"]->query($query);
		
		if ($row = $result->fetch_array()){						
			$id_persona = $row['id_persona'];
			$establecimiento = $row['establecimiento'];
		
			$sql="SELECT persona_id 'id_persona',persona_nombre 'apeynom', CASE p.persona_tipodoc WHEN 'D' THEN 'DNI' WHEN 'C' THEN 'LC' WHEN 'E' THEN 'LE' WHEN 'F' THEN 'CI' END as 'tipo_doc', persona_dni 'nrodoc', IF( persona_sexo = 'M', 'MASCULINO', 'FEMENINO' ) as 'sexo', ";
			$sql.="IF (persona_fecha_nacimiento <> '0000-00-00',DATE_FORMAT(persona_fecha_nacimiento,'%d/%m/%Y'),'') as 'fechanac', ";
			$sql.="IF (persona_fecha_nacimiento <> '0000-00-00',(YEAR(CURRENT_DATE)-YEAR(persona_fecha_nacimiento))-(RIGHT(CURRENT_DATE,5)<RIGHT(persona_fecha_nacimiento,5)),'') 'edad', persona_domicilio 'domicilio', localidad, departamento, provincias ";
			$sql.="FROM $salud._personas p ";			
			$sql.="LEFT JOIN $salud._localidades l ON p.localidad_id = l.localidad_id ";
			$sql.="LEFT JOIN $salud._departamentos d USING(departamento_id) ";
			$sql.="LEFT JOIN $salud._provincias pr ON d.provincia_id = pr.provincias_id ";
			$sql.="WHERE p.persona_id='$id_persona'";
						
			toXML($xmlPaciente, $sql, "paciente");			
			
			$this->establecimiento = $establecimiento;
			$this->xmlPaciente = $xmlPaciente;											
		}
	}
}

$pdf = new PDF();
$pdf->salud = $salud;
$pdf->suram = $suram;

// Carga de datos
$data = $pdf->LoadData();

$html ='<html>';
$html.='<head>';
$html.='<title>Prescripciones</title>';			
$html.='<style type="text/css">';
$html.='<!--		
			body { background-color:#FFFFFF; font-family:Arial, Helvetica, sans-serif; }
			h1 { font-size:20px; }
			h2 { font-size:18px; }
			h3 { font-size:16px; }
			table, td, th
			{
				border:1px solid #666666;
				border-collapse:collapse;
				font-size:10px;
			}
			th
			{
				background-color:#666666;
				color:white;
			}
		-->';
$html.='</style>';	
$html.='</head>';
$html.='<body>';
$html.='<h1>Establecimiento: '.utf8_decode($pdf->establecimiento).'</h1>';
$html.='<h2>Medico: '.$_SESSION['usuario_nombre'].'</h2>';
$html.='<h3>DATOS DEL PACIENTE</h3>';
$html.='<table>';
$html.='<theader><tr>';
$html.='<th>Nombre</th>';
$html.='<th>Tipo Doc</th>';
$html.='<th>Nro Doc</th>';
$html.='<th>Fecha Nac</th>';
$html.='<th>Sexo</th>';
$html.='<th>Edad</th>';
$html.='<th>Domicilio</th>';
$html.='<th>Localidad</th>';
$html.='<th>Departamento</th>';
$html.='</tr></theader>';
$html.='<tbody>';
$html.='<tr>';
$html.='<td>'.$pdf->xmlPaciente->paciente['apeynom'].'</td>';
$html.='<td>'.$pdf->xmlPaciente->paciente['tipo_doc'].'</td>';
$html.='<td>'.$pdf->xmlPaciente->paciente['nrodoc'].'</td>';
$html.='<td>'.$pdf->xmlPaciente->paciente['fecha_nac'].'</td>';
$html.='<td>'.$pdf->xmlPaciente->paciente['sexo'].'</td>';
$html.='<td>'.$pdf->xmlPaciente->paciente['edad'].'</td>';
$html.='<td>'.$pdf->xmlPaciente->paciente['domicilio'].'</td>';
$html.='<td>'.$pdf->xmlPaciente->paciente['localidad'].'</td>';
$html.='<td>'.$pdf->xmlPaciente->paciente['departamento'].'</td>';
$html.='</tr>';
$html.='</tbody>';
$html.='</table>';

$html.='<br />';

$xmlIngreso=new SimpleXMLElement('<rows/>');
$xmlPrescripciones=new SimpleXMLElement('<rows/>');
	
$sql="SELECT id_ingreso_movimiento ";
$sql.="FROM ingresos_movimientos ";
$sql.="INNER JOIN ingresos USING(id_ingreso) ";
$sql.="WHERE id_ingreso='$id_ingreso' ";
$row = $GLOBALS["mysqli"]->query($sql);
if ($rs = $row->fetch_array()){
	$id_ingreso_movimiento = $rs['id_ingreso_movimiento'];		
	
	$sql="SELECT p.id_prescripcion, CONCAT(monodroga, ' ',presentacion, ' ',concentracion) 'descrip', ";
	$sql.="v.id_vademecum, p.posologia, v.monodroga, v.concentracion, v.presentacion, DATE_FORMAT(fecha_prescripcion,'%d/%m/%Y') 'fecha_prescripcion' ";
	$sql.="FROM ingresos_movimientos im ";
	$sql.="INNER JOIN prescripciones p USING(id_ingreso_movimiento) ";
	$sql.="INNER JOIN vademecum v USING(id_vademecum) ";		
	$sql.="WHERE im.id_ingreso_movimiento='$id_ingreso_movimiento' ";
	$sql.="ORDER BY fecha_prescripcion";
	
	toXML($xmlPrescripciones, $sql, "prescripcion");	
	
	if ($xmlPrescripciones->prescripcion) {
		$html.='<table>';
		$html.='<theader>';
		$html.='<th>Fecha</th>';
		$html.='<th>Monodroga</th>';
		$html.='<th>Presentacion</th>';
		$html.='<th>Concentracion</th>';
		$html.='<th>Posologia</th>';
		$html.='</theader>';
		$html.='<tbody>';
		foreach ($xmlPrescripciones->prescripcion as $prec) {
			$html.='<td>'.$prec['fecha_prescripcion'].'</td>';
			$html.='<td>'.$prec['monodroga'].'</td>';
			$html.='<td>'.$prec['presentacion'].'</td>';
			$html.='<td>'.$prec['concentracion'].'</td>';
			$html.='<td>'.$prec['posologia'].'</td>';			
		}
		$html.='</tbody>';
		$html.='</table>';		 				
	}	
	$date = date("d/m/Y");
	$texto = 'Fecha: '.$date.' ';
	$texto.= 'Medico: '.$_SESSION['usuario_nombre'].' ';
	$texto.= 'Id: '.$id_ingreso_movimiento;	
	
	$html.='<img src="img.php?texto='.$texto.'"/>';
	$html.='</body>';
	$html.='</html>';
}
echo $html;
?>
