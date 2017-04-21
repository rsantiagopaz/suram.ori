			<form action="login.php" method="POST" enctype="multipart/form-data" name="f1" id="f1">
                <div align="center"> 
                  <table border="0" cellpadding="4" cellspacing="0">
                    <tr> 
                      <td valign="top"> <p align="right"><b><font face="Arial" size="2" color="#000080">Usuario:</font></b></p></td>
                      <td valign="top"><font color="#000080" size="2" face="Arial"> 
                        
          <input name="SYSusuario" type="text" id="SYSusuario" size="10" maxlength="20" value="<? print $SYSusuario; ?>">
                        </font> </td>
                    </tr>
                    <tr> 
                      <td valign="top"> <p align="right"><b><font face="Arial" size="2" color="#000080">Contrase&ntilde;a:</font></b></p></td>
                      <td valign="top"><font color="#000080" size="2" face="Arial">
                        <input name="SYSpassword" type="password" id="SYSpassword" size="10" maxlength="10">
                        </font> </td>
                    </tr>
                    <?
						if ($tipo=='sale')
						 {
						  // Imagen a subir Nº1
                          print '<tr>';
                          print '  <td valign="top" align="right"><font face="Arial" size="2" color="#000080">Image 1: </font></td>';
                          print '  <td valign="top">';
						  print '   <input name="imagen1" type="file" id="imagen1" size="30">';
						  print '  </td>';
                          print '</tr>';
						  // Imagen a subir Nº2
                          print '<tr>';
                          print '  <td valign="top" align="right"><font face="Arial" size="2" color="#000080">Image 2: </font></td>';
                          print '  <td valign="top">';
						  print '   <input name="imagen2" type="file" id="imagen2" size="30">';
						  print '  </td>';
                          print '</tr>';
						  // Imagen a subir Nº3
                          print '<tr>';
                          print '  <td valign="top" align="right"><font face="Arial" size="2" color="#000080">Image 3: </font></td>';
                          print '  <td valign="top">';
						  print '   <input name="imagen3" type="file" id="imagen3" size="30">';
						  print '  </td>';
                          print '</tr>';
						  // Imagen a subir Nº4
                          print '<tr>';
                          print '  <td valign="top" align="right"><font face="Arial" size="2" color="#000080">Image 4: </font></td>';
                          print '  <td valign="top">';
						  print '   <input name="imagen4" type="file" id="imagen4" size="30">';
						  print '  </td>';
                          print '</tr>';
						 }
						?>
                    <tr> 
                      <td valign="top"><script>document.f1.SYSusuario.focus();</script></td>
                      <td valign="top"><font color="#000080" size="2" face="Arial"> 
                        <input type="submit" value="Ingresar" name="B1">
                        </font></td>
                    </tr>
                  </table>
                </div>
                
  <input name="SYSpatharchivo" type="hidden" id="SYSpatharchivo" value="<? print $SYSpatharchivo; ?>">
  <input name="mensaje" type="hidden" id="mensaje" value="<? print $mensaje; ?>">
</form>

