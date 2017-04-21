// ActionScript file
	import clases.HTTPServices;
	
	import consulta.consulta;
	
	import flash.events.Event;
	import flash.net.URLRequest;
	import flash.net.navigateToURL;
	
	import mx.controls.Alert;
	import mx.managers.PopUpManager;
	import mx.rpc.events.ResultEvent;
	
	include "../control_acceso.as";
	
	private var _twConsulta:consulta;
	[Bindable] private var httpDatos : HTTPServices = new HTTPServices;	
	[Bindable] private var _xmlIngresos:XML = <ingresos></ingresos>;
	[Bindable] public var _xmlIngresos2:XMLList;	
	private var _idIngreso:String;
	private var _idPersona : String;
		
	public function set idPersona(idPersona:String):void
	{
		_idPersona = idPersona;
	}	
	
	public function fncInit():void
	{
		//preparo el PopUp Para que se cierre con esc		
		this.addEventListener(KeyboardEvent.KEY_UP,function(e:KeyboardEvent):void{if (e.keyCode==27) btnClose.dispatchEvent(new MouseEvent(MouseEvent.CLICK))});
		
		httpDatos.url = "historial/historial.php";
		httpDatos.addEventListener("acceso",acceso);
		httpDatos.addEventListener(ResultEvent.RESULT,fncCargarDatos);
		httpDatos.send({rutina:"traer_historial", id_persona:_idPersona});
		
		// escucho evento de los botones		
		btnClose.addEventListener("click",fncCerrarPOP);		
	}
	
	private function fncCargarDatos(e:ResultEvent):void
	{
		//_xmlIngresos.appendChild(httpDatos.lastResult.ingresos);		
		_xmlIngresos2 = httpDatos.lastResult.ingresos2.item_ingreso as XMLList;
		dtgHistorial.expandAll();
	}
	
	public function fncVerDetalle(data:Object):void
	{	
		var histoURL:URLRequest = new URLRequest("historial/view_historial.php");
		var vars:URLVariables = new URLVariables();
		vars.id_ingreso_movimiento = data.@id_ingreso_movimiento;			
		histoURL.data = vars;
		navigateToURL(histoURL);
		/*var urlHistorial : String = "historial/view_historial.php?id_ingreso_movimiento="+data.@id_ingreso_movimiento;
		var jscommand:String = "window.open(" + urlHistorial + ",'win','height=200,width=300,toolbar=no,scrollbars=yes');"; 
		var urlPopUp:URLRequest = new URLRequest("javascript:" + jscommand + ";");
		navigateToURL(urlPopUp, "_self"); */
	}
	
	private function fncCerrarDetalle(e:Event):void
	{
		PopUpManager.removePopUp(e.target as consulta);
	}
	
	private function fncCerrarPOP(e:Event):void
	{
		if (parentApplication.DesdeConsulta){
			dispatchEvent(new Event("SelectConsulta"));
		}else{
			dispatchEvent(new Event("SelectPrincipal"));
		}
	}