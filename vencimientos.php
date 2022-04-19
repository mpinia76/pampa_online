<?
include("config/db.php");

$to 		= 'minervinim@villagedelaspampas.com.ar, minervinin@villagedelaspampas.com.ar';
$mensaje 	= '';
$headers 	= 'From: Pampa Online <no-reply@pampaonline.com>';

$sql = "SELECT nro_orden FROM gasto WHERE fecha_vencimiento = '".date('Y-m-d')."' and estado = 0";
$rsTemp = mysql_query($sql);
while ($rs = mysql_fetch_array($rsTemp)){
	$orden 	= $rs['nro_orden'];
	$asunto = "El gasto con orden $orden vence hoy";
	mail($to,$asunto,$mensaje,$headers);
}

$sql = "SELECT nro_orden FROM compra WHERE fecha_vencimiento = '".date('Y-m-d')."' and estado = 0";
$rsTemp = mysql_query($sql);
while ($rs = mysql_fetch_array($rsTemp)){
	$orden 	= $rs['nro_orden'];
	$asunto = "La compra con orden $orden vence hoy";
	mail($to,$asunto,$mensaje,$headers);
}

$dias 		= 3;
$fecha_time = time() + $dias*24*60*60;
$fecha 		= date('Y-m-d',$fecha_time);

$sql = "SELECT nro_orden FROM gasto WHERE fecha_vencimiento = '$fecha' and estado = 0";
$rsTemp = mysql_query($sql);
while ($rs = mysql_fetch_array($rsTemp)){
	$orden 	= $rs['nro_orden'];
	$asunto = "El gasto con orden $orden vence en $dias dias";
	mail($to,$asunto,$mensaje,$headers);
}

$sql = "SELECT nro_orden FROM compra WHERE fecha_vencimiento = '$fecha' and estado = 0";
$rsTemp = mysql_query($sql);
while ($rs = mysql_fetch_array($rsTemp)){
	$orden 	= $rs['nro_orden'];
	$asunto = "La compra con orden $orden vence en $dias dias";
	mail($to,$asunto,$mensaje,$headers);
}

?>