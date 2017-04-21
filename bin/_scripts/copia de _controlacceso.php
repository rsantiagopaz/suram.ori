<?php
// header("Cache-Control: no-cache, must-revalidate");
header('Expires: Wed, 23 Dec 1980 00:30:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
 
 // USO DE SESION
 session_start();
 $_sessionid=$HTTP_SESSION_VARS['_sessionid']; 
 include($SYSpathraiz.'config.php');
 include($SYSpathraiz.'_scripts/fechayhora.inc');
 if (empty($_sessionid))
  {
   $_acceso='NO';
   $mensaje='NO EXISTE SESION (si el mensaje persiste, comuníquelo al administrador de sistemas)';
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
      $mensaje='NO SE ENCONTRO SESION: '.$_sessionid.'<br>(es probable que UD. no haya ingresado sus datos de acceso aún)';
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
        
		
      if (!empty($SYSsesionfecha_cierre))
	   {
		$_acceso='NO';
		$mensaje='>>LA SESIÓN YA SE ENCUENTRA CERRADA: '.$_sessionid;
	   }

	  if (empty($SYSusuario))
	   {
		$_acceso='NO';
		$mensaje='NO SE EN CONTRO EL USUARIO VINCULADO A LA SESION: '.$_sessionid;
       } 
	  else
	   {
         // Verifico si el usuario está activo es decir que "SYSusuario_estado = 1" 
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
		   $SYSsistemas_perfiles_usuario='';
		   
		   
		   if ($SYSusuario_estado==1)
		      {
               $_acceso='SI';
               $_login ='NO';
			   
               // Verifico si el usuario está autorizado a utilizar el sistema.
			   if (empty($SYSsistema_id))
			     {
                  $_acceso='NO';
                  $_login ='NO';
				  $mensaje='ERROR DE SISTEMA: SYSsistema_id vacío.';
   				 }
				else
				 {
				  // Veo en tabla _sistemas_ususarios si está autorizado el usuario
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
						// para ver si un perfil está en el arreglo, usar la func. in_array($perfil_id,$SYSsistemas_perfiles_usuario)
						// devolverá true si está o false.
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

					 

			         // Verifica si la sesión no está cerrada  
			         if(empty($SYSsesionfecha_cierre) OR $SYSsesionfecha_cierre=='0000-00-00')
		               {
		                $_acceso='SI';
		               }
		              else
		               {
		                $_acceso='NO';
			            $mensaje.='<BR>La sesión ya se encuentra cerrada.';
			            $_login='SI';
		               } 
                     // FIN: Verifica si la sesión no está cerrada
					 
				    }
				   else
				    {
		              $_acceso='NO';
			          $mensaje.='<BR>¡ El usuario <b>'.$SYSusuario.'</b> NO ESTA AUTORIZADO a utilizar el sistema <b>'.$SYSsistema_id.'</b> !.';
			          $_login='SI';
				    }
				  // FIN: Veo en tabla _sistemas_ususarios si está autorizado el usuario
				 }
			    // FIN: Verifico si el usuario está autorizado a utilizar el sistema.			   
			    
			   
			   
			  }
			 else
			  {
               $_acceso='NO';
               $mensaje='¡ El usuario <b>'.$SYSusuario.'</b> no está AUTORIZADO PARA EL USO DE NINGUN SISTEMA !';
               $_login ='NO';
			  }
	
          }
		 else
		  {
           $_acceso='NO';
           $mensaje='¡ El usuario <b>'.$SYSusuario.'</b> NO EXISTE !';
           $_login ='NO';
		  }
		     
			
         // FIN: Verifico si el usuario está activo es decir que "SYSusuario_estado = 1" 
		 			
	   } 
     }
  }	 


  if ($_acceso=='NO')
    {
  	 if ($_login=='SI')
	   {
   	    print '<br><br><br><center><font face="arial" color="#000088" size="4"><b>CARGANDO PAGINA DE ACCESO DEL USUARIO...</b></font></center>';
	    print '<br><br><center>';
	    print '<a href="'.$SYSpathraiz.'_scripts/login_form.php?SYSpatharchivo='.$SYSpatharchivo.'"><font face="arial" size="2">De un click aquí para ingresar sus datos de usuario si no redirecciona automáticamente...</font></a>';
	    print '</center><br><br>';
		$_redireccion = $SYSpathraiz.'_scripts/login_form.php?SYSpatharchivo='.$SYSpatharchivo;
		if (!empty($mensaje))
		 {
		  $_redireccion.='&mensaje='.$mensaje;
		 }
		print '<script>window.location = "'.$_redireccion.'";</script>';
	   } 
	  else
	   {
   	    print '<br><br><br><center><font face="arial" size="4"><b>¡ ACCESO DENEGADO !</b></font></center>';
	    print '<br><br><center><font face="arial" size="2">'.$mensaje.'</font></center>';
	   }

	 exit;
   
    }
   else
    {
	 $SYSsesiondetalle.=$SYSpatharchivo.'<br>';
	 mysql_query ("UPDATE _sesiones SET 
	        SYSsesiondetalle='$SYSsesiondetalle',
			SYSsesionfecha_ultimo='$_fechaactual',
			SYSsesionhora_ultimo='$_horaactual'
           WHERE _sessionid='$_sessionid'"); 
	}
   
 // FIN: USO DE SESION


?>