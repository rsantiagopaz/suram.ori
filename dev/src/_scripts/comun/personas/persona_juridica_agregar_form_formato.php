<?
echo 
"
<script> 
var NombreArchivo;

function pidoLocalidad(_variable_localidad_id, _variable_localidad)
 { 
   ";
   print "NombreArchivo='".$SYSpathraiz.'_scripts/comun/localidades/localidad_consultar.php?_variable_localidad_id='."';";
echo '
   window.open(NombreArchivo+_variable_localidad_id+"&_variable_localidad="+_variable_localidad,"Localidades","width=400,height=400,scrollbars=yes");
 } 
';


//------CONTROL DE CAMPOS EN JAVASCRIPT
echo "
function ControldeCampos()
{
mensaje='';

//if (f1.persona_dni.value=='') 
//	{mensaje=mensaje + 'No ingresó el DNI de la persona \\n';
//	document.f1.persona_dni.focus();}
//	else if (IsNumeric(f1.persona_dni.value) == false) 
//	   mensaje=mensaje + 'El DNI debe ser un valor numérico \\n';
//	else if (f1.persona_dni.value.length != 8)
//	   mensaje=mensaje + 'El DNI debe ser de 8 dígitos \\n';

if (f1.persona_cuit.value=='') 
	{mensaje=mensaje + 'No ingresó el CUIT de la persona \\n';
	document.f1.persona_cuit.focus();}
	else if (IsNumeric(f1.persona_cuit.value) == false) 
	   mensaje=mensaje + 'El CUIT debe ser un valor numérico \\n';

	   
if (f1.persona_nombre.value=='') 	
	{mensaje=mensaje + 'No ingresó la RAZÓN SOCIAL \\n';
	document.f1.persona_nombre.focus();}

if (f1.persona_localidad_id.value=='') 
	{mensaje=mensaje + 'No el ingresó el código de LOCALIDAD\\n';
	document.f1.persona_localidad_id.focus();}
	else if (IsNumeric(f1.persona_localidad_id.value) == false) 
	   mensaje=mensaje + 'El código de LOCALIDAD debe ser un valor numérico \\n';
	else if (f1.persona_localidad_id.value.length != 5)
	   mensaje=mensaje + 'El código de LOCALIDAD debe ser de 5 dígitos \\n';
	
	
if (mensaje!='')	
	{
	alert (mensaje);
	return false;
	}
	
}//de la funcion


function IsNumeric(strString)
   //  check for valid numeric strings	
   {
   var strValidChars = '0123456789.-';
   var strChar;
   var blnResult = true;

   if (strString.length == 0) return false;

   //  test strString consists of valid characters listed above
   for (i = 0; i < strString.length && blnResult == true; i++)
      {
      strChar = strString.charAt(i);
      if (strValidChars.indexOf(strChar) == -1)
         {
         blnResult = false;
         }
      }
   return blnResult;
   }

</script> 
     ";
?> 
<form action="persona_juridica_agregar.php" method="POST" onSubmit="return ControldeCampos()" enctype="multipart/form-data" name="f1" id="f1">
  <div align="center">
    <table width="100%" border="0" cellpadding="4" cellspacing="0">
      <tr> 
        <td width="26%" align="right" valign="middle"> 
          <p align="right"><b><font face="Arial" size="2" color="#000080">Raz&oacute;n 
            Social :</font></b></p></td>
        <td colspan="4" valign="top"><font color="#000080" size="2" face="Arial"> 
          <input name="persona_nombre" type="text" id="persona_nombre" value="<? print $persona_nombre; ?>" size="50" maxlength="100">
          * 
          <script language="">
document.f1.persona_nombre.focus();
		  
</script>
          </font></td>
      </tr>
      <tr> 
        <td align="right" valign="middle"> <p align="right"><b><font face="Arial" size="2" color="#000080">Domicilio:</font></b></p></td>
        <td colspan="4" valign="top"><font color="#000080" size="2" face="Arial"> 
          <input name="persona_domicilio" type="text" id="persona_domicilio" value="<? print $persona_domicilio; ?>" size="50" maxlength="100">
          </font> <font color="#000080" size="2" face="Arial">&nbsp; </font> </td>
      </tr>
      <tr> 
        <td align="right" valign="middle"><b><font face="Arial" size="2" color="#000080">Cuit:</font></b></td>
        <td colspan="2" valign="top"><font color="#000080" size="2" face="Arial">
          <input name="persona_cuit" type="text" id="persona_cuit" value="<? print $persona_cuit; ?>" size="12" maxlength="11">
          * </font></td>
        <td width="18%" valign="top">
<div align="right"><b></b></div></td>
        <td width="36%" valign="top"><font color="#000080" size="2" face="Arial">&nbsp; 
          </font></td>
      </tr>
      <tr> 
        <td valign="top"><div align="right"><b><font face="Arial" size="2" color="#000080">Localidad:</font></b></div></td>
        <td colspan="4" valign="top"><input name="persona_localidad_id" type="text" id="persona_localidad_id" size="5" maxlength="5"> 
          <font color="#000080" size="2" face="Arial">*</font> <input type="button" name="Submit" value="?" onClick="pidoLocalidad('persona_localidad_id','persona_localidad_txt');" > 
          <input name="persona_localidad_txt" type="text" id="persona_localidad_txt" size="40">
        </td>
      </tr>
      <tr> 
        <td valign="top"></td>
        <td colspan="2" valign="top"><font color="#000080" size="2" face="Arial"> 
          <input type="submit" value="Agregar" name="B1">
          </font></td>
        <td valign="top"><script language="">
document.f1.persona_localidad_txt.disabled=true;
		  
</script></td>
        <td valign="top">&nbsp;</td>
      </tr>
      <tr> 
        <td valign="top"></td>
        <td colspan="3" valign="top"><font color="#000080" size="2" face="Arial">* 
          Campos obligatorios</font></td>
        <td valign="top">&nbsp;</td>
      </tr>
    </table>
  </div>
  <input name="SYSpatharchivo" type="hidden" id="SYSpatharchivo" value="<? print $SYSpatharchivo; ?>">
  <input name="_variable_dni" type="hidden" id="_variable_dni" value="<? print $_variable_dni; ?>">
  <input name="persona_tipo" type="hidden" id="persona_tipo" value="J">
</form>
