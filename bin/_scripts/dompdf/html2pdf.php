<?php

require_once("dompdf_config.inc.php");

$url = rawurldecode($_REQUEST['url']);

$dompdf = new DOMPDF();

//$dompdf->set_base_path("../certificados");

$dompdf->load_html_file($url);

$dompdf->set_paper($_REQUEST["paper"]);

$dompdf->render();

$dompdf->stream($_REQUEST['nombre'].".pdf");


?>
