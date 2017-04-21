<?php
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
 
</script> 
';


?>



<form action="persona_fisica_agregar.php" method="POST"  enctype="multipart/form-data" name="f1" id="f1">
  <div align="center"> 
    <table border="0" cellpadding="4" cellspacing="0">
      <tr> 
        <td width="122" align="right" valign="middle"> <p align="right"><b><font face="Arial" size="2" color="#000080">Apellido 
            y Nombre:</font></b></p></td>
        <td colspan="3" valign="top"><font color="#000080" size="2" face="Arial"> 
          <input name="persona_nombre" type="text" id="persona_nombre" value="<? print $persona_nombre; ?>" size="50" maxlength="100">
          * 
          <script language="javascript">
document.f1.persona_nombre.focus();
		  
</script>
          </font> </td>
      </tr>
      <tr> 
        <td align="right" valign="middle"> <p align="right"><b><font face="Arial" size="2" color="#000080">Domicilio:</font></b></p></td>
        <td colspan="3" valign="top"><font color="#000080" size="2" face="Arial"> 
          <input name="persona_domicilio" type="text" id="persona_domicilio" value="<? print $persona_domicilio; ?>" size="50" maxlength="100">
          </font> <font color="#000080" size="2" face="Arial">&nbsp; </font> </td>
      </tr>
      <tr> 
        <td align="right" valign="middle"><b><font face="Arial" size="2" color="#000080">Dni:</font></b></td>
        <td width="106" valign="top"><font color="#000080" size="2" face="Arial"> 
          <input name="persona_dni" type="text" id="persona_dni" value="<? print $persona_dni; ?>" size="8" maxlength="8">
          * </font></td>
        <td valign="top"><div align="right"><b><font face="Arial" size="2" color="#000080">Nacionalidad:</font></b></div></td>
        <td valign="top"><font color="#000080" size="2" face="Arial"> 
          <input name="persona_nacionalidad" type="text" id="persona_nacionalidad2" value="<? print $persona_nacionalidad; ?>" size="20" maxlength="15">
          </font></td>
      </tr>
      <tr> 
        <td align="right" valign="middle"><b><font face="Arial" size="2" color="#000080">Cuil:</font></b></td>
        <td valign="top"><font color="#000080" size="2" face="Arial"> 
          <input name="persona_cuil" type="text" id="persona_cuil2" value="<? print $persona_cuil; ?>" size="11" maxlength="12">
          </font> </td>
        <td width="99" valign="top"><div align="right"><b><font face="Arial" size="2" color="#000080">Sexo:</font></b></div></td>
        <td width="196" valign="top"><select name="persona_sexo" id="persona_sexo">
            <option value="-"></option>
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
          </select></td>
      </tr>
      <tr> 
        <td align="right" valign="middle"><div align="right"><b><font face="Arial" size="2" color="#000080">Instrucci&oacute;n:</font></b></div></td>
        <td valign="top"><div align="left"><font color="#000080" size="2" face="Arial"> 
            <input name="persona_instruccion" type="text" id="persona_instruccion" value="<? print $persona_instruccion; ?>" size="21" maxlength="20">
            </font></div></td>
        <td valign="top"><div align="right"><b><font face="Arial" size="2" color="#000080">Estado 
            Civil:</font></b></div></td>
        <td valign="top"><select name="persona_estado_civil" id="select2">
            <option value="-"></option>
            <option value="S">Soltero</option>
            <option value="C">Casado</option>
            <option value="D">Divorciado</option>
            <option value="V">Viudo</option>
            <option value="O">Concubino</option>
          </select></td>
      </tr>
      <tr> 
        <td valign="top"><div align="right"><b><font face="Arial" size="2" color="#000080">Localidad:</font></b></div></td>
        <td colspan="3" valign="top"><input name="persona_localidad_id" type="text" id="persona_localidad_id" size="5" maxlength="5" value="<?php print $persona_localidad_id; ?>"> 
          <font color="#000080" size="2" face="Arial">*</font> <input type="button" name="Submit" value="?" onClick="pidoLocalidad('persona_localidad_id','persona_localidad_txt');" > 
          <input name="persona_localidad_txt" type="text" id="persona_localidad_txt" value="<?php print $persona_localidad_txt; ?>" size="40"> 
          <script language="javascript">
//document.f1.persona_localidad_txt.disabled=true;
		  
</script> </td>
      </tr>
      <tr> 
        <td valign="top"></td>
        <td valign="top"><font color="#000080" size="2" face="Arial"> 
          <input type="submit" value="Agregar" name="B1">
          </font></td>
        <td valign="top">&nbsp;</td>
        <td valign="top">&nbsp;</td>
      </tr>
      <tr> 
        <td valign="top"></td>
        <td colspan="2" valign="top"><font color="#000080" size="2" face="Arial">* 
          Campos obligatorios</font></td>
        <td valign="top">&nbsp;</td>
      </tr>
    </table>
  </div>
  <input name="SYSpatharchivo" type="hidden" id="SYSpatharchivo" value="<? print $SYSpatharchivo; ?>">
  <input name="_variable_dni" type="hidden" id="_variable_dni" value="<? print $_variable_dni; ?>">
  <input name="persona_tipo" type="hidden" id="persona_tipo" value="F">
</form>
