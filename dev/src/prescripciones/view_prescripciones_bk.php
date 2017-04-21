<?php
include("../control_acceso_flex.php");
include("../rutinas.php");
require($SYSpathraiz.'fpdf17/mc_table/mc_table.php');
include($SYSpathraiz.'phpqrcode/phpqrcode.php');

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
	    $this->Cell(0,6,'Medicamentos Prescriptos',0,1,'C');	    
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
						
		$query="SELECT id_ingreso, id_persona, id_nivel, ";
		$query.="DATE_FORMAT(fecha_consulta_ingreso,'%d/%m/%Y') 'fecha', organismo_area 'establecimiento' ";
		$query.="FROM ingresos JOIN $salud._organismos_areas USING(organismo_area_id) ";
		$query.="WHERE id_ingreso='".$_REQUEST['id_ingreso']."'";
		$result = mysql_query($query);
		
		if ($row = mysql_fetch_array($result)){						
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

$pdf->SetFont('Arial','',8);
$pdf->SetFont('','B');
$pdf->SetWidths(array(35,45));
$pdf->Row(array('Establecimiento: ',utf8_decode($pdf->establecimiento)));
$pdf->Row(array('Medico: ',$_SESSION['usuario_nombre']));
$pdf->SetFont('Arial','',6);
$pdf->Cell(0,6,'DATOS DEL PACIENTE',0,1,'C');
$pdf->SetWidths(array(35,20,20,20,15,10,30,20,20));
$pdf->Row(array('Nombre','Tipo Doc','Nro Doc','Fecha Nac','Sexo','Edad','Domicilio','Localidad','Departamento'));
$pdf->SetFont('');
$pdf->Row(array($pdf->xmlPaciente->paciente['apeynom'],$pdf->xmlPaciente->paciente['tipo_doc'],$pdf->xmlPaciente->paciente['nrodoc'],
	            $pdf->xmlPaciente->paciente['fecha_nac'],$pdf->xmlPaciente->paciente['sexo'],$pdf->xmlPaciente->paciente['edad'],
				$pdf->xmlPaciente->paciente['domicilio'],$pdf->xmlPaciente->paciente['localidad'],$pdf->xmlPaciente->paciente['departamento']));					

$xmlIngreso=new SimpleXMLElement('<rows/>');
$xmlPrescripciones=new SimpleXMLElement('<rows/>');
	
$sql="SELECT id_ingreso_movimiento ";
$sql.="FROM ingresos_movimientos ";
$sql.="INNER JOIN ingresos USING(id_ingreso) ";
$sql.="WHERE id_ingreso='$id_ingreso' ";
$row = mysql_query($sql);
if ($rs = mysql_fetch_array($row)){
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
		$pdf->SetFont('','B');
		$pdf->Cell(0,6,'Prescripciones',0,1,'L');			
		
		$pdf->SetWidths(array(30,30,30,30,30));
		
		$pdf->Row(array('Fecha','Monodroga','Presentacion','Concentracion','Posologia'));
		$pdf->SetFont('');
		 		
		foreach ($xmlPrescripciones->prescripcion as $prec) {
			$pdf->Row(array($prec['fecha_prescripcion'],utf8_decode($prec['monodroga']),$prec['presentacion'],
							   $prec['concentracion'],utf8_decode($prec['posologia'])));
		}
	}
	$pdf->Ln(1);
	$date = date("d/m/Y");
	$texto = 'Fecha: '.$date.' ';
	$texto.= 'Medico: '.$_SESSION['usuario_nombre'].' ';
	$texto.= 'Id: '.$id_ingreso_movimiento;	
	$img = "codigo.png";
	QRcode::png($texto, $img);
    $pdf->Image($img);
}
$pdf->Output();
?>