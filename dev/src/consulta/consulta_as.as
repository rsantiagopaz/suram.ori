// ActionScript file
	import clases.HTTPServices;
	
	import flash.events.Event;
	
	import mx.controls.Alert;
	import mx.rpc.events.ResultEvent;
	
	import page_especialidad.oftalmologia.page_oftalmologia;
	
	include "../control_acceso.as";

	private var _idIngreso : String;
	[Bindable] private var _xmlDatosPaciente : XML;	
	[Bindable] private var httpDatos : HTTPServices = new HTTPServices;
	[Bindable] private var httpGuardar : HTTPServices = new HTTPServices;
	private var _ConfirmPrescripciones : Boolean = false;
	private var _ConfirmAntecedentes : Boolean = false;
	private var _ConfirmVacunaciones : Boolean = false;
	private var _ConfirmDatosConsulta : Boolean = false;
	private var _ConfirmDerivaciones : Boolean = false;
	private var _ConfirmCargaResultados : Boolean = false;
	private var _ConfirmIndicarPracticas : Boolean = false;
	private var _ConfirmImprimirPrescripciones : Boolean = false;
	
	private var _ConfirmEspecialidad : Boolean = false;
	
	public var id_especialidad : String;
	public var page_oftalmologia_1 : page_oftalmologia;
	
	public function fncInit(ingreso:String):void
	{
		consultaTabs.selectedIndex=0;
		consultaTabs.addEventListener("change",UpdateFinalizarConsulta);
		_ConfirmPrescripciones = false;
		_ConfirmAntecedentes = false;
		_ConfirmVacunaciones = false;
		_ConfirmDatosConsulta = false;
		_ConfirmDerivaciones = false;
		_ConfirmCargaResultados = false;
		_ConfirmIndicarPracticas  = false;
		_ConfirmImprimirPrescripciones = false;
		
		_ConfirmEspecialidad = false;
		
		_idIngreso = ingreso;
		_xmlDatosPaciente = new XML;
		
		httpDatos.url = "consulta/consulta.php";
		httpDatos.addEventListener("acceso",acceso);
		httpDatos.addEventListener(ResultEvent.RESULT,fncCargarDatos);
		//httpDatos.send({rutina:"traer_datos", id_ingreso:_idIngreso});
		
		httpGuardar.url = "consulta/consulta.php";
		httpGuardar.addEventListener("acceso",acceso);
		httpGuardar.addEventListener(ResultEvent.RESULT,fncResultAlta);
		
		
		

		var httpAux : HTTPServices = new HTTPServices;
		httpAux.url = "consulta/consulta.php";
		httpAux.resultFormat = "text";
		httpAux.addEventListener(ResultEvent.RESULT, function():void{
			var lastResult : String = httpAux.lastResult as String;
			
			if (id_especialidad != null && id_especialidad != lastResult) {
				id_especialidad = lastResult;
				consultaTabs.removeChildAt(8);
				
				if (id_especialidad == "42") {
					page_oftalmologia_1 = new page_oftalmologia;
					consultaTabs.addChildAt(page_oftalmologia_1, 8);
				}
			}
			
			httpDatos.send({rutina:"traer_datos", id_ingreso:_idIngreso});			
		});
		httpAux.send({rutina:"leer_especialidad", id_servicio: parentApplication.controlAcceso.usuario_servicio_id});
	}
	
	private function Module_creationComplete() : void {
		var httpAux : HTTPServices = new HTTPServices;
		httpAux.url = "consulta/consulta.php";
		httpAux.resultFormat = "text";
		httpAux.addEventListener(ResultEvent.RESULT, function() : void {
			id_especialidad = httpAux.lastResult as String;
			
			if (id_especialidad == "42") {
				page_oftalmologia_1 = new page_oftalmologia;
				consultaTabs.addChildAt(page_oftalmologia_1, 8);
			}
		});
		httpAux.send({rutina:"leer_especialidad", id_servicio: parentApplication.controlAcceso.usuario_servicio_id});
	}
	
	private function fncCerrar():void{
		dispatchEvent(new Event("SelectPrincipal"));
	}
	
	public function get idIngreso():String { return _idIngreso; }
	public function get xmlDatosPaciente():XML { return _xmlDatosPaciente }		
	
	public function get ConfirmPrescripciones():Boolean { return _ConfirmPrescripciones}
	public function set ConfirmPrescripciones(v:Boolean):void { _ConfirmPrescripciones = v;}
	
	public function get ConfirmAntecedentes():Boolean { return _ConfirmAntecedentes}
	public function set ConfirmAntecedentes(v:Boolean):void { _ConfirmAntecedentes = v;}
	
	public function get ConfirmVacunaciones():Boolean { return _ConfirmVacunaciones}
	public function set ConfirmVacunaciones(v:Boolean):void { _ConfirmVacunaciones = v;}
	
	public function get ConfirmDatosConsulta():Boolean { return _ConfirmDatosConsulta}
	public function set ConfirmDatosConsulta(v:Boolean):void { _ConfirmDatosConsulta = v;}
	
	public function get ConfirmDerivaciones():Boolean { return _ConfirmDerivaciones}
	public function set ConfirmDerivaciones(v:Boolean):void { _ConfirmDerivaciones = v;}
	
	public function get ConfirmCargaResultados():Boolean { return _ConfirmCargaResultados}
	public function set ConfirmCargaResultados(v:Boolean):void { _ConfirmCargaResultados = v;}
	
	public function get ConfirmIndicarPracticas():Boolean { return _ConfirmIndicarPracticas}
	public function set ConfirmIndicarPracticas(v:Boolean):void { _ConfirmIndicarPracticas = v;}
	
	public function get ConfirmImprimirPrescripciones():Boolean { return _ConfirmImprimirPrescripciones}
	public function set ConfirmImprimirPrescripciones(v:Boolean):void { _ConfirmImprimirPrescripciones = v;}
	
	public function get ConfirmEspecialidad():Boolean { return _ConfirmEspecialidad}
	public function set ConfirmEspecialidad(v:Boolean):void { _ConfirmEspecialidad = v;}
	
	private function fncCargarDatos(e:ResultEvent):void
	{
		_xmlDatosPaciente = new XML;
		_xmlDatosPaciente = httpDatos.lastResult as XML;		
		
		if (ModDatosPersonales){ModDatosPersonales.fncInit();}
		if (ModAntecedentes){ModAntecedentes.fncInit();}
		if (ModDatosConsulta){ModDatosConsulta.fncInit();}
		if (ModPrescripciones){ModPrescripciones.fncInit();}
		if (ModIndicarPracticas){ModIndicarPracticas.fncInit();}
		if (ModVacunacion){ModVacunacion.fncInit();}
		if (ModResultadosPracticas){ModResultadosPracticas.fncInit();}
		if (ModDerivaciones){ModDerivaciones.fncInit();}
		
		if (id_especialidad == "42") {
			page_oftalmologia_1.fncInit();
		}
	}
	
	private function fncViewHistorial():void
	{
		dispatchEvent(new Event("ViewHistorial"));
	}
	
	private function fncAgregarPracticaResultado():void
	{			
		if (ModResultadosPracticas) {
			var _xmlResultado:XML = ModIndicarPracticas.xmlResultado;
			ModResultadosPracticas.fncAgregarPracticaResultado(_xmlResultado);
		}					
	}
	
	private function fncEliminarPracticaResultado():void
	{			
		if (ModResultadosPracticas) {
			var _idIndex:int = ModIndicarPracticas.idIndex
			ModResultadosPracticas.fncEliminarPracticaResultado(_idIndex);
		}					
	}
	
	private function fncGuardarPracticaResultado():void
	{			
		if (ModIndicarPracticas) {
			var _idIndex:int = ModResultadosPracticas.idIndex;
			var _resultado:String = ModResultadosPracticas.result;
			ModIndicarPracticas.fncGuardarPracticaResultado(_idIndex,_resultado);
		}					
	}

	private function UpdateFinalizarConsulta(e:Event):void
	{
		if (panelFinalizarConsulta){
			txiApenom.text = _xmlDatosPaciente.paciente.@apeynom;
			imgAntecedentes.source = ConfirmAntecedentes ? "img/ok.png" : "img/nok.png";
			imgPrescripciones.source = ConfirmPrescripciones ? "img/ok.png" : "img/nok.png";
			imgVacunas.source = ConfirmVacunaciones ? "img/ok.png" : "img/nok.png";
			imgDatosConsulta.source = ConfirmDatosConsulta ? "img/ok.png" : "img/nok.png";
			imgDerivacion.source = ConfirmDerivaciones ? "img/ok.png" : "img/nok.png";
			imgIndicarPracticas.source = ConfirmIndicarPracticas ? "img/ok.png" : "img/nok.png";
			imgCargarResultados.source = ConfirmCargaResultados ? "img/ok.png" : "img/nok.png";
			
			imgEspecialidad.source = ConfirmEspecialidad ? "img/ok.png" : "img/nok.png";
		}		
	}
	
	private function fncGuardarConsulta():void
	{
		var xmlGuardar : XML = <datos></datos>;
		
		if(ConfirmDatosConsulta){
			xmlGuardar.appendChild(ModDatosConsulta.xmlDiagnosticos);
			xmlGuardar.appendChild(ModDatosConsulta.xmlDatosConsulta);
			if (ConfirmAntecedentes){xmlGuardar.appendChild(ModAntecedentes.xmlAntecedentes)}
			if (ConfirmPrescripciones){xmlGuardar.appendChild(ModPrescripciones.xmlPrescripciones)}
			if (ConfirmIndicarPracticas){xmlGuardar.appendChild(ModIndicarPracticas.xmlPracticas)}
			if (ConfirmCargaResultados){xmlGuardar.appendChild(ModResultadosPracticas.xmlResultados)}
			if (ConfirmDerivaciones){xmlGuardar.appendChild(ModDerivaciones.xmlDerivacion)}
			if (ConfirmVacunaciones){xmlGuardar.appendChild(ModVacunacion.xmlVacunaciones)}
			
			if (ConfirmEspecialidad){
				consultaTabs.getChildren()[8].xmlModel.ingresos_especialidad.@id_especialidad = id_especialidad;
				xmlGuardar.appendChild(consultaTabs.getChildren()[8].xmlModel)
			}
			
			//Alert.show(xmlGuardar.toXMLString());
			//Alert.show(consultaTabs.getChildren()[8].xmlModel.toXMLString());
			
			httpGuardar.send({rutina:"guardar_datos", xmlDatos:xmlGuardar});
		}else{
			Alert.show("Debe Confirmar los Datos de la Consulta","ERROR");
		}
	}
	
	private function fncImprimirPrescripciones():void
	{			
		//Creo los contenedores para enviar datos y recibir respuesta
		var url:String = "prescripciones/view_prescripciones.php";
		var enviar:URLRequest = new URLRequest(url);
		var recibir:URLLoader = new URLLoader();
	 		 
		//Creo la variable que va a ir dentro de enviar, con los campos que tiene que recibir el PHP.
		var variables:URLVariables = new URLVariables();
		
		variables.id_ingreso = _idIngreso;					
					
		//Indico que voy a enviar variables dentro de la petición
		enviar.data = variables;
		
		navigateToURL(enviar);
	}
	
	private function fncResultAlta(e:Event):void{
		Alert.show("Se guardaron Exitosamente los datos","Datos Consulta");
		if (ConfirmImprimirPrescripciones == true) fncImprimirPrescripciones();
		dispatchEvent(new Event("SelectPrincipal"));
	}