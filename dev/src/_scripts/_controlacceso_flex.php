<?php

header('Expires: Wed, 23 Dec 1980 00:30:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');


 global $_sessionid;
 
 global $SYSusuario;
 global $SYSusuario_nombre;
 global $SYSusuario_estado;
 global $SYSusuario_organismo_id;
 global $SYSusuario_organismo;
 global $SYSusuario_organismo_area_id;
 global $SYSusuario_organismo_area;
 global $SYSusuario_organismo_area_mesa_entrada;
 global $SYSsistemas_perfiles_usuario;
 
 
 global $link_salud1;

 // USO DE SESION
 session_start();
 
 
//$_sessionid=$HTTP_SESSION_VARS['_sessionid']; 
$_sessionid = $_SESSION['SYSsesion_id'];
$SYSusuario = $_SESSION['usuario'];
$id_oas_usuario = $_SESSION['id_oas_usuario'];
 include($SYSpathraiz.'config.php');
  
  
 $_acceso='NO';
 
if (empty($_sessionid))
{
   $_acceso='NO';
   $_mensaje='� NO EXISTE SESION (si el mensaje persiste, comun�quelo al administrador de sistemas) !';
   $_login ='SI';
}
else
{ 
   $result=$mysqli->query("SELECT * FROM $salud._sesiones WHERE _sessionid='$_sessionid'");
   $num=$result->num_rows;
   $_acceso='SI';
   $_login='NO';
   if ($num<=0)
   {
      $_acceso='NO';
      $_mensaje='� NO SE ENCONTRO SESION: '.$_sessionid.' (es probable que UD. no haya ingresado sus datos de acceso a�n) !';
      $_login ='SI';
   }
   else
   {
   	$aux = $result->fetch_array();
   	
      $SYSusuario            = $aux['SYSusuario'];
	  $SYSsesiondetalle      = $aux['SYSsesiondetalle'];
	  $SYSsesionfecha_cierre = $aux['SYSsesionfecha_cierre'];
	  $SYSsesionhora_cierre  = $aux['SYSsesionhora_cierre'];
	  $SYSsesionfecha_ultimo = $aux['SYSsesionfecha_ultimo'];
	  $SYSsesionhora_ultimo  = $aux['SYSsesionhora_ultimo'];
		
	  if (empty($SYSusuario))
	  {
		$_acceso='NO';
		$_mensaje='� NO SE ENCONTRO EL USUARIO VINCULADO A LA SESION: '.$_sessionid.' !';
      } 
	  else
	  {
		 $sql="SELECT *  ";
	     $sql.="FROM $salud._usuarios u ";
	     $sql.="INNER JOIN $salud.oas_usuarios USING(SYSusuario) ";
	     $sql.="INNER JOIN $salud._organismos_areas_servicios oas USING(id_organismo_area_servicio) ";
	   	 $sql.="INNER JOIN $salud._organismos_areas a ON a.organismo_area_id=oas.id_organismo_area ";
	   	 $sql.="INNER JOIN $salud._servicios s USING(id_servicio) ";
         $sql.="INNER JOIN $salud._organismos USING(organismo_id) ";
         $sql.="INNER JOIN $salud._personas p ON p.persona_id=u.id_persona ";
         $sql.="WHERE $salud.oas_usuarios.id_oas_usuario='$id_oas_usuario' ";

         $result=@$mysqli->query($sql);
			 
         $num=$result->num_rows;
         if ($num>0)
         {
         	$aux = $result->fetch_array();
         	
		   $SYSusuario_nombre            = $aux['persona_nombre'];
		   $SYSusuario_estado            = $aux['SYSusuario_estado'];
		   $SYSusuario_organismo_id      = $aux['organismo_id'];
		   $SYSusuario_organismo         = $aux['organismo'];
		   $SYSusuario_organismo_area_id = $aux['organismo_area_id'];
		   $SYSusuario_organismo_area    = $aux['organismo_area'];
		   $SYSusuario_organismo_area_mesa_entrada = $aux['organismo_area_mesa_entrada'];
		   $SYSusuario_oas_id = $aux['id_oas_usuario'];
		   //$SYSusuario_ = $aux[];
		   $SYSsistemas_perfiles_usuario[0]='';  
		   if ($SYSusuario_estado==1)
		   {
               $_acceso='SI';
               $_login ='NO';
			   
               // Verifico si el usuario est� autorizado a utilizar el sistema.
			   if (empty($SYSsistema_id))
			   {
                  $_acceso='NO';
                  $_login ='NO';
				  $_mensaje='� ERROR DE SISTEMA: SYSsistema_id vac�o !';
   				}
				else
				{
				  // Veo en tabla _sistemas_ususarios si est� autorizado el usuario
                  $sql="SELECT *  ";
				  $sql.="FROM $salud._sistemas_usuarios ";
				  $sql.="INNER JOIN $salud.sistemas_perfiles_usuarios_oas USING(id_sistema_usuario) ";
				  $sql.="WHERE $salud.sistemas_perfiles_usuarios_oas.id_oas_usuario='$id_oas_usuario' ";
				  $sql.="AND $salud._sistemas_usuarios.sistema_id='$SYSsistema_id' ";
				        
				  $result = $mysqli->query($sql);
				  $nr = $result->num_rows;
				  if ($nr>0)
				  {
					 $_acceso='SI';
					 $_login ='NO';
					 $SYSsistemas_perfiles_usuario = array();
					 while ($row = $result->fetch_array())
					 {
					   	$SYSsistemas_perfiles_usuario[]=$row["perfil_id"];
					 }
					 $_SESSION['sistemas_perfiles_usuario'] = $SYSsistemas_perfiles_usuario;
					        
				  
					}
					else
				    {
		              $_acceso='NO';
			          $_mensaje.='� El usuario '.$SYSusuario.' NO ESTA AUTORIZADO a utilizar el sistema '.$SYSsistema_id.' !';
			          $_login='SI';
				    }
				}
			}
			else
		  	{
           		$_acceso='NO';
           		$_mensaje='� El usuario '.$SYSusuario.' SE encuentra deshabilitado !';
           		$_login ='NO';
		  	}
		}
		else
		{
		    $_acceso='NO';
		    $_mensaje.='� El usuario '.$SYSusuario.' NO ESTA AUTORIZADO a utilizar el sistema '.$SYSsistema_id.' !';
		    $_login='SI';
		}
	}
  }
}

if ($_acceso=='SI')
    {
	 $_fechaactual = date("d/m/Y");
	 $_horaactual  = date("H:i:s");
     $SYSsesiondetalle.=$SYSpatharchivo.'<br>';
	 $mysqli->query ("UPDATE $salud._sesiones SET 
	        SYSsesiondetalle='$SYSsesiondetalle',
			SYSsesionfecha_ultimo='$_fechaactual',
			SYSsesionhora_ultimo='$_horaactual'
           WHERE _sessionid='$_sessionid'"); 
	}
   
 // FIN: USO DE SESION

//print $_mensaje;
//print $_acceso;
?>
