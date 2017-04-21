<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>BUSCANDO...</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body leftmargin="0" topmargin="0">

<?php

$_txt_buscando = $_REQUEST["_txt_buscando"];

?>

<table border="1" cellpadding="5" cellspacing="0" bgcolor="#EFEFEF">
  <tr>
    <td><strong><font color="#FF0000" size="3" face="Arial, Helvetica, sans-serif">Buscando<?php if (!empty($_txt_buscando)) print ' '.$_txt_buscando; ?>... 
      </font></strong></td>
  </tr>
</table>
<strong></strong> 
</body>
</html>
