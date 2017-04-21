<?php 
switch($_REQUEST['tipo'])
{
	case 'prueba':
		$url=urlencode("http://".$_SERVER['SERVER_NAME']."/soscash2/_scripts/dompdf/prueba.php?apellido=mitre&nombre=jorge");
		$nombre="prueba";
		$paper='a4';
		break;
	case 'traslado':
		$url=urlencode("http://".$_SERVER['SERVER_NAME']."/remedu/certificados/dictamen.php?id_traslado=".$_REQUEST['id']);
		$nombre="Certificado Traslado Trasitorio y/o Cambio de Funciones";
		$paper='legal';
		break;
}
//   header("Location: html2pdf.php?url=".$url."&nombre=".$nombre."&paper=".$paper);
?>

<HTML>
<BODY>
<embed src="html2pdf.php?url=<? echo $url;  ?>&nombre=<? echo $nombre;?>&paper=<? echo $paper; ?>" type="application/pdf" width="100%" height="100%">
</BODY>
</HTML>