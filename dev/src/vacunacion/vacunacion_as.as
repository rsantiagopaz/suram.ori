// ActionScript file
	import clases.HTTPServices;
	
	import flash.events.Event;
	
	import mx.controls.Alert;
	import mx.events.CloseEvent;
	import mx.managers.PopUpManager;
	import mx.rpc.events.ResultEvent;
	
	import vacunacion.nueva_vacunacion;
	
	include "../control_acceso.as";
	
	[Bindable] private var _xmlVacunaciones:XML = <vacunaciones></vacunaciones>;
	private var _xmlDatosPaciente:XML = <datospaciente></datospaciente>;
	private var _twNuevasVacunaciones:nueva_vacunacion;
	private var _idIngreso:String;
	private var httpVacunas:HTTPServices = new HTTPServices;
		
	public function fncInit():void
	{
		_xmlVacunaciones = <vacunaciones></vacunaciones>;
		_xmlDatosPaciente = <datospaciente></datospaciente>;
		_idIngreso = this.parentDocument.idIngreso;		
		_xmlVacunaciones.appendChild(this.parentDocument.xmlDatosPaciente.vacunaciones);
		_xmlDatosPaciente.appendChild(this.parentDocument.xmlDatosPaciente.paciente);
		txiApenom.text = _xmlDatosPaciente.paciente.@apeynom;
		lblEstado.text = '';
		btnNuevaVacunacion.addEventListener("click",fncAgregarVacunacion);		
		btnCancelar.addEventListener("click",fncCancelar);
		btnConfirmar.addEventListener("click",fncConfirmar);	
	}
	
	public function get xmlVacunaciones():XML{return _xmlVacunaciones.copy();}
	
	private function fncCancelar(e:Event):void{
		this.parentDocument.ConfirmVacunaciones = false;
		lblEstado.text = 'Pestaña Cancelada';
	}
	
	private function fncConfirmar(e:Event):void{		
		this.parentDocument.ConfirmVacunaciones = true;
		lblEstado.text = 'Pestaña Confirmada';		
	}
		
	private function fncAgregarVacunacion(e:Event):void
	{
		_twNuevasVacunaciones = new nueva_vacunacion;
		_twNuevasVacunaciones.addEventListener("EventConfirmarVacunacion",fncGrabarNuevaVacunacion);
		PopUpManager.addPopUp(_twNuevasVacunaciones,this,true);
		PopUpManager.centerPopUp(_twNuevasVacunaciones);
	}
	
	private function fncGrabarNuevaVacunacion(e:Event):void
	{			
		var xmlP : XML = _twNuevasVacunaciones.xmlVacuna;
		xmlP.@id_ingreso = _idIngreso;		
		_xmlVacunaciones.appendChild(xmlP);
		PopUpManager.removePopUp(e.target as nueva_vacunacion);	
	}
	
	private function fncCargarID(e:Event):void
	{
		var xmlP : XML = _twNuevasVacunaciones.xmlVacuna;
		xmlP.@id_vacunacion = httpVacunas.lastResult.insert_id;		
		_xmlVacunaciones.appendChild(xmlP);		
	}
	
	private function fncEditarNuevaVacunacion(e:Event):void
	{
		var xmlP : XML = _twNuevasVacunaciones.xmlVacuna;
		xmlP.@id_ingreso = _idIngreso;		
		_xmlVacunaciones.vacunaciones[(gridVacunaciones.selectedItem as XML).childIndex()] = _twNuevasVacunaciones.xmlVacuna;
		PopUpManager.removePopUp(e.target as nueva_vacunacion);	
	}
	
	public function fncEditarVacunacion():void
	{
		_twNuevasVacunaciones = new nueva_vacunacion;
		_twNuevasVacunaciones.xmlVacuna =  (gridVacunaciones.selectedItem as XML).copy();
		_twNuevasVacunaciones.addEventListener("EventConfirmarVacunacion",fncEditarNuevaVacunacion);
		PopUpManager.addPopUp(_twNuevasVacunaciones,this,true);
		PopUpManager.centerPopUp(_twNuevasVacunaciones);
	}
	
	public function fncEliminarVacunacion():void
	{
		Alert.show("¿Realmente desea Eliminar la Vacuna "+ gridVacunaciones.selectedItem.@nombre+"?", "Confirmar", Alert.OK | Alert.CANCEL, this, fncConfirmEliminarVacunacion, null, Alert.OK);
	}
	
	private function fncConfirmEliminarVacunacion(e:CloseEvent):void
	{		
		var xmlP : XML = _xmlVacunaciones.vacunaciones[(gridVacunaciones.selectedItem as XML).childIndex()];
		if (e.detail==Alert.OK){			
			delete _xmlVacunaciones.vacunaciones[(gridVacunaciones.selectedItem as XML).childIndex()]; 
		}
	}