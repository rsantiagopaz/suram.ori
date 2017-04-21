// ActionScript file
	import clases.HTTPServices;
	
	import consultar_practicas.twpractica;
	
	import flash.events.Event;
	
	import mx.managers.PopUpManager;
	import mx.rpc.events.ResultEvent;

	include "../control_acceso.as";
	
	[Bindable] private var _xmlPracticas:XML = <practicas></practicas>;
	[Bindable] private var httpPracticas : HTTPServices = new HTTPServices;
	private var twPractica:twpractica;
	[Bindable] private var _abmPra:Boolean = false;
	
	private function fncInit():void
	{
		if (parentApplication.abmPra == true) {			
			_abmPra = true;	
		} else {
			_abmPra = false;
		}
		httpPracticas.url = "consultar_practicas/practicas.php";
		httpPracticas.addEventListener("acceso",acceso);
		httpPracticas.addEventListener(ResultEvent.RESULT,fncCargarPracticas);
		// escucho evento de los botones
		//btnNuevaPractica.addEventListener("click" ,fncAgregarPractica);
		btnCerrar.addEventListener("click",fncCerrar);
		txtPractica.addEventListener("change",fncTraerPractica);
		btnBuscar.addEventListener("click",fncTraerPracticaBoton);
	}
	
	private function fncCerrar(e:Event):void{
		dispatchEvent(new Event("SelectPrincipal"));
	}
		
	private function fncTraerPractica(e:Event):void{
		if (txtPractica.text.length==3){
			httpPracticas.send({rutina:"traer_practicas",filter:txtPractica.text});
		}
		if(txtPractica.text.length>3 && gridEstudios.dataProvider){
		
	  		gridEstudios.dataProvider.filterFunction = filtroTexto;
            gridEstudios.dataProvider.refresh();			
		}
	}
	
	private function fncCargarPracticas(e:Event):void {
		_xmlPracticas = <practicas></practicas>;
		_xmlPracticas.appendChild(httpPracticas.lastResult.practica);		
	}
	
	private function filtroTexto (item : Object) : Boolean
	{
		var isMatch:Boolean = false;
		if(item.@descripcion.toString().toLowerCase().search(txtPractica.text.toLowerCase()) != -1)
		{
			isMatch = true;
		}		
		return isMatch;                        
		
	}
	
	private function fncTraerPracticaBoton(e:Event):void{
		httpPracticas.send({rutina:"traer_practicas",filter:txtPractica.text});
	}
	
	private function fncAgregarPractica(e:Event):void
	{
		twPractica = new twpractica;
		twPractica.addEventListener("EventAlta",fncAltaPractica);
		PopUpManager.addPopUp(twPractica,this,true);
		PopUpManager.centerPopUp(twPractica);
	}
	
	private function fncAltaPractica(e:Event):void
	{
		var xmlPractica : XML = twPractica.xmlPractica;
		_xmlPracticas.appendChild(xmlPractica);
		PopUpManager.removePopUp(e.target as twpractica);		
	}
	
	public function fncEditar():void
	{
		twPractica = new twpractica;
		twPractica.xmlPractica =  (gridEstudios.selectedItem as XML).copy();
		twPractica.addEventListener("EventEdit",fncEditarPractica);
		PopUpManager.addPopUp(twPractica,this,true);
		PopUpManager.centerPopUp(twPractica);
	}
	
	public function fncEliminar():void
	{
		twPractica = new twpractica;
		twPractica.xmlPractica2 =  (gridEstudios.selectedItem as XML).copy();
		twPractica.addEventListener("EventDelete",fncEliminarMonodroga);
		PopUpManager.addPopUp(twPractica,this,true);
		PopUpManager.centerPopUp(twPractica);
	}
	
	private function fncEditarPractica(e:Event):void
	{
		_xmlPracticas.practica[(gridEstudios.selectedItem as XML).childIndex()] = twPractica.xmlPractica;
		PopUpManager.removePopUp(e.target as twpractica);		
	}
	
	private function fncEliminarMonodroga(e:Event):void
	{
		delete _xmlPracticas.practica[(gridEstudios.selectedItem as XML).childIndex()];
		PopUpManager.removePopUp(e.target as twpractica);		
	}
