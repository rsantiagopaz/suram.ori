// ActionScript file
	import clases.HTTPServices;
	
	import flash.events.Event;
	import flash.utils.Timer;
	
	import mx.controls.Alert;
	import mx.events.CloseEvent;
	
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
		timerActColas = new Timer(30000);
		timerActColas.addEventListener(TimerEvent.TIMER, fncTraerColas);
		timerActColas.start();
		//http utilizado para traer los pacientes en la cola de espera
		httpEspera.url = "colas/colas.php";
		httpEspera.addEventListener("acceso",acceso);
		httpEspera.send({rutina:"lista_espera"});
		//filtro de busqueda de pacientes en cola de espera
		txtNombreEspera.addEventListener("change",fncFiltrarEspera);
		//http utilizado para traer los pacientes ya Atendidos
		httpAtendidos.url = "colas/colas.php";
		httpAtendidos.addEventListener("acceso",acceso);
		httpAtendidos.send({rutina:"lista_atendido"});
		//filtro de busqueda en la cola de pacientes ya Atendidos
		txtNombreAtendido.addEventListener("change",fncFiltrarAtendido);
	}
	
	private function calcRowColor(item:Object, rowIndex:int,
     dataIndex:int, color:uint):uint
	 {
	   if(item.@estado_turno == 'AU')
	     return 0xFF8800;
	   else
	     return color;
	 }
	
	//filtra la cola de espera
	private function fncFiltrarEspera(e:Event):void
	{
			if (!(txtNombreEspera.length % 2)){
				httpEspera.send({rutina:"lista_espera",filtro_espera:txtNombreEspera.text});
			}
	}
	
	//filtra la cola de pacientes atendidos
	private function fncFiltrarAtendido(e:Event):void
	{
			if (!(txtNombreAtendido.length % 2)){
				httpAtendidos.send({rutina:"lista_atendido",filtro_atendido:txtNombreAtendido.text});
			}
	}
	
	//actualiza las dos colas de espera
	public function fncTraerColas(e:Event):void
	{
		httpEspera.send({rutina:"lista_espera"});
		httpAtendidos.send({rutina:"lista_atendido"});
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
		httpAusente.url = "colas/colas.php";
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
		httpRecuperar.url = "colas/colas.php";
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
	