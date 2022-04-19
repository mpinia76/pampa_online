<?php

$fecha	= $_GET['fecha'];
$fecha_vencimiento	= ($_GET['fecha_vencimiento']!='')?$_GET['fecha_vencimiento']:'01/01/1970';
$result = "";
$result['fecha']='no';

//echo $fecha." - ". $fecha_vencimiento;
include_once("fechasql.php");
if ((is_date($fecha))&&(is_date($fecha_vencimiento))) {
	$result['fecha']='si';
}



	

echo json_encode( $result ); 
?>