// ActionScript file
	
	import flash.events.Event;
	import flash.events.KeyboardEvent;
	
	import mx.core.UIComponent;
	import mx.events.ValidationResultEvent;
	import mx.managers.PopUpManager;
	import mx.validators.Validator;

	[Bindable] private var _xmlPractica : XML = <practica id_solitudes="0" descripcion="" resultados="" estado="" />;
	
	public function get xmlPractica():XML
	{
		return _xmlPractica.copy();
	}
	
	public function set xmlPractica(prac:XML):void
	{
		_xmlPractica = prac;
	}
	
	private function fncInit():void
	{
		//preparo el PopUp Para que se cierre con esc y marco el default button
		this.defaultButton = btnGrabar;
		this.addEventListener(KeyboardEvent.KEY_UP,function(e:KeyboardEvent):void{if (e.keyCode==27) btnCancel.dispatchEvent(new MouseEvent(MouseEvent.CLICK))});
		// escucho evento de los botones
		btnCancel.addEventListener("click",fncCerrar);
		btnGrabar.addEventListener("click",fncConfirmar);
		// cargo la descripcion y el resultado de la practica
		txaResultado.text = _xmlPractica.@resultados;
		txtPractica.text = _xmlPractica.@descripcion;
		txaResultado.setFocus();
	}
	
	private function fncCerrar(e:Event):void
	{
		PopUpManager.removePopUp(this)	
	}
	
	private function fncConfirmar(e:Event):void
	{
		var error:Array = Validator.validateAll([validResultado]);
		if (error.length>0) {
			((error[0] as ValidationResultEvent).target.source as UIComponent).setFocus();
		}else {
			_xmlPractica.@resultados = txaResultado.text;
			_xmlPractica.@estado= 'R';
			dispatchEvent(new Event("EventConfirmarResultado"));
		}	
		

	}