package clases
{
	import mx.controls.DateField;

	public class DateFieldEs extends DateField
	{
		public function DateFieldEs()
		{
			super();
			formatString = "DD/MM/YYYY";
			dayNames = [ 'D', 'L', 'M', 'M', 'J', 'V', 'S' ];
			monthNames = [ 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' ];
		}
		
	}
}
