<?php
//FUNCIONES DE FECHAS

//Agrega a una fecha anos,meses,dias,horas,minutos o segundos y la devuelve en el formato deseado
function dateAdd($date, $formato, $dd=0, $mm=0, $yy=0, $hh=0, $mn=0, $ss=0){
	$date_r = getdate(strtotime($date));
	$date_result = date($formato, mktime(($date_r["hours"]+$hh),($date_r["minutes"]+$mn),($date_r["seconds"]+$ss),($date_r["mon"]+$mm),($date_r["mday"]+$dd),($date_r["year"]+$yy)));
	return $date_result;
}

//muestra el nombre del mes
function mes($mes){
	switch($mes){
		case 1:
		return "Enero";
		break;
		
		case 2:
		return "Febrero";
		break;
		
		case 3:
		return "Marzo";
		break;
		
		case 4:
		return "Abril";
		break;
		
		case 5:
		return "Mayo";
		break;
		
		case 6:
		return "Junio";
		break;
		
		case 7:
		return "Julio";
		break;
		
		case 8:
		return "Agosto";
		break;
		
		case 9:
		return "Septiembre";
		break;
		
		case 10:
		return "Octubre";
		break;
		
		case 11:
		return "Noviembre";
		break;
		
		case 12:
		return "Diciembre";
		break;
		
	}
}
?>