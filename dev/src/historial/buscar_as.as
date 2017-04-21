// ActionScript file
	import clases.HTTPServices;
	
	import flash.events.Event;
	
	import mx.controls.Alert;
	import mx.core.UIComponent;
	import mx.events.ValidationResultEvent;
	import mx.managers.PopUpManager;
	import mx.rpc.events.ResultEvent;
	import mx.validators.Validator;
	
	include "../control_acceso.as";

	private var _tipo_doc : String;
	private var _nro_doc: String;
	[Bindable] public var idPersona : String;
	[Bindable] private var httpDatos : HTTPServices = new HTTPServices;
	
	public function fncInit():void
	{	
		//preparo el PopUp Para que se cierre con esc		
		this.addEventListener(KeyboardEvent.KEY_UP,function(e:KeyboardEvent):void{if (e.keyCode==27) btnCancelar.dispatchEvent(new MouseEvent(MouseEvent.CLICK))});
		idPersona = '';			
		httpDatos.url = "historial/buscar.php";
		httpDatos.addEventListener("acceso",acceso);
		httpDatos.addEventListener(ResultEvent.RESULT,fncCargarDatos);
		btnAceptar.addEventListener("click",fncIniciarBusqueda);	
		btnCancelar.addEventListener("click",fncCerrar);	
	}		
		
	
	private function fncCerrar(e:Event):void{
		PopUpManager.removePopUp(this)
	}
	
	private function fncValidar():Boolean
	{
		var error:Array = Validator.validateAll([validNDOC]);
		if (error.length>0) {
			((error[0] as ValidationResultEvent).target.source as UIComponent).setFocus();
			return false;
		}else{
			return true;	
		}	
	}
	
	private function fncIniciarBusqueda(e:Event):void
	{
		if (fncValidar()){
			var idxdoc:int = cbxTipoDoc.selectedIndex;			
			_tipo_doc = xmlTiposDoc.tipodoc.id[idxdoc];
			_nro_doc = txiNroDoc.text;
			httpDatos.send({rutina:"buscar_persona", tipo_doc:_tipo_doc, nro_doc:_nro_doc});
		}	
	}
	
	private function fncCargarDatos(e:ResultEvent):void
	{
		idPersona = httpDatos.lastResult.persona.@persona_id;
		if (idPersona != '0' && idPersona){
			dispatchEvent(new Event("verHistorial"));
		}else{
			Alert.show("Los datos ingresados no corresponden a ninguna persona registrada en el sistema","ERROR");		
		}
	}