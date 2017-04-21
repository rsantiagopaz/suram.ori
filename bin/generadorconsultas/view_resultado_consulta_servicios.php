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
	    $this->Cell(0,6,'Reporte Generado',0,1,'C');	    
	    $this->Ln(2);
	    $this->SetFont('Arial','',10);
	    $this->Cell(0,6,'Desde: '.$_REQUEST['fecha_desde'].' Hasta: '.$_REQUEST['fecha_hasta'],0,1,'C');
	    $this->Ln(2);
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
		//$this->SetFont('');
	}
	
	// Cargar los datos
	function LoadData()
	{
		$salud = $this->salud;
		$suram = $this->suram;
		
		$xmlServicios=new SimpleXMLElement('<rows/>');		
		
		$sql="SELECT id_servicio, denominacion ";
		$sql.="FROM $salud._organismos_areas_servicios ";
		$sql.="JOIN $salud._servicios ";
		$sql.="USING ( id_servicio ) ";
		$sql.="WHERE id_organismo_area = '".$_SESSION['usuario_organismo_area_id']."' ";
		if ($_REQUEST['unservicio'] == 'S')
			$sql.="AND id_servicio = '".$_REQUEST['id_servicio']."' ";
		$sql.="ORDER BY denominacion";
		toXML($xmlServicios, $sql, "servicios");
		
		$this->xmlServicios = $xmlServicios;
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

$pdf->SetLeftMargin(20);
$pdf->AddPage();

$xmlAntecedentes=new SimpleXMLElement('<rows/>');

if ($_REQUEST['antecedentes'] == 'S') {
	$sql="SELECT descripcion, COUNT(descripcion) 'cant', id_antecedente, antecedente, observaciones, ";
	$sql.="CASE accion WHEN 'A' THEN 'Agregó' WHEN 'B' THEN 'Quitó' WHEN 'M' THEN 'Modificó' END as 'accion' ";			
	$sql.="FROM $salud.027_antecedentes_personas a ";
	$sql.="JOIN $salud.027_antecedentes USING(id_antecedente) ";
	$sql.="JOIN $salud.027_tipo_antec USING(id_tipo_antec) ";						
	$sql.="WHERE fecha >= '".YYYYDM($_REQUEST['fecha_desde'])."' ";
	$sql.="AND fecha <= '".YYYYDM($_REQUEST['fecha_hasta'])."' ";
	$sql.="AND usuario='".$_SESSION['usuario']."' ";
	$sql.="GROUP BY antecedente";
	toXML($xmlAntecedentes, $sql, "antecedentes");	
}

if ($xmlAntecedentes->antecedentes) {
	$pdf->SetFont('Arial','',8);
	$pdf->SetFont('','B');
	$pdf->SetX(15);
	$pdf->Cell(0,6,'ANTECEDENTES',0,1,'L');			
	
	$pdf->SetWidths(array(60,60,30));
	
	$pdf->Row(array('Antecedente','Tipo','Cantidad'));
	$pdf->SetFont('');
	 		
	foreach ($xmlAntecedentes->antecedentes as $ant) {
		$pdf->Row(array(utf8_decode($ant['antecedente']),utf8_decode($ant['descripcion']),$ant['cant']));
	}
	
	$pdf->Ln(4);
}

if ($pdf->xmlServicios->servicios) {
	$pdf->SetFont('Arial','',8);
	$pdf->SetFont('','B');
	$pdf->SetX(15);	
	$pdf->Cell(0,6,'SERVICIOS',0,1,'L');		
	
	foreach ($pdf->xmlServicios->servicios as $ser) {
		$xmlPacientes=new SimpleXMLElement('<rows/>');		
		$xmlDiagnosticos=new SimpleXMLElement('<rows/>');
		$xmlPrescripciones=new SimpleXMLElement('<rows/>');				
		
		$pdf->SetX(15);
		$pdf->SetFont('','B');
		$pdf->SetWidths(array(155));		
		$pdf->Row(array($ser['denominacion']));
		$pdf->SetFont('');				
		
		if ($_REQUEST['pacientes'] == 'S') {
			$sql = "SELECT COUNT(id_ingreso) 'cant' ";
			$sql.= "FROM ingresos i ";
			$sql.= "JOIN ingresos_movimientos im USING(id_ingreso) ";
			$sql.= "JOIN $salud._organismos_areas_servicios oas ON im.id_area_servicio_ingreso = oas.id_servicio ";
			$sql.= "WHERE DATE(fecha_consulta_ingreso) >= '".YYYYDM($_REQUEST['fecha_desde'])."' ";
			$sql.= "AND DATE(fecha_consulta_ingreso) <= '".YYYYDM($_REQUEST['fecha_hasta'])."' ";
			$sql.= "AND im.id_area_servicio_ingreso = '" . $ser['id_servicio'] . "' ";
			$sql.= "AND id_organismo_area = '".$_SESSION['usuario_organismo_area_id']."' ";						
			toXML($xmlPacientes, $sql, "pacientes");	
		}				
		
		if ($_REQUEST['diagnosticos'] == 'S') {
			$sql ="SELECT CONCAT(cie10.cod_4,' - ',cie10.descripcion4) 'coddescrip', ";
			$sql.="COUNT(CONCAT(cie10.cod_4,' - ',cie10.descripcion4)) 'cant', ";
			$sql.="cie10.descripcion4 'descripcion', tipo_diagnostico,  cie10.cod_4 'codigo' ";
			$sql.="FROM ingresos_movimientos im ";
			$sql.="JOIN ingresos i USING(id_ingreso) ";
			$sql.="INNER JOIN items_diagnosticos id USING(id_ingreso_movimiento) ";
			$sql.="INNER JOIN cie10 USING(id_diagnostico) ";
			$sql.="JOIN $salud._organismos_areas_servicios oas ON im.id_area_servicio_ingreso = oas.id_servicio ";		
			$sql.="WHERE DATE(fecha_consulta_ingreso) >= '".YYYYDM($_REQUEST['fecha_desde'])."' ";
			$sql.="AND DATE(fecha_consulta_ingreso) <= '".YYYYDM($_REQUEST['fecha_hasta'])."' ";
			$sql.="AND im.id_area_servicio_ingreso = '" . $ser['id_servicio'] . "' ";
			$sql.="AND id_organismo_area = '".$_SESSION['usuario_organismo_area_id']."' ";
			$sql.="GROUP BY coddescrip";			
			toXML($xmlDiagnosticos, $sql, "diagnosticos");		
		}
		
		if ($_REQUEST['prescripciones'] == 'S') {
			$sql ="SELECT CONCAT(monodroga, ' ',presentacion, ' ',concentracion) 'descrip', ";
			$sql.="COUNT(CONCAT(monodroga, ' ',presentacion, ' ',concentracion)) 'cant', ";
			$sql.="v.id_vademecum, p.posologia, v.monodroga, v.concentracion, v.presentacion, DATE_FORMAT(fecha_prescripcion,'%d/%m/%Y') 'fecha_prescripcion' ";
			$sql.="FROM ingresos_movimientos im ";
			$sql.="JOIN ingresos i USING(id_ingreso) ";
			$sql.="INNER JOIN prescripciones p USING(id_ingreso_movimiento) ";
			$sql.="INNER JOIN vademecum v USING(id_vademecum) ";
			$sql.="JOIN $salud._organismos_areas_servicios oas ON im.id_area_servicio_ingreso = oas.id_servicio ";
			$sql.="WHERE DATE(fecha_consulta_ingreso) >= '".YYYYDM($_REQUEST['fecha_desde'])."' ";
			$sql.="AND DATE(fecha_consulta_ingreso) <= '".YYYYDM($_REQUEST['fecha_hasta'])."' ";
			$sql.="AND im.id_area_servicio_ingreso = '" . $ser['id_servicio'] . "' ";
			$sql.="AND id_organismo_area = '".$_SESSION['usuario_organismo_area_id']."' ";
			$sql.="GROUP BY descrip";			
			toXML($xmlPrescripciones, $sql, "prescripciones");	
		}
		
		if ($xmlPacientes->pacientes) {
			$pdf->SetFont('','B');
			$pdf->Cell(0,6,'Pacientes',0,1,'L');			
			
			$pdf->SetWidths(array(20,30));
			
			$pdf->Row(array('Cantidad'));
			$pdf->SetFont('');
			 		
			foreach ($xmlPacientes->pacientes as $pac) {
				$pdf->Row(array($pac['cant']));
			}
		}				
		
		if ($xmlDiagnosticos->diagnosticos) {
			$pdf->SetFont('','B');
			$pdf->Cell(0,6,utf8_decode('Diagnósticos'),0,1,'L');			
			
			$pdf->SetWidths(array(30,90,30));
			
			$pdf->Row(array('Codigo',utf8_decode('Descripción'),'Cantidad'));
			$pdf->SetFont('');
			 		
			foreach ($xmlDiagnosticos->diagnosticos as $diag) {
				$pdf->Row(array($diag['codigo'],$diag['descripcion'],$diag['cant']));
			}
		}
				
		if ($xmlPrescripciones->prescripciones) {
			$pdf->SetFont('','B');
			$pdf->Cell(0,6,'Prescripciones',0,1,'L');			
			
			$pdf->SetWidths(array(60,30,30,30));
			
			$pdf->Row(array('Monodroga',utf8_decode('Presentación'),utf8_decode('Concentración'),'Cantidad'));
			$pdf->SetFont('');
			 		
			foreach ($xmlPrescripciones->prescripciones as $prec) {
				$pdf->Row(array(utf8_decode($prec['monodroga']),$prec['presentacion'],$prec['concentracion'],$prec['cant']));
			}
		}
		
		$pdf->Ln(4);					
	}
}

$pdf->Ln(1);
$pdf->Output();
?>