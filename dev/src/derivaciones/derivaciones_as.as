// ActionScript file
	import flash.events.Event;
	
	import mx.controls.Alert;
	
	private var _idIngreso:String;
	private var _derivacion:String;
	[Bindable] private var _xmlEspecialidades : XML = <especialidades></especialidades>;
	private var _xmlDatosPaciente:XML = <datospaciente></datospaciente>;
	
	public function fncInit():void
	{
		_xmlEspecialidades = <especialidades></especialidades>;
		_xmlDatosPaciente = <datospaciente></datospaciente>;
		_xmlDatosPaciente.appendChild(this.parentDocument.xmlDatosPaciente.paciente);
		txiApenom.text = _xmlDatosPaciente.paciente.@apeynom;
		_xmlEspecialidades.appendChild(this.parentDocument.xmlDatosPaciente.especialidad);
		lblEstado.text = '';
		btnConfirmar.addEventListener("click" ,fncConfirmar);
		btnCancelar.addEventListener("click" ,fncCancelar);
		_derivacion = this.parentDocument.xmlDatosPaciente.datosconsulta.@derivacion;
		if (_derivacion == '1'){
			if (this.parentDocument.xmlDatosPaciente.datosconsulta.@tipo_derivacion == 'A'){
				rbAmbulatoria.selected = true;
				rbInternacion.selected = false;
				for(var i:uint = 0; i<cmbEspecialidades.dataProvider.length; i++){	
					var item:String = cmbEspecialidades.dataProvider[i].@id_especialidad;					     	
					 if(item == this.parentDocument.xmlDatosPaciente.datosconsulta.@id_especialidad){					     	  	
					 	cmbEspecialidades.selectedIndex  = i;
					    break;
					}
				}
			}else{
				rbAmbulatoria.selected = false;
				rbInternacion.selected = true;
				cmbEspecialidades.selectedIndex = -1;
			}
		}else{
			_derivacion = '0';
		}
	}
	
	private function fncCancelar(e:Event):void{
		this.parentDocument.ConfirmDerivaciones = false;
		lblEstado.text = 'Pestaña Cancelada';
	}
	
	private function fncConfirmar(e:Event):void{
		if (rbAmbulatoria.selected == true || rbInternacion.selected == true){
			_derivacion = '1';
			this.parentDocument.ConfirmDerivaciones = true;
			lblEstado.text = 'Pestaña Confirmada';
		}else{
			Alert.show("Seleccione el Tipo de Derivacion","ERROR");
		}
	}
	
	public function get xmlDerivacion():XML{
		var id_especialidad : String = cmbEspecialidades.selectedItem.@id_especialida ? cmbEspecialidades.selectedItem.@id_especialidad : '0';
		var tipo_derivacion : String = (rbAmbulatoria.selected == true) ? 'A' : 'I';
		var _xmlDerivacion : XML = 
								<datosderivacion>
									<derivacion>{_derivacion}</derivacion>
									<tipo_derivacion>{tipo_derivacion}</tipo_derivacion>
									<id_especialidad>{id_especialidad}</id_especialidad>
								</datosderivacion>;	
					
		return _xmlDerivacion.copy()
	}
	
	