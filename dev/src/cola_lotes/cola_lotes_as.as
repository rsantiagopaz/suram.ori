import clases.HTTPServices;

import flash.events.Event;
import flash.utils.Timer;

import mx.controls.Alert;
import mx.events.CloseEvent;
import mx.core.UIComponent;
import mx.events.ValidationResultEvent;
import mx.validators.Validator;

include "../control_acceso.as";


[Bindable] private var httpEspera : HTTPServices = new HTTPServices;
[Bindable] private var httpAtendidos : HTTPServices = new HTTPServices;

private var timerActColas : Timer;
private var _idIngreso : String;

public function get idIngreso():String{
	return _idIngreso;
}
	
public function fncDetenerTimer():void { timerActColas.stop(); }
public function fncIniciarTimer():void { timerActColas.start(); }

//inicializa las variales necesarias para el modulo
public function fncInit(e:Event):void
{
	//Timer para Actualizar las Colas
	timerActColas = new Timer(300000);
	timerActColas.addEventListener(TimerEvent.TIMER, fncTraerColas);
	timerActColas.start();
	//http utilizado para traer los pacientes en la cola de espera
	httpEspera.url = "cola_lotes/cola_lotes.php";
	httpEspera.addEventListener("acceso",acceso);
	httpEspera.send({rutina:"lista_espera"});
	//filtro de busqueda de pacientes en cola de espera
	txtNombreEspera.addEventListener("change",fncFiltrarEspera);
	//filtro de busqueda de pacientes en cola de espera de acuerdo al médico tratante
	txtMedicoEspera.addEventListener("change",fncFiltrarEsperaMedico);
	//http utilizado para traer los pacientes ya Atendidos
	httpAtendidos.url = "cola_lotes/cola_lotes.php";
	httpAtendidos.addEventListener("acceso",acceso);
	httpAtendidos.send({rutina:"lista_atendido"});
	//filtro de busqueda en la cola de pacientes ya Atendidos
	txtNombreAtendido.addEventListener("change",fncFiltrarAtendido);
	btnBuscarEspera.addEventListener("click",fncBuscarPacientesEspera);
	btnBuscarAtendido.addEventListener("click",fncBuscarPacientesAtendido);
}

private function calcRowColor(item:Object, rowIndex:int,
 dataIndex:int, color:uint):uint
 {
   if(item.@estado_turno == 'AU')
     return 0xFF8800;
   else
     return color;
 }
 
private function fncBuscarPacientesEspera(e:Event):void
{
	var error:Array = Validator.validateAll([validFechaDesdeEspera,validFechaHastaEspera]);
	if (error.length>0) {
		((error[0] as ValidationResultEvent).target.source as UIComponent).setFocus();
	} else {
		var fecha_desde_espera:String;
		var fecha_hasta_espera:String;
		
		fecha_desde_espera = (dfFechaDesdeEspera.selectedDate != null) ? dfFechaDesdeEspera.text : "";
		fecha_hasta_espera = (dfFechaHastaEspera.selectedDate != null) ? dfFechaHastaEspera.text : "";
		httpEspera.send({rutina:"lista_espera",filtro_espera:txtNombreEspera.text,filtro_espera_medico:txtMedicoEspera.text,fecha_desde_espera:dfFechaDesdeEspera.text,fecha_hasta_espera:dfFechaHastaEspera.text});
	}
}

private function fncBuscarPacientesAtendido(e:Event):void
{
	var error:Array = Validator.validateAll([validFechaDesdeAtendido,validFechaHastaAtendido]);
	if (error.length>0) {
		((error[0] as ValidationResultEvent).target.source as UIComponent).setFocus();
	} else {
		var fecha_desde_atendido:String;
		var fecha_hasta_atendido:String;
		
		fecha_desde_atendido = (dfFechaDesdeAtendido.selectedDate != null) ? dfFechaDesdeAtendido.text : "";
		fecha_hasta_atendido = (dfFechaHastaAtendido.selectedDate != null) ? dfFechaHastaAtendido.text : "";
		httpAtendidos.send({rutina:"lista_atendido",filtro_atendido:txtNombreAtendido.text,fecha_desde_atendido:dfFechaDesdeAtendido.text,fecha_hasta_atendido:dfFechaHastaAtendido.text});
	}
}

//filtra la cola de espera
private function fncFiltrarEspera(e:Event):void
{
	var fecha_desde_espera:String;
	var fecha_hasta_espera:String;
	
	fecha_desde_espera = (dfFechaDesdeEspera.selectedDate != null) ? dfFechaDesdeEspera.text : "";
	fecha_hasta_espera = (dfFechaHastaEspera.selectedDate != null) ? dfFechaHastaEspera.text : "";
	if (!(txtNombreEspera.length % 2)){
		httpEspera.send({rutina:"lista_espera",filtro_espera:txtNombreEspera.text,filtro_espera_medico:txtMedicoEspera.text,fecha_desde_espera:dfFechaDesdeEspera.text,fecha_hasta_espera:dfFechaHastaEspera.text});
	}
}

//filtra la cola de espera de acuerdo al médico tratante
private function fncFiltrarEsperaMedico(e:Event):void
{
	var fecha_desde_espera:String;
	var fecha_hasta_espera:String;
	
	fecha_desde_espera = (dfFechaDesdeEspera.selectedDate != null) ? dfFechaDesdeEspera.text : "";
	fecha_hasta_espera = (dfFechaHastaEspera.selectedDate != null) ? dfFechaHastaEspera.text : "";
	if (txtMedicoEspera.text.length == 3) {
		httpEspera.send({rutina:"lista_espera",filtro_espera:txtNombreEspera.text,filtro_espera_medico:txtMedicoEspera.text,fecha_desde_espera:dfFechaDesdeEspera.text,fecha_hasta_espera:dfFechaHastaEspera.text});
	}
	if (txtMedicoEspera.text.length > 3) {
		// Se filtran los datos en la grilla para mostrar los que coinciden con el criterio
		// definido
  		gridEspera.dataProvider.filterFunction = filtroTexto;
  		// Se refresca el contenido de la grilla
        gridEspera.dataProvider.refresh();	
	}
}

private function filtroTexto (item : Object) : Boolean
{	
	return item.@medico.toString().toLowerCase().indexOf(txtMedicoEspera.text.toLowerCase()) != -1;   
}

//filtra la cola de pacientes atendidos
private function fncFiltrarAtendido(e:Event):void
{
	var fecha_desde_atendido:String;
	var fecha_hasta_atendido:String;
	
	fecha_desde_atendido = (dfFechaDesdeAtendido.selectedDate != null) ? dfFechaDesdeAtendido.text : "";
	fecha_hasta_atendido = (dfFechaHastaAtendido.selectedDate != null) ? dfFechaHastaAtendido.text : "";
	if (!(txtNombreAtendido.length % 2)){
		httpAtendidos.send({rutina:"lista_atendido",filtro_atendido:txtNombreAtendido.text,fecha_desde_atendido:dfFechaDesdeAtendido.text,fecha_hasta_atendido:dfFechaHastaAtendido.text});
	}
}

//actualiza las dos colas de espera
public function fncTraerColas(e:Event):void
{
	var fecha_desde_espera:String;
	var fecha_hasta_espera:String;
	var fecha_desde_atendido:String;
	var fecha_hasta_atendido:String;
	
	fecha_desde_espera = (dfFechaDesdeEspera.selectedDate != null) ? dfFechaDesdeEspera.text : "";
	fecha_hasta_espera = (dfFechaHastaEspera.selectedDate != null) ? dfFechaHastaEspera.text : "";		
	fecha_desde_atendido = (dfFechaDesdeAtendido.selectedDate != null) ? dfFechaDesdeAtendido.text : "";
	fecha_hasta_atendido = (dfFechaHastaAtendido.selectedDate != null) ? dfFechaHastaAtendido.text : "";
	
	httpEspera.send({rutina:"lista_espera",filtro_espera:txtNombreEspera.text,fecha_desde_espera:dfFechaDesdeEspera.text,fecha_hasta_espera:dfFechaHastaEspera.text});
	httpAtendidos.send({rutina:"lista_atendido",filtro_atendido:txtNombreAtendido.text,fecha_desde_atendido:dfFechaDesdeAtendido.text,fecha_hasta_atendido:dfFechaHastaAtendido.text});
}


//envia el evento atender paciente al modulo padre
public function fncAtender():void
{
	_idIngreso = gridEspera.selectedItem.@id_ingreso;
	timerActColas.stop();
	dispatchEvent(new Event("EventAtenderPaciente"));
}

//Solicita confirmacion para marcar a un paciente como ausente
public function fncAusente():void
{
	Alert.show("¿Desea Poner Como Ausente al Paciente "+gridEspera.selectedItem.@apeynom_paciente+"?", "Confirmar", Alert.OK | Alert.CANCEL, this, fncConfirmAusente, null, Alert.OK);	
}

public function fncRecuperarTurno():void
{
	Alert.show("¿Desea Recuperar el Turno del Paciente "+gridAtendidos.selectedItem.@apeynom_paciente+"?", "Confirmar", Alert.OK | Alert.CANCEL, this, fncConfirmRecuperar, null, Alert.OK);	
}

// Marca al paciente seleccionado de la lista de espera como Ausente
private function fncConfirmAusente(e:CloseEvent):void 
{
	//http Usado para marcar como ausente a un paciente
	var httpAusente : HTTPServices = new HTTPServices;
	httpAusente.url = "cola_lotes/cola_lotes.php";
	httpAusente.addEventListener("acceso",acceso);
	var _idTurno : String = gridEspera.selectedItem.@id_turno;
    if (e.detail==Alert.OK){
    	httpAusente.send({rutina:"poner_ausente",id_turno:_idTurno});
    	httpEspera.send({rutina:"lista_espera"});
	}
}

private function fncConfirmRecuperar(e:CloseEvent):void 
{
	//http Usado para recuperar el turno de un paciente
	var httpRecuperar : HTTPServices = new HTTPServices;
	httpRecuperar.url = "cola_lotes/cola_lotes.php";
	httpRecuperar.addEventListener("acceso",acceso);
	var _idTurno : String = gridAtendidos.selectedItem.@id_turno;
    if (e.detail==Alert.OK){
    	httpRecuperar.send({rutina:"recuperar_turno",id_turno:_idTurno});
    	httpAtendidos.send({rutina:"lista_atendido"});
	}
}

// envia el evento para que el modulo padre se encarde de modificar la consulta
public function fncModificarConsulta():void
{
	_idIngreso = gridAtendidos.selectedItem.@id_ingreso;
	timerActColas.stop();
	dispatchEvent(new Event("EventModificarConsulta"));	
}
	