// ActionScript file
	import flash.events.Event;
	
	import mx.controls.Alert;
	import mx.events.CloseEvent;
	import mx.managers.PopUpManager;
			
	[Bindable] private var _xmlPracticas:XML = <resultados></resultados>;
	private var _xmlDatosPaciente:XML = <datospaciente></datospaciente>;
	private var _twResultado:resultado;
	private var _idIngreso:String;
	private var _idIndex:int;
	private var _resultado:String
	
	public function fncInit():void
	{
		_xmlPracticas = <resultados></resultados>;
		_xmlDatosPaciente = <datospaciente></datospaciente>;
		_idIngreso = this.parentDocument.idIngreso;
		_xmlDatosPaciente.appendChild(this.parentDocument.xmlDatosPaciente.paciente);
		txiApenom.text = _xmlDatosPaciente.paciente.@apeynom;
		_xmlPracticas.appendChild(this.parentDocument.xmlDatosPaciente.resultado);		
		lblEstado.text = '';
		btnConfirmar.addEventListener("click" ,fncConfirmar);
		btnCancelar.addEventListener("click" ,fncCancelar);
	}
	
	public function get xmlResultados():XML { return _xmlPracticas.copy(); }
	
	public function get idIndex():int { return _idIndex; }
	
	public function get result():String { return _resultado; }
	
	private function fncCancelar(e:Event):void
	{
		this.parentDocument.ConfirmCargaResultados = false;
		lblEstado.text = 'Pestaña Cancelada';
	}
	
	private function fncConfirmar(e:Event):void
	{		
		this.parentDocument.ConfirmCargaResultados = true;
		lblEstado.text = 'Pestaña Confirmada';		
	}
	
	private function fncGuardarEditarResultado(e:Event):void
	{
		var xmlPrac:XML = _twResultado.xmlPractica;
		_idIndex = (gridPracticas.selectedItem as XML).childIndex();
		_resultado = xmlPrac.@resultados;
		_xmlPracticas.resultado[(gridPracticas.selectedItem as XML).childIndex()] = _twResultado.xmlPractica;				
		PopUpManager.removePopUp(e.target as resultado);
		dispatchEvent(new Event("eveGuardarPracticaResultado"));
	}
	
	public function fncEditarResultado():void
	{
		_twResultado = new resultado;
		_twResultado.xmlPractica =  (gridPracticas.selectedItem as XML).copy();
		_twResultado.addEventListener("EventConfirmarResultado",fncGuardarEditarResultado);
		PopUpManager.addPopUp(_twResultado,this,true);
		PopUpManager.centerPopUp(_twResultado);
	}
	
	public function fncAgregarPracticaResultado(xmlRes:XML):void
	{			
		_xmlPracticas.appendChild(xmlRes);
	}
	
	public function fncEliminarPracticaResultado(idx:int):void
	{
		delete _xmlPracticas.resultado[idx];
	}	
	
	public function fncEliminarResultado():void
	{		
		Alert.show("¿Realmente desea Eliminar el Resultado de la Practica "+ gridPracticas.selectedItem.@descripcion+"?", "Confirmar", Alert.OK | Alert.CANCEL, this, fncConfirmEliminarResultado, null, Alert.OK);
	}
	
	private function fncConfirmEliminarResultado(e:CloseEvent):void
	{
		if (e.detail==Alert.OK){
			var _xmlPractica:XML = (gridPracticas.selectedItem as XML).copy();
			_xmlPractica.@estado = 'S';
			_xmlPractica.@resultados = '';
			_xmlPracticas.resultado[(gridPracticas.selectedItem as XML).childIndex()] = _xmlPractica;
			_idIndex = (gridPracticas.selectedItem as XML).childIndex();
			_resultado = _xmlPractica.@resultados;
			dispatchEvent(new Event("eveGuardarPracticaResultado"));	
		}
	}
	