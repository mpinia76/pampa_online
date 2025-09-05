<?php



include_once("../config/db.php");
include_once("fechasql.php");

$caja_id 	= $_GET['caja_id'];
$fecha	= fechasql($_GET['fecha']);

$sql = "SELECT MAX(fecha) AS fecha  
FROM caja_sincronizada
WHERE caja_id = ".$caja_id;

$rs = mysqli_fetch_array(mysqli_query($conn,$sql));
$fecha_sincronizacion = $rs['fecha'];

$sql = "SELECT * 
FROM caja
WHERE id = ".$caja_id;

$rs = mysqli_fetch_array(mysqli_query($conn,$sql));
$sincronizacion = $rs['sincronizacion'];
$dias_sincronizacion = $rs['dias_sincronizacion'];
$sincronizada=1;

if ($sincronizacion) {
	$fechaInicial = new DateTime($fecha_sincronizacion);
	$fechaActual = new DateTime($fecha); // la fecha del ordenador

	// Obtenemos la diferencia en milisegundos
	
	
	$interval = $fechaInicial->diff($fechaActual);
	$days = intval($interval->format('%R%a'));
	
	if ($days>$dias_sincronizacion) {
		$sincronizada=0;
	}
	
}


if($sincronizada){

	echo "si";
	
}else{

	echo "no";
	
}

?>