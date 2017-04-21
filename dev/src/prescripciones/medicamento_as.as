import clases.HTTPServices;

import flash.events.Event;

import mx.core.UIComponent;
import mx.events.ListEvent;
import mx.events.ValidationResultEvent;
import mx.managers.PopUpManager;
import mx.rpc.events.ResultEvent;
import mx.validators.Validator;

include "../control_acceso.as";

[Bindable] private var httpAcMedicamentos : HTTPServices = new HTTPServices;
[Bindable] private var _xmlMedicamento : XML = <prescripcion estado="" id_prescripcion="0" id_vademecum="0" posologia="" entregado="" concentracion="" presentacion="" monodroga="" descrip="" />;
private var _accion : String;

public function get xmlMedicamento():XML
{
	return _xmlMedicamento.copy();
}

public function set xmlMedicamento(med:XML):void
{
	_xmlMedicamento = med;
	_accion = "editar";
}

private function fncInit():void
{
	//preparo el PopUp Para que se cierre con esc y marco el default button
	this.defaultButton = btnGrabar;
	this.addEventListener(KeyboardEvent.KEY_UP,function(e:KeyboardEvent):void{if (e.keyCode==27) btnCancel.dispatchEvent(new MouseEvent(MouseEvent.CLICK))});
	//preparo el autocomplete
	acMedicamento.addEventListener(FocusEvent.FOCUS_OUT,function(e:Event):void{acMedicamento.errorString=(acMedicamento.selectedItem==null ? 'Debe seleccionar un medicamento' : '')});
	acMedicamento.addEventListener(ListEvent.CHANGE,ChangeAcMedicamento);
	acMedicamento.addEventListener(KeyboardEvent.KEY_UP,fncKeyUpMedicamento);
	acMedicamento.labelField = "@descrip";
	acMedicamento.setFocus();
	//preparo el httpservice necesario para el autocomplete
	httpAcMedicamentos.url = "prescripciones/medicamento.php";
	httpAcMedicamentos.addEventListener("acceso",acceso);
	httpAcMedicamentos.addEventListener(ResultEvent.RESULT,fncCargarAcMedicamentos);
	// escucho evento de los botones
	btnCancel.addEventListener("click",fncCerrar);
	btnGrabar.addEventListener("click",fncConfirmar);
	// si se trata de una edicion cargo el valor a editar
	if (_accion == "editar"){
		_xmlMedicamento.@estado = "M";
		acMedicamento.typedText = _xmlMedicamento.@descrip;
		acMedicamento.text = _xmlMedicamento.@descrip;
		httpAcMedicamentos.send({rutina:"traer_medicamentos",descrip:acMedicamento.text});
		txaPosologia.text = _xmlMedicamento.@posologia;
		//dfPrescripcion.text = _xmlMedicamento.@fecha_prescripcion;
	}
}

public function modificar():void
{
		_xmlMedicamento.@estado = "M";
		acMedicamento.typedText = _xmlMedicamento.@descrip;
		httpAcMedicamentos.send({rutina:"traer_medicamentos",descrip:acMedicamento.text});
		txaPosologia.text = _xmlMedicamento.@posologia;
		//dfPrescripcion.text = _xmlMedicamento.@fecha_prescripcion;
}

private function fncKeyUpMedicamento(e:KeyboardEvent):void
{
	if (e.keyCode==38 || e.keyCode==40) {
		acMedicamento.toolTip = '';
		acMedicamento.toolTip = acMedicamento.text;	
	}		
}

private function ChangeAcMedicamento(e:Event):void{
	if (acMedicamento.text.length==3){
		httpAcMedicamentos.send({rutina:"traer_medicamentos",descrip:acMedicamento.text});
	}
}
	
private function fncCargarAcMedicamentos(e:Event):void{
	acMedicamento.typedText = acMedicamento.text;
	acMedicamento.dataProvider = httpAcMedicamentos.lastResult.medicamento;
}

private function fncCerrar(e:Event):void
{
	PopUpManager.removePopUp(this)	
}

private function fncConfirmar(e:Event):void
{
	var error:String = '';
	//var error:Array = Validator.validateAll([validPosologia,validFecha]);
	if (error.length>0) {
		((error[0] as ValidationResultEvent).target.source as UIComponent).setFocus();
	} else if (acMedicamento.selectedItem==null) {
		acMedicamento.errorString='Debe seleccionar un medicamento válido';
		acMedicamento.setFocus();
	}else {
		_xmlMedicamento.@descrip = acMedicamento.text;
		_xmlMedicamento.@id_vademecum= acMedicamento.selectedItem.@id_vademecum;
		_xmlMedicamento.@presentacion= acMedicamento.selectedItem.@presentacion;
		_xmlMedicamento.@monodroga= acMedicamento.selectedItem.@monodroga;
		_xmlMedicamento.@concentracion= acMedicamento.selectedItem.@concentracion;
		_xmlMedicamento.@descrip= acMedicamento.selectedItem.@descrip;
		_xmlMedicamento.@posologia = txaPosologia.text;
		//_xmlMedicamento.@fecha_prescripcion= dfPrescripcion.text;
		dispatchEvent(new Event("EventConfirmarMedicamento"));
	}	
}

private function customFilterFunction(element:*, text:String):Boolean 
{	
    var label:String = element.@descrip;    
    return (label.toLowerCase().indexOf(text.toLowerCase()) != -1);
}