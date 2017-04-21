// ActionScript file
	import clases.HTTPServices;
	
	import flash.events.Event;
	
	import mx.controls.Alert;
	import mx.events.CloseEvent;
	import mx.core.UIComponent;
	import mx.events.ValidationResultEvent;
	import mx.managers.PopUpManager;
	import mx.rpc.events.ResultEvent;
	import mx.validators.Validator;

	include "../control_acceso.as";
	
	[Bindable] private var _xmlMedicamento : XML = <vademecum />;
	[Bindable] private var httpMedicamento : HTTPServices = new HTTPServices;
	private var _accion : String;	
	
	
	public function get xmlMedicamento():XML{return _xmlMedicamento.copy();}
	public function set xmlMedicamento(esp:XML):void{
		_xmlMedicamento = esp;
		_accion = "editar";
	}
	public function set xmlMedicamento2(esp:XML):void{
		_xmlMedicamento = esp;
		_accion = "eliminar";
	}
	
	private function fncInit():void
	{
		//preparo el PopUp Para que se cierre con esc y marco el default button
		this.defaultButton = btnGrabar;
		this.addEventListener(KeyboardEvent.KEY_UP,function(e:KeyboardEvent):void{if (e.keyCode==27) btnCancel.dispatchEvent(new MouseEvent(MouseEvent.CLICK))});		
		//preparo el httpservice
		httpMedicamento.url = "consultar_vademecum/vademecum.php";
		httpMedicamento.addEventListener("acceso",acceso);
		// escucho evento de los botones
		btnCancel.addEventListener("click",fncCerrar);
		if (_accion == "editar") {
			//txtCodigo.text = _xmlMedicamento.@codigo;
			txtMonodroga.text = _xmlMedicamento.@monodroga;
			txtConcentracion.text = _xmlMedicamento.@concentracion;
			txtPresentacion.text = _xmlMedicamento.@presentacion;
			btnGrabar.addEventListener("click",fncEdit);
		} else if (_accion == "eliminar") {
			this.currentState = "eliminar";
			//txtCodigo.text = _xmlMedicamento.@codigo;
			txtMonodroga.text = _xmlMedicamento.@monodroga;
			txtConcentracion.text = _xmlMedicamento.@concentracion;
			txtPresentacion.text = _xmlMedicamento.@presentacion;
			btnEliminar.addEventListener("click",fncDelete);
		} else
			btnGrabar.addEventListener("click",fncAdd);
		//pocisino el cursor
		txtMonodroga.setFocus();
	}
	
	private function fncDelete(e:Event):void
	{
		Alert.show("¿Realmente desea Eliminar la Medicamento "+ _xmlMedicamento.@monodroga+"?", "Confirmar", Alert.OK | Alert.CANCEL, this, fncConfirmEliminarMedicamento, null, Alert.OK);		
	}
	
	private function fncConfirmEliminarMedicamento(e:CloseEvent):void
	{		
		if (e.detail==Alert.OK){
			httpMedicamento.addEventListener(ResultEvent.RESULT,fncResultDelete);
			httpMedicamento.send({rutina:"delete", xmlMedicamento:_xmlMedicamento.toXMLString()}); 
		}
	}
	
	private function fncResultDelete(e:Event):void{		
		Alert.show("La eliminación se registro con exito","Medicamento");
		dispatchEvent(new Event("EventDelete"));			
		httpMedicamento.removeEventListener(ResultEvent.RESULT,fncResultDelete);
	}
	
	private function fncArmarxmlMedicamento():void
	{
		_xmlMedicamento.@id_vademecum="";
		_xmlMedicamento.@monodroga=txtMonodroga.text;
		_xmlMedicamento.@presentacion=txtPresentacion.text;
		_xmlMedicamento.@concentracion=txtConcentracion.text;				
	}
	
	private function fncCerrar(e:Event):void
	{
		PopUpManager.removePopUp(this)	
	}
	
	private function fncAdd(e:Event):void
	{
		if (fncValidar()) {
			fncArmarxmlMedicamento();
			httpMedicamento.addEventListener(ResultEvent.RESULT,fncResultAdd);
			httpMedicamento.send({rutina:"insert", xmlMedicamento:_xmlMedicamento.toXMLString()});
		}
	}
	
	private function fncEdit(e:Event):void
	{
		if (fncValidar()) {
			_xmlMedicamento.@monodroga=txtMonodroga.text;		
			httpMedicamento.addEventListener(ResultEvent.RESULT,fncResultEdit);
			httpMedicamento.send({rutina:"update", xmlMedicamento:_xmlMedicamento.toXMLString()});
		}
	}
	
	private function fncResultAdd(e:Event):void{
		_xmlMedicamento.@id_vademecum = httpMedicamento.lastResult.insert_id;		
		
		Alert.show("El alta se registró con exito","Medicamento");
		dispatchEvent(new Event("EventAlta"));
		
		httpMedicamento.removeEventListener(ResultEvent.RESULT,fncResultAdd);
	}
	
	private function fncResultEdit(e:Event):void{				
		Alert.show("La modificación se registró con exito","Medicamento");
		dispatchEvent(new Event("EventEdit"));			
		httpMedicamento.removeEventListener(ResultEvent.RESULT,fncResultEdit);
	}
	
	private function fncValidar():Boolean
	{
		var error:Array = Validator.validateAll([validMonodroga,validConcentracion,validPresentacion]);
		if (error.length>0) {
			((error[0] as ValidationResultEvent).target.source as UIComponent).setFocus();
			return false;
		}else{
			return true;	
		}	
	}