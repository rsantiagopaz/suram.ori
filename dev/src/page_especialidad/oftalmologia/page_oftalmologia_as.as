
import mx.controls.Alert;
private var _xmlDatosPaciente : XML = <datospaciente></datospaciente>;

public function Panel_creationComplete() : void
{
	btnConfirmar.addEventListener("click", btnConfirmar_click);
	btnCancelar.addEventListener("click", btnCancelar_click);
}


public function fncInit():void
{
	_xmlDatosPaciente = <datospaciente></datospaciente>;
	_xmlDatosPaciente.appendChild(this.parentDocument.xmlDatosPaciente.paciente);
	txiApenom.text = _xmlDatosPaciente.paciente.@apeynom;
	
	lblEstado.text = '';
	
	//Alert.show(this.parentDocument.xmlDatosPaciente.toXMLString());
	xmlModel.ingresos_especialidad = this.parentDocument.xmlDatosPaciente.ingresos_especialidad.ingresos_especialidad;
	//Alert.show("init");
}


public function btnConfirmar_click(e:MouseEvent) : void {
	
	this.parentDocument.ConfirmEspecialidad = true;
	lblEstado.text = 'Pestaña Confirmada';
}


public function btnCancelar_click(e:MouseEvent) : void {
	this.parentDocument.ConfirmEspecialidad = false;
	lblEstado.text = 'Pestaña Cancelada';
}