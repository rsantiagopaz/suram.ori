// ActionScript file
import flash.events.Event;

import mx.controls.Alert;

import mx.core.UIComponent;
import mx.events.ListEvent;
import mx.events.ValidationResultEvent;
import mx.rpc.events.ResultEvent;
import mx.validators.Validator;

include "../control_acceso.as";
	
[Bindable] private var httpAcServicio : HTTPServices = new HTTPServices;
[Bindable] private var _xmlDiagnosticos : XML = <diagnosticos></diagnosticos>;
[Bindable] private var _xmlDiagnostico : XML = <diagnostico id_diagnostico="0" descripcion="" coddescrip="" />;
private var _perfilEstMedico:Boolean;

public function fncInit():void
{	
	//preparo el autocomplete	
	acServicio.addEventListener(ListEvent.CHANGE,ChangeAcServicio);	
	acServicio.labelField = "@denominacion";
	btnGenerarReporte.addEventListener("click",fncGenerarConsulta);
	//preparo el httpservice necesario para el autocomplete
	httpAcServicio.url = "generadorconsultas/generadorconsultas.php";
	httpAcServicio.addEventListener("acceso",acceso);
	httpAcServicio.addEventListener(ResultEvent.RESULT,fncCargarAcServicio);
	if (this.parentApplication.controlAcceso.tienePerfil('030EME')) {		
		acServicio.text = this.parentApplication.controlAcceso.usuario_servicio.toString();
		acServicio.typedText = this.parentApplication.controlAcceso.usuario_servicio.toString();
		acServicio.enabled = false;
		_perfilEstMedico = true;
	} else {
		acServicio.text = '';
		acServicio.typedText = '';
		_perfilEstMedico = false;
	}
}

private function fncCerrar():void{
	dispatchEvent(new Event("SelectPrincipal"));
}

private function ChangeAcServicio(e:Event):void{
	if (acServicio.text.length==3){
		httpAcServicio.send({rutina:"traer_servicios",denominacion:acServicio.text});
	}
}
	
private function fncCargarAcServicio(e:Event):void{
	acServicio.typedText = acServicio.text;
	acServicio.dataProvider = httpAcServicio.lastResult.servicio;
}

private function fncGenerarConsulta(e:Event):void
{
	if (fncValidar()) {		
		var unservicio:String;
		var pacientes:String;
		var antecedentes:String;
		var diagnosticos:String;
		var prescripciones:String;								
		var url:String;	 		 
		//Creo la variable que va a ir dentro de enviar, con los campos que tiene que recibir el PHP.
		var variables:URLVariables = new URLVariables();
					
		variables.pacientes = (chkPacientes.selected == true) ? 'S' : 'N';;							
		variables.antecedentes = (chkAntecedentes.selected == true) ? 'S' : 'N';
		variables.diagnosticos = (chkDiagnosticos.selected == true) ? 'S' : 'N';
		variables.prescripciones = (chkPrescripciones.selected == true) ? 'S' : 'N';
		variables.fecha_desde = dtfDesde.text;
		variables.fecha_hasta = dtfHasta.text;
		
		//Creo los contenedores para enviar datos y recibir respuesta
		if (_perfilEstMedico == true)
			url = "generadorconsultas/view_resultado_consulta.php";
		else {
			variables.unservicio = (acServicio.selectedIndex != -1) ? 'S' : 'N';;
			variables.id_servicio = (acServicio.selectedIndex != -1) ? acServicio.selectedItem.@id_servicio : '';			
			url = "generadorconsultas/view_resultado_consulta_servicios.php";	
		}
		
		var enviar:URLRequest = new URLRequest(url);
		var recibir:URLLoader = new URLLoader();
					
		//Indico que voy a enviar variables dentro de la petición
		enviar.data = variables;
		
		navigateToURL(enviar);	
	}	
}

private function fncValidar():Boolean
{
	var error:Array = Validator.validateAll([validFechaDesde,validFechaHasta]);
	if (error.length>0) {
		((error[0] as ValidationResultEvent).target.source as UIComponent).setFocus();
		return false;
	} else {
		return true;
	} 
}