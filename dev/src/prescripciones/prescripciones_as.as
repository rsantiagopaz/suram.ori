// ActionScript file
	import clases.HTTPServices;
	
	import flash.events.Event;
	
	import mx.controls.Alert;
	import mx.events.CloseEvent;
	import mx.managers.PopUpManager;
	
	import prescripciones.medicamento;
	
	include "../control_acceso.as";
	
	[Bindable] private var _xmlPrescripciones:XML = <prescripciones></prescripciones>;
	private var _xmlDatosPaciente:XML = <datospaciente></datospaciente>;
	private var _twMedicamentos:medicamento;
	private var _idIngreso:String;
	private var httpPrescripciones:HTTPServices = new HTTPServices;
	
	public function fncInit():void
	{
		_xmlPrescripciones = <prescripciones></prescripciones>;
		_xmlDatosPaciente = <datospaciente></datospaciente>;
		_idIngreso = this.parentDocument.idIngreso;
		_xmlDatosPaciente.appendChild(this.parentDocument.xmlDatosPaciente.paciente);
		txiApenom.text = _xmlDatosPaciente.paciente.@apeynom;
		_xmlPrescripciones.appendChild(this.parentDocument.xmlDatosPaciente.prescripcion);
		httpPrescripciones.url = "prescripciones/prescripciones.php";
		httpPrescripciones.addEventListener("acceso",acceso);
		lblEstado.text = '';
		btnNuevaPrescripcion.addEventListener("click" ,fncAgregarPrescripcion);
		btnConfirmar.addEventListener("click" ,fncConfirmar);
		btnCancelar.addEventListener("click" ,fncCancelar);
		btnImprimirPrescripciones.addEventListener("click" ,fncImprimirPrescripciones);
		btnImprimirPrescripciones.enabled = false;
	}
	
	public function get xmlPrescripciones():XML{return _xmlPrescripciones.copy();}
	
	private function fncCancelar(e:Event):void
	{
		this.parentDocument.ConfirmPrescripciones = false;
		btnImprimirPrescripciones.enabled = false;
		lblEstado.text = 'Pestaña Cancelada';
	}
	
	private function fncConfirmar(e:Event):void
	{		
		this.parentDocument.ConfirmPrescripciones = true;
		btnImprimirPrescripciones.enabled = true;
		lblEstado.text = 'Pestaña Confirmada';		
	}
	
	private function fncImprimirPrescripciones(e:Event):void
	{
		Alert.show("¿Realmente desea Imprimir las Prescripciones?", "Confirmar", Alert.OK | Alert.CANCEL, this, fncConfirmImprimirPrescripciones, null, Alert.OK);
	}
	
	private function fncConfirmImprimirPrescripciones(e:CloseEvent):void
	{		
		if (e.detail==Alert.OK){
			this.parentDocument.ConfirmImprimirPrescripciones = true;		
			Alert.show("La impresión de las prescripciones ha sido confirmada, y se realizará al Guardar la Consulta"); 
		}
	}
	
	private function fncAgregarPrescripcion(e:Event):void
	{
		_twMedicamentos = new medicamento;
		_twMedicamentos.addEventListener("EventConfirmarMedicamento",fncGrabarMedicamento);
		PopUpManager.addPopUp(_twMedicamentos,this,true);
		PopUpManager.centerPopUp(_twMedicamentos);
	}
	
	private function fncGrabarMedicamento(e:Event):void
	{
		var xmlP : XML = _twMedicamentos.xmlMedicamento;
		_xmlPrescripciones.appendChild(xmlP);
		PopUpManager.removePopUp(e.target as medicamento);	
	}
	
	public function fncEditarPrescripcion():void
	{
		_twMedicamentos = new medicamento;
		_twMedicamentos.xmlMedicamento =  (gridPrescripciones.selectedItem as XML).copy();
		PopUpManager.addPopUp(_twMedicamentos,this,true);
		PopUpManager.centerPopUp(_twMedicamentos);
		_twMedicamentos.addEventListener("EventConfirmarMedicamento",fncEditarMedicamento);
	}
	
	private function fncEditarMedicamento(e:Event):void
	{
		var xmlP : XML = _twMedicamentos.xmlMedicamento;		
		_xmlPrescripciones.prescripcion[(gridPrescripciones.selectedItem as XML).childIndex()] = _twMedicamentos.xmlMedicamento;
		PopUpManager.removePopUp(e.target as medicamento);	
	}
	
	public function fncEliminarPrescripcion():void{
		Alert.show("¿Realmente desea Eliminar la Prescripcion "+ gridPrescripciones.selectedItem.@descrip+"?", "Confirmar", Alert.OK | Alert.CANCEL, this, fncConfirmEliminarPrescripcion, null, Alert.OK);
	}
	
	private function fncConfirmEliminarPrescripcion(e:CloseEvent):void
	{
		var xmlP : XML = _xmlPrescripciones.prescripcion[(gridPrescripciones.selectedItem as XML).childIndex()];
		if (e.detail==Alert.OK){			
			delete _xmlPrescripciones.prescripcion[(gridPrescripciones.selectedItem as XML).childIndex()]; 
		}
	}