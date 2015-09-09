<?
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
	
	$sinc_sql = "SELECT caja_sincronizada.usuario_id,caja_sincronizada.fecha,caja_sincronizada.monto,usuario.nombre, usuario.apellido FROM caja_sincronizada INNER JOIN usuario ON caja_sincronizada.usuario_id = usuario.id WHERE caja_sincronizada.caja_id = ".$rs['id']." ORDER BY caja_sincronizada.fecha DESC LIMIT 1";
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
			round($saldo,2)
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>