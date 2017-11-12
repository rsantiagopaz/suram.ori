import flash.events.MouseEvent;

import mx.controls.Alert;
import mx.events.MenuEvent;

private var _xmlDatosPaciente : XML = <datospaciente></datospaciente>;


public function Panel_creationComplete() : void
{
	btnConfirmar.addEventListener("click", btnConfirmar_click);
	btnCancelar.addEventListener("click", btnCancelar_click);
	
	pmb_sc_od.addEventListener("itemClick", pmb_itemClick);
	pmb_sc_oi.addEventListener("itemClick", pmb_itemClick);
	pmb_cc_od.addEventListener("itemClick", pmb_itemClick);
	pmb_cc_oi.addEventListener("itemClick", pmb_itemClick);
}


private function pmb_itemClick(evt: MenuEvent):void {
	if (evt.target.popUp.visible) evt.target.label = evt.item.@value;
}
            

public function fncInit():void
{
	_xmlDatosPaciente = <datospaciente></datospaciente>;
	_xmlDatosPaciente.appendChild(this.parentDocument.xmlDatosPaciente.paciente);
	txiApenom.text = _xmlDatosPaciente.paciente.@apeynom;
	
	lblEstado.text = '';
	
	
	
	
	xmlModel.ingresos_especialidad = this.parentDocument.xmlDatosPaciente.ingresos_especialidad.ingresos_especialidad;
}


public function btnConfirmar_click(e:MouseEvent) : void {
	
	this.parentDocument.ConfirmEspecialidad = true;
	lblEstado.text = 'Pestaña Confirmada';
}


public function btnCancelar_click(e:MouseEvent) : void {
	this.parentDocument.ConfirmEspecialidad = false;
	lblEstado.text = 'Pestaña Cancelada';
}
