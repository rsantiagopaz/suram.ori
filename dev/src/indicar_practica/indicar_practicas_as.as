// ActionScript file
	import clases.HTTPServices;
	
	import flash.events.Event;
	
	import indicar_practica.practica;
	
	import mx.controls.Alert;
	import mx.events.CloseEvent;
	import mx.managers.PopUpManager;
	import mx.rpc.events.ResultEvent;
	
	include "../control_acceso.as";
	
	[Bindable] private var _xmlPracticas:XML = <practicas></practicas>;
	[Bindable] private var _xmlResultado:XML;
	private var _xmlDatosPaciente:XML = <datospaciente></datospaciente>;
	private var _twPractica:practica;
	private var _idIngreso:String;
	private var _idIndex:int;
	private var httpPracticas:HTTPServices = new HTTPServices;
	
	public function get xmlResultado():XML { return _xmlResultado.copy(); }
	
	public function get idIndex():int { return _idIndex; }
	
	public function fncInit():void
	{
		_xmlPracticas = <practicas></practicas>;
		_xmlDatosPaciente = <datospaciente></datospaciente>;
		_idIngreso = this.parentDocument.idIngreso;
		_xmlDatosPaciente.appendChild(this.parentDocument.xmlDatosPaciente.paciente);
		txiApenom.text = _xmlDatosPaciente.paciente.@apeynom;
		_xmlPracticas.appendChild(this.parentDocument.xmlDatosPaciente.practica);
		lblEstado.text = '';
		btnNuevaPractica.addEventListener("click" ,fncAgregarPractica);		
		btnConfirmar.addEventListener("click" ,fncConfirmar);
		btnCancelar.addEventListener("click" ,fncCancelar);
		btnImprimirResumenHC.addEventListener("click",fncImprimir);
	}
	
	public function fncGuardarPracticaResultado(idx:int,res:String):void
	{		
		_xmlPracticas.practica[idx].@resultados = res;				
	}
	
	private function fncImprimir(e:Event):void
	{			
		//Creo los contenedores para enviar datos y recibir respuesta
		var url:String = "indicar_practica/view_resumen_hc.php";
		var enviar:URLRequest = new URLRequest(url);
		var recibir:URLLoader = new URLLoader();
	 		 
		//Creo la variable que va a ir dentro de enviar, con los campos que tiene que recibir el PHP.
		var variables:URLVariables = new URLVariables();
		
		variables.id_ingreso = _idIngreso;					
					
		//Indico que voy a enviar variables dentro de la petición
		enviar.data = variables;
		
		navigateToURL(enviar);
	}
	
	public function get xmlPracticas():XML{return _xmlPracticas.copy();}
		
	private function fncCancelar(e:Event):void{
		this.parentDocument.ConfirmIndicarPracticas = false;
		lblEstado.text = 'Pestaña Cancelada';
	}
	
	private function fncConfirmar(e:Event):void{		
		this.parentDocument.ConfirmIndicarPracticas = true;
		lblEstado.text = 'Pestaña Confirmada';		
	}
	
	private function fncAgregarPractica(e:Event):void
	{
		_twPractica = new practica;
		_twPractica.addEventListener("EventConfirmarPractica",fncGrabarPractica);
		PopUpManager.addPopUp(_twPractica,this,true);
		PopUpManager.centerPopUp(_twPractica);
	}
	
	private function fncGrabarPractica(e:Event):void
	{
		var xmlPractica : XML = _twPractica.xmlPractica;
		xmlPractica.@id_ingreso = _idIngreso;			
		_xmlPracticas.appendChild(xmlPractica);
		_xmlResultado = _twPractica.xmlResultado;					
		PopUpManager.removePopUp(e.target as practica);		
		dispatchEvent(new Event("eveAgregarPracticaResultado"));		
	}
	
	private function fncCargarID(e:Event):void
	{
		var xmlPractica : XML = _twPractica.xmlPractica;
		xmlPractica.@id_solicitudes = httpPracticas.lastResult.insert_id; 
		_xmlPracticas.appendChild(xmlPractica);
		httpPracticas.removeEventListener(ResultEvent.RESULT,fncCargarID);
	}
	
	public function fncEditarPractica():void
	{
		_twPractica = new practica;
		_twPractica.xmlPractica =  (gridPracticas.selectedItem as XML).copy();
		_twPractica.addEventListener("EventConfirmarPractica",fncGuardarEditarPractica);
		PopUpManager.addPopUp(_twPractica,this,true);
		PopUpManager.centerPopUp(_twPractica);
	}
	
	private function fncGuardarEditarPractica(e:Event):void
	{
		var xmlPractica : XML = _twPractica.xmlPractica;		
		
		_xmlPracticas.practica[(gridPracticas.selectedItem as XML).childIndex()] = _twPractica.xmlPractica;
		PopUpManager.removePopUp(e.target as practica);
	}
	
	public function fncEliminarPractica():void
	{
		Alert.show("¿Realmente desea Eliminar la Practica "+ gridPracticas.selectedItem.@descripcion+"?", "Confirmar", Alert.OK | Alert.CANCEL, this, fncConfirmEliminarPractica, null, Alert.OK);
	}
	
	private function fncConfirmEliminarPractica(e:CloseEvent):void
	{
		var xmlPractica : XML = _xmlPracticas.practica[(gridPracticas.selectedItem as XML).childIndex()];
		if (e.detail==Alert.OK){
			_idIndex = (gridPracticas.selectedItem as XML).childIndex();
			delete _xmlPracticas.practica[(gridPracticas.selectedItem as XML).childIndex()];			
			dispatchEvent(new Event("eveEliminarPracticaResultado"));
		}
	}
	