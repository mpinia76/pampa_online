<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');


include_once("../config/db.php");
include_once("../config/user.php");
include_once("fechasql.php");

$caja_id 	= $_GET['caja_id'];
$fecha	= fechasql($_GET['fecha']);

$time = time();

$hora = date("H:i:s", $time);

$fecha .=' '.$hora;

$sql = "SELECT MAX(fecha) AS fecha  
FROM caja_sincronizada
WHERE caja_id = ".$caja_id;

$rs = mysqli_fetch_array(mysqli_query($conn,$sql));
$fecha_sincronizacion = $rs['fecha'];
$fechaPosterior=1;

//echo $fecha_sincronizacion." >= ".$fecha;
if ($fecha_sincronizacion>=$fecha) {
	$fechaPosterior=0;
}
if(ACCION_134){
	$fechaPosterior=1;
}

if($fechaPosterior){

	echo "si";
	
}else{

	echo "no";
	
}

?>