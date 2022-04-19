<?php
session_start();
$user_id = $_SESSION['userid'];

include_once("config/db.php");

if($user_id != 1){
	$sql = "SELECT caja.* FROM caja INNER JOIN usuario_caja ON usuario_caja.caja_id = caja.id WHERE usuario_caja.usuario_id = $user_id";
}else{
	$sql = "SELECT * FROM caja";
}

$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){

	$saldo_sql = "SELECT SUM(monto) as saldo FROM caja_movimiento WHERE caja_id=".$rs['id'];
	$saldo_rs = mysql_fetch_array(mysql_query($saldo_sql));
	
	$saldo = $saldo_rs['saldo'];
	
	$saldo_sql = "SELECT SUM(monto_moneda) as saldo, SUM(monto_moneda*cambio) as saldo_pesos FROM caja_movimiento WHERE caja_id=".$rs['id']." AND moneda_id = 2";
	$saldo_rs = mysql_fetch_array(mysql_query($saldo_sql));
	
	$saldo_usd = $saldo_rs['saldo'];
	$saldo_usd_restar = $saldo_rs['saldo_pesos'];
	
	$saldo_sql = "SELECT SUM(monto_moneda) as saldo, SUM(monto_moneda*cambio) as saldo_pesos  FROM caja_movimiento WHERE caja_id=".$rs['id']." AND moneda_id = 3";
	$saldo_rs = mysql_fetch_array(mysql_query($saldo_sql));
	
	$saldo_euros = $saldo_rs['saldo'];
	$saldo_euros_restar = $saldo_rs['saldo_pesos'];
	
	$saldo_pesos = $saldo - $saldo_usd_restar - $saldo_euros_restar;
	
	$sinc_sql = "SELECT caja_sincronizada.usuario_id,caja_sincronizada.fecha,caja_sincronizada.monto,usuario.nombre, usuario.apellido FROM caja_sincronizada INNER JOIN usuario ON caja_sincronizada.usuario_id = usuario.id WHERE caja_sincronizada.caja_id = ".$rs['id']." ORDER BY caja_sincronizada.fecha desc LIMIT 1";
	$sinc_rs = mysql_fetch_array(mysql_query($sinc_sql));
	$usuario = $sinc_rs['nombre']." ".$sinc_rs['apellido'];
	if($sinc_rs['fecha'] != ''){ $fecha = date("d/m/Y H:i:s",strtotime($sinc_rs['fecha'])); }else{ $fecha = ''; }
	if($sinc_rs['monto'] != ''){ $monto = $sinc_rs['monto']; }else{ $monto = ''; }
	
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['caja'],
			$usuario,
			$fecha,
			round($monto,2),
			round($saldo_euros,2),
			round($saldo_usd,2),
			round($saldo_pesos,2),
			round($saldo,2)
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>
