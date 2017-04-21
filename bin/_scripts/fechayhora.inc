<?php

// Este programa devuelve la fecha y hora actual del sistema haciendo el cálculo 
// incrementando o decrementando la cantidad de horas que existe de diferencia
// entre la hora local y la del servidor ya que no siempre coinciden, puesto que
// el servidor se encuentra en otro país.


// Tomamos la fecha del Sistema en un vector _fecha
$_fecha=getdate();
$_hora     = $_fecha["hours"];
$_minutos  = $_fecha["minutes"];
$_segundos = $_fecha["seconds"];
$_dia      = $_fecha["mday"];
$_mes      = $_fecha["mon"];
$_ano      = $_fecha["year"];
// fin

$_auxhora  = $_hora;

// Aumentamos o disminuímos la hora según corresponda (si hora en el servidor es distinta a la hora local)

// Diferencia horaria (de -23 a +23 hs.)
$_incremento=0;

if ($_incremento<>0)
 {
  if ($_hora==0)
   $_hora=24;
  $_horafinal=$_hora+$_incremento;
  $_diferencia=24-$_horafinal;
  if ($_diferencia < 0)
    $_horafinal=$_diferencia * (-1);
   else
    {
     if ($_horafinal<0)
      $_horafinal=$_horafinal * (-1);
    }
  if ($_horafinal==24)
    $_horafinal=0;

  // En la variable $_horafinal queda la hora correcta (local).
  $_hora=$_horafinal;
  
 
  // Modifica fecha en función de hora
  if(($_auxhora+$_incremento)<0)
   {
    $_dia=$_dia-1;
    $_operacion=0;
   }
   else
    if (($_auxhora+$_incremento)>23)
     {
      $_dia=$_dia+1;
      $_operacion=1; 
     }
  $_sumarrestarmes=0;
  while (!checkdate($_mes,$_dia,$_ano))
  {
   if($_operacion==0)
    {
     if ($sumarrestarmes==0)
      {
       $_dia=32;
       $_mes=$_mes-1;
       if ($_mes<1)
        {
         $_mes=12;
         $_ano=$_ano-1;
        }
       $_sumarrestarmes=1;
      }
      $_dia=$_dia-1;
    }
    else
    {
     if ($sumarrestarmes==0)
      {
       $_dia=0;
       $_mes=$_mes+1;
       if ($_mes>12)
        {
         $_mes=1;
         $_ano=$_ano+1;
        }
       $_sumarrestarmes=1;
      }
      $_dia=$_dia+1;
    }
  }
 
}

// Concatenamos hora y fecha para que tengan el formato HH:MM:SS y YYYY-MM-DD, respectivamente.

if($_hora<10)
 $_hora="0".$_hora;
if($_minutos<10)
 $_minutos="0".$_minutos;
if($_segundos<10)
 $_segundos="0".$_segundos;
$_horaactual  = $_hora.":".$_minutos.":".$_segundos;

if($_dia<10)
 $_dia="0".$_dia;
if($_mes<10)
 $_mes="0".$_mes;
$_fechaactual = $_ano."-".$_mes."-".$_dia;

// Determino el nombre del mes, Ej "Enero"
switch ($_mes) 
 {
  case 1:
   $_nombremes='Enero';
   break;
  case 2:
   $_nombremes='Febrero';
   break;
  case 3:
   $_nombremes='Marzo';
   break;
  case 4:
   $_nombremes='Abril';
   break;
  case 5:
   $_nombremes='Mayo';
   break;
  case 6:
   $_nombremes='Junio';
   break;
  case 7:
   $_nombremes='Julio';
   break;
  case 8:
   $_nombremes='Agosto';
   break;
  case 9:
   $_nombremes='Septiembre';
   break;
  case 10:
   $_nombremes='Octubre';
   break;
  case 11:
   $_nombremes='Noviembre';
   break;
  case 12:
   $_nombremes='Diciembre';
   break;
 }


// El programa devuelve las siguientes variables:

//  _hora        : Hora local, en formato HH
//  _minutos     : Minuto, en formato MM
//  _segundos    : Segundos, en formato SS

//  _dia         : Día, en formato DD
//  _mes         : Mes, en formato MM
//  _ano         : Año, en formato YYYY

//  _nombremes   : Nombre del mes. Ej: si _mes = 1, _nombremes = "Enero"

//  _horaactual  : Hora local, en formato HH:MM:SS
//  _fechaactual : Fecha local, en formato YYYY-MM-DD

?>

