<?php
/*
CREATE VIEW suram.027_antecedentes AS SELECT * FROM salud1.027_antecedentes;
CREATE VIEW suram.personas_profesion AS SELECT * FROM salud1.personas_profesion;
CREATE VIEW suram.024_profesiones AS SELECT * FROM salud1.024_profesiones;
CREATE VIEW suram._duracion_turno_medicos AS SELECT * FROM salud1._duracion_turno_medicos;
CREATE VIEW suram.027_antecedentes_personas AS SELECT * FROM salud1.027_antecedentes_personas;
CREATE VIEW suram.027_dosis AS SELECT * FROM salud1.027_dosis;
CREATE VIEW suram.027_tipo_antec AS SELECT * FROM salud1.027_tipo_antec;
CREATE VIEW suram.027_vacunaciones AS SELECT * FROM salud1.027_vacunaciones;
CREATE VIEW suram.027_vacunas AS SELECT * FROM salud1.027_vacunas;
CREATE VIEW suram.oas_usuarios AS SELECT * FROM salud1.oas_usuarios;
CREATE VIEW suram.sistemas_perfiles_usuarios_oas AS SELECT * FROM salud1.sistemas_perfiles_usuarios_oas;
CREATE VIEW suram._auditoria AS SELECT * FROM salud1._auditoria;
CREATE VIEW suram._departamentos AS SELECT * FROM salud1._departamentos;
CREATE VIEW suram._localidades AS SELECT * FROM salud1._localidades;
CREATE VIEW suram._organismos AS SELECT * FROM salud1._organismos;
CREATE VIEW suram._organismos_areas AS SELECT * FROM salud1._organismos_areas;
CREATE VIEW suram._organismos_areas_servicios AS SELECT * FROM salud1._organismos_areas_servicios;
CREATE VIEW suram._organismos_tipos AS SELECT * FROM salud1._organismos_tipos;
CREATE VIEW suram._personal AS SELECT * FROM salud1._personal;
CREATE VIEW suram._personas AS SELECT * FROM salud1._personas;
CREATE VIEW suram._provincias AS SELECT * FROM salud1._provincias;
CREATE VIEW suram._servicios AS SELECT * FROM salud1._servicios;
CREATE VIEW suram._sesiones AS SELECT * FROM salud1._sesiones;
CREATE VIEW suram._sistemas AS SELECT * FROM salud1._sistemas;
CREATE VIEW suram._sistemas_perfiles AS SELECT * FROM salud1._sistemas_perfiles;
CREATE VIEW suram._sistemas_usuarios AS SELECT * FROM salud1._sistemas_usuarios;
CREATE VIEW suram._usuarios AS SELECT * FROM salud1._usuarios;
*/
//extrae todo el arreglo Request y lo pone como variables
foreach ($_REQUEST as $key => $value)
{
 	if(get_magic_quotes_gpc()) {
    	${$key} = mysql_real_escape_string(stripslashes($value));
    } else {
        ${$key} = mysql_real_escape_string($value);
    }
}

function loadXML($data) {
	$data = stripslashes($data);
  $xml = @simplexml_load_string($data);
  if (!is_object($xml))
    throw new Exception('Error en la lectura del XML',1001);
  return $xml;
}

function toXML(&$xml, $sql , $tag = "row") {

	$paramet = @mysql_query($sql);
	$nodo = null;
	if (mysql_insert_id()){ 
		$nodo=$xml->addChild("insert_id", mysql_insert_id());
	}
	if (mysql_errno()>0) {
	 	$error="Error devuelto por la Base de Datos: ".mysql_errno()." ".mysql_error()."\n";
	 	$nodo=$xml->addChild("error", $error);
	}
	else{
		if (is_resource($paramet)) {
			WHILE ($row = mysql_fetch_array($paramet)) {
				$nodo=$xml->addChild($tag);	
				foreach($row as $key => $value) {
					if (!is_numeric($key)) $nodo->addAttribute($key, $value);
				}
			}
			$nodo=null;
		} else if (is_array($paramet)) {
			$nodo=$xml->addChild($tag);
			foreach($paramet as $key => $value) {
				if (!is_numeric($key)) $nodo->addAttribute($key, $value);
			}
		} else if (is_a($paramet, "SimpleXMLElement")) {
			$nodo=$xml->addChild($paramet->getName(), $paramet);
			foreach($paramet->attributes() as $key => $value) {
	    		$nodo->addAttribute($key, $value);
			}
			foreach ($paramet->children() as $hijo) {
				toXML($nodo, $hijo);
			}
		} else if (is_string($paramet)) {
			$nodo=new SimpleXMLElement($paramet);
			$nodo=$xml->addChild($nodo->getName(), $nodo[0]);
		}
	}
	return $nodo;
}

function DMYYYY($fecha) {
	$f=explode("-", $fecha);
	return (int) $f[2] . "/" . (int) $f[1] . "/" . (int) $f[0];
}
function YYYYDM($fecha) {
	$fecha = explode('/',$fecha);
	$fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
	
	return $fecha;
}
function XMLtoSQL($xml,$excepto = array()){
	$sql  ="";
	foreach($xml->children() as $child)
	{
		if (!in_array($child->getName(),$excepto)){
			$sql.= $child->getName() . "='" . $child . "', ";
		}
	}
	$sql = substr($sql,0,strlen($sql)-2);
	return $sql;
}
?>