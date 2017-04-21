// ActionScript file
import mx.controls.Alert;

	
	[Bindable] private var _xmlCobertura:XML = <coberturas></coberturas>;
	[Bindable] private var _xmlDatosPaciente:XML = <datospaciente></datospaciente>;
	private var _idIngreso:String;
	
	//inicializa las variales necesarias para el modulo
	public function fncInit():void
	{	
		_xmlCobertura = <coberturas></coberturas>;
		_xmlDatosPaciente = <datospaciente></datospaciente>;
		_xmlCobertura.appendChild(this.parentDocument.xmlDatosPaciente.cobertura);
		_xmlDatosPaciente.appendChild(this.parentDocument.xmlDatosPaciente.paciente);
		_idIngreso = this.parentDocument.idIngreso;
		txiApenom.text = _xmlDatosPaciente.paciente.@apeynom;
		txiDNI.text = _xmlDatosPaciente.paciente.@nrodoc;
		txiSexo.text = _xmlDatosPaciente.paciente.@sexo;
		txiEdad.text = _xmlDatosPaciente.paciente.@edad;
		txiDomicilio.text = _xmlDatosPaciente.paciente.@domicilio;
		txiLocalidad.text = _xmlDatosPaciente.paciente.@localidad;
		btnHistorial.addEventListener("click" ,fncVerHistorial);
		btnImprimirHC.addEventListener("click",fncImprimir);
	}
	
	private function fncVerHistorial():void
	{
		dispatchEvent(new Event("ViewHistorial"));
	}
	
	private function fncImprimir(e:Event):void
	{			
		//Creo los contenedores para enviar datos y recibir respuesta
		var url:String = "datospersonales/view_hc.php";
		var enviar:URLRequest = new URLRequest(url);
		var recibir:URLLoader = new URLLoader();
	 		 
		//Creo la variable que va a ir dentro de enviar, con los campos que tiene que recibir el PHP.
		var variables:URLVariables = new URLVariables();
		
		variables.id_ingreso = _idIngreso;					
					
		//Indico que voy a enviar variables dentro de la petición
		enviar.data = variables;
		
		navigateToURL(enviar);
	}
	