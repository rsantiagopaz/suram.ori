// ActionScript file
	import clases.HTTPServices;
	import flash.events.Event;
	import flash.events.KeyboardEvent;
	import mx.events.ListEvent;
	import mx.managers.PopUpManager;
	import mx.rpc.events.ResultEvent;
	
	import consultar_vademecum.twmedicamento;

	include "../control_acceso.as";
	
	[Bindable] private var _xmlMedicamentos:XML = <medicamentos></medicamentos>;
	[Bindable] private var httpVademecum : HTTPServices = new HTTPServices;
	private var twMedicamento:twmedicamento;
	[Bindable] private var _abmVad:Boolean = false;
	
	private function fncInit():void
	{
		if (parentApplication.abmVad == true) {			
			_abmVad = true;	
		} else {
			_abmVad = false; 
		}
		httpVademecum.url = "consultar_vademecum/vademecum.php";
		httpVademecum.addEventListener("acceso",acceso);
		httpVademecum.addEventListener(ResultEvent.RESULT,fncCargarVademecum);
		// escucho evento de los botones
		//btnNuevoMedicamento.addEventListener("click" ,fncAgregarMedicamento);
		btnCerrar.addEventListener("click",fncCerrar);
		txtDroga.addEventListener("change",fncTraerVademecum);
		btnBuscar.addEventListener("click",fncTraerVademecumBoton);
	}
	
	private function fncCerrar(e:Event):void{
		dispatchEvent(new Event("SelectPrincipal"));
	}
		
	private function fncTraerVademecum(e:Event):void{
		if (txtDroga.text.length==3){
			httpVademecum.send({rutina:"traer_vademecum",filter:txtDroga.text});
		}
		if(txtDroga.text.length>3 && gridVademecum.dataProvider){
	  		gridVademecum.dataProvider.filterFunction = filtroTexto;
            gridVademecum.dataProvider.refresh();			
		}
	}
	
	private function fncCargarVademecum(e:Event):void {
		_xmlMedicamentos = <medicamentos></medicamentos>;
		_xmlMedicamentos.appendChild(httpVademecum.lastResult.vademecum);		
	}
	
	private function filtroTexto (item : Object) : Boolean
	{
		//return item.@monodroga.toString().substr(0, txtDroga.text.length).toLowerCase() == txtDroga.text.toLowerCase();   
		var isMatch:Boolean = false;
		if(item.@monodroga.toString().toLowerCase().search(txtDroga.text.toLowerCase()) != -1)
		{
			isMatch = true;
		}		
		return isMatch;	
	}
	
	private function fncTraerVademecumBoton(e:Event):void{
		httpVademecum.send({rutina:"traer_vademecum",filter:txtDroga.text});
	}
	
	private function fncAgregarMedicamento(e:Event):void
	{
		twMedicamento = new twmedicamento;
		twMedicamento.addEventListener("EventAlta",fncAltaMedicamento);
		PopUpManager.addPopUp(twMedicamento,this,true);
		PopUpManager.centerPopUp(twMedicamento);
	}
	
	private function fncAltaMedicamento(e:Event):void{
		var xmlMedicamento : XML = twMedicamento.xmlMedicamento;
		_xmlMedicamentos.appendChild(xmlMedicamento);
		PopUpManager.removePopUp(e.target as twmedicamento);		
	}
	
	public function fncEditar():void
	{
		twMedicamento = new twmedicamento;
		twMedicamento.xmlMedicamento =  (gridVademecum.selectedItem as XML).copy();
		twMedicamento.addEventListener("EventEdit",fncEditarMedicamento);
		PopUpManager.addPopUp(twMedicamento,this,true);
		PopUpManager.centerPopUp(twMedicamento);
	}
	
	public function fncEliminar():void
	{
		twMedicamento = new twmedicamento;
		twMedicamento.xmlMedicamento2 =  (gridVademecum.selectedItem as XML).copy();
		twMedicamento.addEventListener("EventDelete",fncEliminarMonodroga);
		PopUpManager.addPopUp(twMedicamento,this,true);
		PopUpManager.centerPopUp(twMedicamento);
	}
	
	private function fncEditarMedicamento(e:Event):void
	{
		_xmlMedicamentos.vademecum[(gridVademecum.selectedItem as XML).childIndex()] = twMedicamento.xmlMedicamento;
		PopUpManager.removePopUp(e.target as twmedicamento);		
	}
	
	private function fncEliminarMonodroga(e:Event):void
	{
		delete _xmlMedicamentos.vademecum[(gridVademecum.selectedItem as XML).childIndex()];
		PopUpManager.removePopUp(e.target as twmedicamento);		
	}
