// ActionScript file
	import antecedentes.nuevo_antecedente;
	
	import clases.HTTPServices;
	
	import flash.events.Event;
	
	import mx.controls.Alert;
	import mx.events.CloseEvent;
	import mx.managers.PopUpManager;
	import mx.rpc.events.ResultEvent;
	
	include "../control_acceso.as";	
	
	[Bindable] private var _xmlAntecedentes:XML = <antecedentes></antecedentes>;
	private var _xmlDatosPaciente:XML = <datospaciente></datospaciente>;
	private var _twNuevosAntecedentes:nuevo_antecedente;
	private var _idIngreso:String;
	private var httpAntecedentes:HTTPServices = new HTTPServices;
		
	public function fncInit():void
	{
		_xmlAntecedentes = <antecedentes></antecedentes>;
		_xmlDatosPaciente = <datospaciente></datospaciente>;
		_idIngreso = this.parentDocument.idIngreso;
		_xmlAntecedentes.appendChild(this.parentDocument.xmlDatosPaciente.antecedente);
		_xmlDatosPaciente.appendChild(this.parentDocument.xmlDatosPaciente.paciente);
		txiApenom.text = _xmlDatosPaciente.paciente.@apeynom;
		httpAntecedentes.url = "antecedentes/antecedentes.php";
		httpAntecedentes.addEventListener("acceso",acceso);
		btnNuevoAntecedente.addEventListener("click" ,fncAgregarAntecedente);
		lblEstado.text = '';
		btnCancelar.addEventListener("click" ,fncCancelar);
		btnConfirmar.addEventListener("click" ,fncConfirmar);
	}
	
	public function get xmlAntecedentes():XML{return _xmlAntecedentes.copy();}
		
	private function fncCancelar(e:Event):void{
		this.parentDocument.ConfirmAntecedentes = false;
		lblEstado.text = 'Pestaña Cancelada';
	}
	
	private function fncConfirmar(e:Event):void{
		//if(_xmlAntecedentes.hasOwnProperty('antecedente')){
			this.parentDocument.ConfirmAntecedentes = true;
			lblEstado.text = 'Pestaña Confirmada';
		//}		
	}
	
	private function fncAgregarAntecedente(e:Event):void
	{
		_twNuevosAntecedentes = new nuevo_antecedente;
		_twNuevosAntecedentes.addEventListener("EventConfirmarAntecedente",fncGrabarNuevoAntecedente);
		PopUpManager.addPopUp(_twNuevosAntecedentes,this,true);
		PopUpManager.centerPopUp(_twNuevosAntecedentes);
	}
	
	private function fncGrabarNuevoAntecedente(e:Event):void
	{
		var xmlP : XML = _twNuevosAntecedentes.xmlNuevoAntecedente;
		xmlP.@id_ingreso_movimiento = _idIngreso;		
		_xmlAntecedentes.appendChild(xmlP);
		
		PopUpManager.removePopUp(e.target as nuevo_antecedente);	
	}
	
	private function fncCargarID(e:Event):void
	{
		var xmlP : XML = _twNuevosAntecedentes.xmlNuevoAntecedente;
		xmlP.@id_antecedente = httpAntecedentes.lastResult.insert_id;		
		_xmlAntecedentes.appendChild(xmlP);		
		httpAntecedentes.removeEventListener(ResultEvent.RESULT,fncCargarID);
	}
	
	private function fncEditarNuevoAntecedente(e:Event):void
	{		
		_xmlAntecedentes.appendChild(_twNuevosAntecedentes.xmlNuevoAntecedente);		
		PopUpManager.removePopUp(e.target as nuevo_antecedente);	
	}
	
	public function fncEditarAntecedente():void
	{
		_twNuevosAntecedentes = new nuevo_antecedente;
		_twNuevosAntecedentes.xmlNuevoAntecedente =  (gridAntecedentes.selectedItem as XML).copy();
		_twNuevosAntecedentes.addEventListener("EventConfirmarAntecedente",fncGrabarNuevoAntecedente);
		PopUpManager.addPopUp(_twNuevosAntecedentes,this,true);
		PopUpManager.centerPopUp(_twNuevosAntecedentes);
	}
	
	public function fncEliminarAntecedente2():void
	{
		_twNuevosAntecedentes = new nuevo_antecedente;
		_twNuevosAntecedentes.xmlNuevoAntecedente2 =  (gridAntecedentes.selectedItem as XML).copy();
		_twNuevosAntecedentes.addEventListener("EventConfirmarAntecedente",fncGrabarNuevoAntecedente);
		PopUpManager.addPopUp(_twNuevosAntecedentes,this,true);
		PopUpManager.centerPopUp(_twNuevosAntecedentes);
	}
	
	public function fncEliminarAntecedente():void
	{
		Alert.show("¿Realmente desea Eliminar el Antecedente "+ gridAntecedentes.selectedItem.@antecedente+"?", "Confirmar", Alert.OK | Alert.CANCEL, this, fncConfirmEliminarAntecedente, null, Alert.OK);
	}
	
	private function fncConfirmEliminarAntecedente(e:CloseEvent):void
	{
		if (e.detail==Alert.OK){			 
			fncEliminarAntecedente2();
		}
	}