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
	
	[Bindable] private var _xmlPractica : XML = <practica />;
	[Bindable] private var httpPractica : HTTPServices = new HTTPServices;
	private var _accion : String;	
	
	
	public function get xmlPractica():XML{return _xmlPractica.copy();}
	public function set xmlPractica(esp:XML):void{
		_xmlPractica = esp;
		_accion = "editar";
	}
	public function set xmlPractica2(esp:XML):void{
		_xmlPractica = esp;
		_accion = "eliminar";
	}
	
	private function fncInit():void
	{
		//preparo el PopUp Para que se cierre con esc y marco el default button
		this.defaultButton = btnGrabar;
		this.addEventListener(KeyboardEvent.KEY_UP,function(e:KeyboardEvent):void{if (e.keyCode==27) btnCancel.dispatchEvent(new MouseEvent(MouseEvent.CLICK))});		
		//preparo el httpservice
		httpPractica.url = "consultar_practicas/practicas.php";
		httpPractica.addEventListener("acceso",acceso);
		// escucho evento de los botones
		btnCancel.addEventListener("click",fncCerrar);
		if (_accion == "editar") {
			//txtCodigo.text = _xmlPractica.@codigo;
			txtProcedimiento.text = _xmlPractica.@descripcion;			
			btnGrabar.addEventListener("click",fncEdit);
		} else if (_accion == "eliminar") {
			this.currentState = "eliminar";
			//txtCodigo.text = _xmlPractica.@codigo;
			txtProcedimiento.text = _xmlPractica.@descripcion;			
			btnEliminar.addEventListener("click",fncDelete);
		} else
			btnGrabar.addEventListener("click",fncAdd);
		//pocisino el cursor
		txtProcedimiento.setFocus();
	}
	
	private function fncDelete(e:Event):void
	{
		Alert.show("¿Realmente desea Eliminar la Practica "+ _xmlPractica.@descripcion+"?", "Confirmar", Alert.OK | Alert.CANCEL, this, fncConfirmEliminarPractica, null, Alert.OK);		
	}
	
	private function fncConfirmEliminarPractica(e:CloseEvent):void
	{		
		if (e.detail==Alert.OK){
			httpPractica.addEventListener(ResultEvent.RESULT,fncResultDelete);
			httpPractica.send({rutina:"delete", xmlPractica:_xmlPractica.toXMLString()}); 
		}
	}
	
	private function fncResultDelete(e:Event):void{		
		Alert.show("La eliminación se registro con exito","Practica");
		dispatchEvent(new Event("EventDelete"));			
		httpPractica.removeEventListener(ResultEvent.RESULT,fncResultDelete);
	}
	
	private function fncArmarxmlPractica():void
	{
		_xmlPractica.@id_practica="";
		_xmlPractica.@descripcion=txtProcedimiento.text;						
	}
	
	private function fncCerrar(e:Event):void
	{
		PopUpManager.removePopUp(this)	
	}
	
	private function fncAdd(e:Event):void
	{
		if (fncValidar()) {
			fncArmarxmlPractica();
			httpPractica.addEventListener(ResultEvent.RESULT,fncResultAdd);
			httpPractica.send({rutina:"insert", xmlPractica:_xmlPractica.toXMLString()});
		}
	}
	
	private function fncEdit(e:Event):void
	{
		if (fncValidar()) {
			_xmlPractica.@descripcion=txtProcedimiento.text;	
			httpPractica.addEventListener(ResultEvent.RESULT,fncResultEdit);
			httpPractica.send({rutina:"update", xmlPractica:_xmlPractica.toXMLString()});
		}
	}
	
	private function fncResultAdd(e:Event):void{
		_xmlPractica.@id_practica = httpPractica.lastResult.insert_id;		
		
		Alert.show("El alta se registró con exito","Practica");
		dispatchEvent(new Event("EventAlta"));
		
		httpPractica.removeEventListener(ResultEvent.RESULT,fncResultAdd);
	}
	
	private function fncResultEdit(e:Event):void{				
		Alert.show("La modificación se registró con exito","Practica");
		dispatchEvent(new Event("EventEdit"));			
		httpPractica.removeEventListener(ResultEvent.RESULT,fncResultEdit);
	}
	
	private function fncValidar():Boolean
	{
		var error:Array = Validator.validateAll([validProcedimiento]);
		if (error.length>0) {
			((error[0] as ValidationResultEvent).target.source as UIComponent).setFocus();
			return false;
		}else{
			return true;	
		}	
	}