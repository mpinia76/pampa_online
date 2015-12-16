<?php
session_start();
$user_id = $_SESSION['userid'];

include_once("config/db.php");

if($user_id != 1){
	$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN usuario_cuenta ON usuario_cuenta.cuenta_id = cuenta.id WHERE usuario_cuenta.usuario_id = $user_id ORDER BY banco.banco";
}else{
	$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id ORDER BY banco.banco";
}

$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){

	$saldo_sql = "SELECT SUM(monto) as saldo FROM cuenta_movimiento WHERE cuenta_id=".$rs['id'];
	$saldo_rs = mysql_fetch_array(mysql_query($saldo_sql));
	
	$saldo = round($saldo_rs['saldo'],2);
	
	$sinc_sql = "SELECT cuenta_sincronizada.usuario_id,cuenta_sincronizada.fecha,cuenta_sincronizada.monto,usuario.nombre, usuario.apellido FROM cuenta_sincronizada INNER JOIN usuario ON cuenta_sincronizada.usuario_id = usuario.id WHERE cuenta_sincronizada.cuenta_id = ".$rs['id']." ORDER BY cuenta_sincronizada.fecha DESC LIMIT 1";
	$sinc_rs = mysql_fetch_array(mysql_query($sinc_sql));
	$usuario = $sinc_rs['nombre']." ".$sinc_rs['apellido'];
	if($sinc_rs['fecha'] != ''){ $fecha = date("d/m/Y H:i:s",strtotime($sinc_rs['fecha'])); }else{ $fecha = ''; }
	if($sinc_rs['monto'] != ''){ $monto = $sinc_rs['monto']; }else{ $monto = ''; }
	
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['banco'].' ('.$rs['sucursal'].') '.$rs['nombre'],
			$usuario,
			$fecha,
			round($monto,2),
			round($saldo,2)
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>