import clases.HTTPServices;
import flash.events.Event;
import flash.events.KeyboardEvent;
import mx.events.ListEvent;
import mx.managers.PopUpManager;
import mx.rpc.events.ResultEvent;

include "../control_acceso.as";

[Bindable] private var httpAcDiagnostico : HTTPServices = new HTTPServices;
[Bindable] private var _xmlDiagnostico : XML = <diagnostico id_solitudes="0" tipo_diagnostico="S" id_diagnostico="0" descripcion="" coddescrip="" />;
private var _accion : String;

public function get xmlDiagnostico():XML
{
	return _xmlDiagnostico.copy();
}

public function set xmlDiagnostico(diag:XML):void
{
	_xmlDiagnostico = diag;
	_accion = "editar";
}

private function fncInit():void
{
	//preparo el PopUp Para que se cierre con esc y marco el default button
	this.defaultButton = btnGrabar;
	this.addEventListener(KeyboardEvent.KEY_UP,function(e:KeyboardEvent):void{if (e.keyCode==27) btnCancel.dispatchEvent(new MouseEvent(MouseEvent.CLICK))});
	//preparo el autocomplete
	acDiagnostico.addEventListener(FocusEvent.FOCUS_OUT,function(e:Event):void{acDiagnostico.errorString=(acDiagnostico.selectedItem==null ? 'Debe seleccionar un Diagnostico' : '')});
	acDiagnostico.addEventListener(ListEvent.CHANGE,ChangeAcDiagnostico);
	acDiagnostico.addEventListener(KeyboardEvent.KEY_UP,fncKeyUpDiagnostico);
	acDiagnostico.labelField = "@coddescrip";
	acDiagnostico.setFocus();
	rbDiagSi.selected = false;
	//preparo el httpservice necesario para el autocomplete
	httpAcDiagnostico.url = "datos_consulta/diagnostico.php";
	httpAcDiagnostico.addEventListener("acceso",acceso);
	httpAcDiagnostico.addEventListener(ResultEvent.RESULT,fncCargarAcDiagnostico);
	// escucho evento de los botones
	btnCancel.addEventListener("click",fncCerrar);
	btnGrabar.addEventListener("click",fncConfirmar);
	// si se trata de una edicion cargo el valor a editar
	if (_accion == "editar"){
		acDiagnostico.typedText = _xmlDiagnostico.@coddescrip;
		acDiagnostico.text = _xmlDiagnostico.@coddescrip;
		httpAcDiagnostico.send({rutina:"traer_diagnosticos",descripcion:acDiagnostico.text});
		if (_xmlDiagnostico.@tipo_diagnostico == 'P'){
			rbDiagSi.selected = true;
			rbDiagNo.selected = false;	
		}else{
			rbDiagSi.selected = false;
			rbDiagNo.selected = true;
		}
	}
}

private function fncKeyUpDiagnostico(e:KeyboardEvent):void
{
	if (e.keyCode==38 || e.keyCode==40) {
		acDiagnostico.toolTip = '';
		acDiagnostico.toolTip = acDiagnostico.text;	
	}		
}

private function ChangeAcDiagnostico(e:Event):void{
	if (acDiagnostico.text.length==3){
		httpAcDiagnostico.send({rutina:"traer_diagnosticos",descripcion:acDiagnostico.text});
	}
}
	
private function fncCargarAcDiagnostico(e:Event):void{
	acDiagnostico.typedText = acDiagnostico.text;
	acDiagnostico.dataProvider = httpAcDiagnostico.lastResult.diagnostico;
}

private function fncCerrar(e:Event):void
{
	PopUpManager.removePopUp(this)	
}

private function fncConfirmar(e:Event):void
{
	if (acDiagnostico.selectedItem==null) {
		acDiagnostico.errorString='Debe seleccionar un Diagnóstico válido';
		acDiagnostico.setFocus();
	}else {
		_xmlDiagnostico.@descripcion = acDiagnostico.selectedItem.@descripcion;
		_xmlDiagnostico.@coddescrip = acDiagnostico.selectedItem.@coddescrip;
		_xmlDiagnostico.@id_diagnostico = acDiagnostico.selectedItem.@id_diagnostico;
		_xmlDiagnostico.@codigo = acDiagnostico.selectedItem.@codigo;
		if (rbDiagSi.selected){
			_xmlDiagnostico.@tipo_diagnostico = 'P';
		}else{
			_xmlDiagnostico.@tipo_diagnostico = 'S';
		}	
		dispatchEvent(new Event("EventConfirmarDiagnostico"));
	}	
}

private function customFilterFunction(element:*, text:String):Boolean 
{	
    var label:String = element.@coddescrip;    
    return (label.toLowerCase().indexOf(text.toLowerCase()) != -1);
}