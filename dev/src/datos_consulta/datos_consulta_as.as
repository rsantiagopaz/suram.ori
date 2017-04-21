// ActionScript file
	import datos_consulta.diagnostico;
	
	import flash.events.Event;
	
	import mx.controls.Alert;
	import mx.events.CloseEvent;
	import mx.managers.PopUpManager;
	
	private var _idIngreso:String;
	[Bindable] private var _xmlTipoIngreso : XML = <tipoingresos></tipoingresos>;
	[Bindable] private var _xmlDiagnosticos : XML = <diagnosticos></diagnosticos>;
	[Bindable] private var _xmlDatosConsulta : XML = <datosconsulta></datosconsulta>;
	private var _xmlDatosPaciente:XML = <datospaciente></datospaciente>;
	private var _twDiagnostico : diagnostico;
	
	public function fncInit():void
	{
		_xmlDiagnosticos = <diagnosticos></diagnosticos>;
		_xmlDatosPaciente = <datospaciente></datospaciente>;
		_idIngreso = this.parentDocument.idIngreso;
		_xmlDatosPaciente.appendChild(this.parentDocument.xmlDatosPaciente.paciente);
		txiApenom.text = _xmlDatosPaciente.paciente.@apeynom;
		_xmlDiagnosticos.appendChild(this.parentDocument.xmlDatosPaciente.diagnostico);
		_xmlTipoIngreso = <tipoingresos></tipoingresos>;
		_xmlTipoIngreso.appendChild(this.parentDocument.xmlDatosPaciente.tipoingreso);
		_xmlDatosConsulta = <datosconsulta></datosconsulta>;
		_xmlDatosConsulta.appendChild(this.parentDocument.xmlDatosPaciente.datosconsulta);
		
		if (this.parentDocument.xmlDatosPaciente.datosconsulta.@embarazada == 'S'){
			this.currentState = 'embarazada';
			fncCargarDatosEmbarazada();
		}
		
		if (this.parentDocument.xmlDatosPaciente.paciente.@edad <= 12){
			this.currentState = 'niño';
			fncCargarDatosNiño();
		}
		fncCargarDatosConsulta();	    
		btnNuevoDiagnostico.addEventListener("click" ,fncAgregarDiagnostico);
		cmbTipoIngreso.addEventListener("change",fncChangeTipoIngreso);
		lblEstado.text = '';
		btnConfirmar.addEventListener("click" ,fncConfirmar);
		btnCancelar.addEventListener("click" ,fncCancelar);
	}
	
	public function get xmlDiagnosticos():XML{return _xmlDiagnosticos.copy();}
	public function get xmlDatosConsulta():XML{
		var tingreso : String = cmbTipoIngreso.selectedItem.@id_tipo_ingreso;
		var cbPeso : String = (this.currentState == 'niño') ? cmdPeso.selectedItem.@valor : '0';
		var cbTalla : String = (this.currentState == 'niño') ? cmdTalla.selectedItem.@valor : '0';
		var cbPerimetro : String = (this.currentState == 'niño') ? cmdPerimetro.selectedItem.@valor : '0';
		var pv : String = (this.currentState == 'niño') ? ((rbInicial.selected == true) ? 'S' : 'N') : '0';
		var pes : String = (this.currentState == 'niño') ? txtPeso.text : '0';
		var per : String = (this.currentState == 'niño') ? txtPerimetro.text : '0';
		var tall : String = (this.currentState == 'niño') ? txtTalla.text : '0';
		var emba : String = (this.currentState == 'embarazada') ? 'S' : 'N';
		var trimestre : String = (this.currentState == 'embarazada') ? cmbTrimestre.selectedItem.@valor : '0';
		var tension : String = (this.currentState == 'embarazada') ? txtPresionArterial.text : '';
		var _xmlConsulta : XML = 
								<datosconsulta>
									<id_persona>{this.parentDocument.xmlDatosPaciente.paciente.@id_persona}</id_persona>
									<id_ingreso>{_idIngreso}</id_ingreso>
									<motivo>{txtMotivo.text}</motivo>
									<observaciones>{txtObservacion.text}</observaciones>
									<id_tipo_ingreso>{tingreso}</id_tipo_ingreso>
									<primera_vez>{pv}</primera_vez>
									<trimestre>{trimestre}</trimestre>
									<tension_arterial>{tension}</tension_arterial>
									<embarazada>{emba}</embarazada>
									<perimetro>{per}</perimetro>
									<peso>{pes}</peso>
									<talla>{tall}</talla>
									<peso_edad>{cbPeso}</peso_edad>
									<talla_edad>{cbTalla}</talla_edad>
									<perc_perimetro>{cbPerimetro}</perc_perimetro>
								</datosconsulta>;	
					
		return _xmlConsulta.copy()
	}
	
	private function fncCancelar(e:Event):void{
		this.parentDocument.ConfirmDatosConsulta = false;
		lblEstado.text = 'Pestaña Cancelada';
	}
	
	private function fncConfirmar(e:Event):void{
		var tienePrincipal:Boolean = false;
		for (var i:int = 0; i < _xmlDiagnosticos.diagnostico.length(); i++)
		{ 
			if (_xmlDiagnosticos.diagnostico[i].@tipo_diagnostico == 'P') {
				tienePrincipal = true;
				break;				
			}
		}
		if (_xmlDiagnosticos.hasOwnProperty("diagnostico")&&tienePrincipal&&txtMotivo.text!=''){
			this.parentDocument.ConfirmDatosConsulta = true;
			lblEstado.text = 'Pestaña Confirmada';
		}else{
			Alert.show("Debe Cargar el/los Diagnosticos (con uno Principal) y el Motivo","ERROR");
		}
	}
	
	private function fncCargarDatosConsulta():void
	{
		for(var i:uint = 0; i<cmbTipoIngreso.dataProvider.length; i++)
		{	
			var item:String = cmbTipoIngreso.dataProvider[i].@id_tipo_ingreso;					     	
			 if(item == _xmlDatosConsulta.datosconsulta.@id_tipo_ingreso)
			 {					     	  	
			 	cmbTipoIngreso.selectedIndex  = i;
			    break;
			}
		}
		if (_xmlDatosConsulta.datosconsulta.@primera_vez == 'S')
		{
			rbInicial.selected = true;
			rbUlterior.selected = false;
		}else{
			rbInicial.selected = false;
			rbUlterior.selected = true;
		}
		txtMotivo.text = _xmlDatosConsulta.datosconsulta.@motivo;
		txtObservacion.text = _xmlDatosConsulta.datosconsulta.@observaciones;
	}
	
	private function fncCargarDatosEmbarazada():void
	{
		for(var i:uint = 0; i<cmbTrimestre.dataProvider.length; i++)
		{	
			var item:String = cmbTrimestre.dataProvider[i].@valor;					     	
			 if(item == _xmlDatosConsulta.datosconsulta.@trimestre)
			 {					     	  	
			 	cmbTrimestre.selectedIndex  = i;
			    break;
			}
		}
		txtPresionArterial.text = _xmlDatosConsulta.datosconsulta.@tension_arterial;
		
	}
	
	private function fncCargarDatosNiño():void
	{
		var i:uint;
		var item:String;
		for(i = 0; i<cmdTalla.dataProvider.length; i++)
		{	
			item = cmdTalla.dataProvider[i].@valor;					     	
			 if(item == _xmlDatosConsulta.datosconsulta.@talla_edad)
			 {					     	  	
			 	cmdTalla.selectedIndex  = i;
			    break;
			}
		}
		for(i = 0; i<cmdPeso.dataProvider.length; i++)
		{	
			item = cmdPeso.dataProvider[i].@valor;					     	
			 if(item == _xmlDatosConsulta.datosconsulta.@peso_edad)
			 {					     	  	
			 	cmdPeso.selectedIndex  = i;
			    break;
			}
		}
		for(i = 0; i<cmdPerimetro.dataProvider.length; i++)
		{	
			item = cmdPerimetro.dataProvider[i].@valor;					     	
			 if(item == _xmlDatosConsulta.datosconsulta.@perc_perimetro)
			 {					     	  	
			 	cmdPerimetro.selectedIndex  = i;
			    break;
			}
		}
		txtPeso.text = _xmlDatosConsulta.datosconsulta.@peso;
		txtTalla.text = _xmlDatosConsulta.datosconsulta.@talla;
		txtPerimetro.text = _xmlDatosConsulta.datosconsulta.@perimetro;
	}
	 
	private function fncChangeTipoIngreso(e:Event):void
	{
		if (cmbTipoIngreso.selectedItem.@id_tipo_ingreso == 2 )	{
			this.currentState = 'embarazada';
		}
		else{
			this.currentState = '';
		}	
	}
	
	private function fncAgregarDiagnostico(e:Event):void
	{
		_twDiagnostico = new diagnostico;
		_twDiagnostico.addEventListener("EventConfirmarDiagnostico",fncGrabarDiagnostico);
		PopUpManager.addPopUp(_twDiagnostico,this,true);
		PopUpManager.centerPopUp(_twDiagnostico);
	}
	
	private function fncGrabarDiagnostico(e:Event):void
	{
		if (_twDiagnostico.xmlDiagnostico.@tipo_diagnostico=='P'){
			for (var i:int = 0; i < _xmlDiagnosticos.diagnostico.length(); i++)
			{ 
				_xmlDiagnosticos.diagnostico[i].@tipo_diagnostico = 'S';
			} 		
		}
		_xmlDiagnosticos.appendChild(_twDiagnostico.xmlDiagnostico);
		PopUpManager.removePopUp(e.target as diagnostico);	
	}
	
	private function fncGuardarEditarDiagnostico(e:Event):void
	{
		_xmlDiagnosticos.diagnostico[(gridDiagnosticos.selectedItem as XML).childIndex()] = _twDiagnostico.xmlDiagnostico;
		PopUpManager.removePopUp(e.target as diagnostico);	
	}
	
	public function fncEditarDiagnostico():void
	{
		_twDiagnostico = new diagnostico;
		_twDiagnostico.xmlDiagnostico =  (gridDiagnosticos.selectedItem as XML).copy();
		_twDiagnostico.addEventListener("EventConfirmarDiagnostico",fncGuardarEditarDiagnostico);
		PopUpManager.addPopUp(_twDiagnostico,this,true);
		PopUpManager.centerPopUp(_twDiagnostico);
	}
	
	public function fncEliminarDiagnostico():void
	{
		Alert.show("¿Realmente desea Eliminar el Diagnóstico "+ gridDiagnosticos.selectedItem.@descripcion+"?", "Confirmar", Alert.OK | Alert.CANCEL, this, fncConfirmEliminarDiagnostico, null, Alert.OK);
	}
	
	private function fncConfirmEliminarDiagnostico(e:CloseEvent):void
	{
		if (e.detail==Alert.OK){
			delete _xmlDiagnosticos.diagnostico[(gridDiagnosticos.selectedItem as XML).childIndex()]; 
		}
	}
	