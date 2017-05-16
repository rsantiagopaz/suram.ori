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
 
 
 $_sessionid=$HTTP_SESSION_VARS['_sessionid']; 
// include($SYSpathraiz.'config.php');
 $SERVIDOR = "localhost";
// $USUARIO = "root";
// $PASSWORD = "punchi";
// $BASE = "sursde";

/*
 $USUARIO = "salud1";
 $PASSWORD = "argentina";
 $BASE = "salud1";
*/
 
 
 $USUARIO = "root";
 $PASSWORD = "";
 $BASE = "salud1";




 
 $link_salud1 = @mysql_connect($SERVIDOR, $USUARIO, $PASSWORD);
 @mysql_select_db($BASE, $link_salud1);

   
  
 $_acceso='NO';
 
 if (empty($_sessionid))
  {
   $_acceso='NO';
   $_mensaje='� NO EXISTE SESION (si el mensaje persiste, comun�quelo al administrador de sistemas) !';
   $_login ='SI';
  }

 else

  { 
   $result=mysql_query("SELECT * FROM _sesiones WHERE _sessionid='$_sessionid'");
   $num=mysql_numrows($result);
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
      $SYSusuario            = mysql_result($result,0,SYSusuario);
	  $SYSsesiondetalle      = mysql_result($result,0,SYSsesiondetalle);
	  $SYSsesionfecha_cierre = mysql_result($result,0,SYSsesionfecha_cierre);
	  $SYSsesionhora_cierre  = mysql_result($result,0,SYSsesionhora_cierre);
	  $SYSsesionfecha_ultimo = mysql_result($result,0,SYSsesionfecha_ultimo);
	  $SYSsesionhora_ultimo  = mysql_result($result,0,SYSsesionhora_ultimo);

	  if (empty($SYSusuario))
	   {
		$_acceso='NO';
		$_mensaje='� NO SE ENCONTRO EL USUARIO VINCULADO A LA SESION: '.$_sessionid.' !';
       } 
	  else
	   {
         // Verifico si el usuario est� activo es decir que "SYSusuario_estado = 1" 
         $result=mysql_query("SELECT * FROM _usuarios 
		                       LEFT JOIN _organismos_areas_usuarios
							          ON _organismos_areas_usuarios.SYSusuario = _usuarios.SYSusuario
		                       LEFT JOIN _organismos_areas
							          ON _organismos_areas.organismo_area_id = _organismos_areas_usuarios.organismo_area_id
		                       LEFT JOIN _organismos
							          ON _organismos.organismo_id = _organismos_areas.organismo_id
		                        WHERE _usuarios.SYSusuario= BINARY '$SYSusuario'");
		 
		 
         $num=mysql_numrows($result);
         if ($num==1)
          {
		   $SYSusuario_nombre            = mysql_result($result,0,SYSusuarionombre);
		   $SYSusuario_estado            = mysql_result($result,0,SYSusuario_estado);
		   $SYSusuario_organismo_id      = mysql_result($result,0,organismo_id);
		   $SYSusuario_organismo         = mysql_result($result,0,organismo);
		   $SYSusuario_organismo_area_id = mysql_result($result,0,organismo_area_id);
		   $SYSusuario_organismo_area    = mysql_result($result,0,organismo_area);
		   $SYSusuario_organismo_area_mesa_entrada = mysql_result($result,0,organismo_area_mesa_entrada);
		   //$SYSusuario_ = mysql_result($result,0,);
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
                  $result=mysql_query("SELECT SYSusuario, sistema_id FROM _sistemas_usuarios 
				                        WHERE SYSusuario='$SYSusuario' 
										  AND sistema_id='$SYSsistema_id'
								      ");
                  $num=mysql_numrows($result);
                  if ($num==1)
                    {
                     $_acceso='SI';
                     $_login ='NO';
					 
					 
					 
					 // Agregado el 2/1/2007
		             // Obtengo un arreglo $SYSsistemas_perfiles_usuario con los perfiles del usuario
		             $result=mysql_query("SELECT * FROM _sistemas_perfiles_usuarios 
				                                   WHERE SYSusuario= BINARY '$SYSusuario' 
								         ");
                      $num=mysql_numrows($result);
                      if ($num>0)
					   {
					    // Cargo en el arreglo SYSsistemas_perfiles_usuarios los perfiles del usuario SYSusuario
						// para ver si un perfil est� en el arreglo, usar la func. in_array($perfil_id,$SYSsistemas_perfiles_usuario)
						// devolver� true si est� o false.
						$SYSsistemas_perfiles_usuario='';
						$indice=0;
						if ($row=mysql_fetch_array($result) )
                         {
						  do
						   {
						    $indice++;
							$SYSsistemas_perfiles_usuario[$indice]=$row["perfil_id"];
						   }
						  while($row=mysql_fetch_array($result)); 
						  /*
   						  print "<br>Arreglo con perfiles: <br>";
						  $i=0;
						  foreach($SYSsistemas_perfiles_usuario AS $elemento)
						   {
						    $i++;
						    print "<b>$i)</b>$elemento ";
						   }
						  print "<br>";
						  */
						 }
					   }
		             // ------------------------------------
					 
					 
					 

			         // Verifica si la sesi�n no est� cerrada  
			         if(empty($SYSsesionfecha_cierre) OR $SYSsesionfecha_cierre=='0000-00-00')
		               {
		                $_acceso='SI';
		               }
		              else
		               {
		                $_acceso='NO';
			            $_mensaje.='� La sesi�n ya se encuentra cerrada !';
			            $_login='SI';
		               } 
                     // FIN: Verifica si la sesi�n no est� cerrada
					 
				    }
				   else
				    {
		              $_acceso='NO';
			          $_mensaje.='� El usuario '.$SYSusuario.' NO ESTA AUTORIZADO a utilizar el sistema '.$SYSsistema_id.' !';
			          $_login='SI';
				    }
				  // FIN: Veo en tabla _sistemas_ususarios si est� autorizado el usuario
				 }
			    // FIN: Verifico si el usuario est� autorizado a utilizar el sistema.			   
			    
			   
			   
			  }
			 else
			  {
               $_acceso='NO';
               $_mensaje='� El usuario '.$SYSusuario.' no est� AUTORIZADO PARA EL USO DE NINGUN SISTEMA !';
               $_login ='NO';
			  }
	
          }
		 else
		  {
           $_acceso='NO';
           $_mensaje='� El usuario '.$SYSusuario.' NO EXISTE !';
           $_login ='NO';
		  }
		     
			
         // FIN: Verifico si el usuario est� activo es decir que "SYSusuario_estado = 1" 
		 			
	   } 
     }
  }	 


  if ($_acceso=='SI')
    {
	 $_fechaactual = date("d/m/Y");
	 $_horaactual  = date("H:i:s");
     $SYSsesiondetalle.=$SYSpatharchivo.'<br>';
	 mysql_query ("UPDATE _sesiones SET 
	        SYSsesiondetalle='$SYSsesiondetalle',
			SYSsesionfecha_ultimo='$_fechaactual',
			SYSsesionhora_ultimo='$_horaactual'
           WHERE _sessionid='$_sessionid'"); 
	}
   
 // FIN: USO DE SESION

//print $_mensaje;
//print $_acceso;
?>
