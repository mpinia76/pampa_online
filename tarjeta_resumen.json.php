<?php
session_start();

include_once("config/db.php");

$sql = "SELECT tarjeta_resumen.*, CONCAT(banco.banco,' ',tarjeta_marca.marca,' ',tarjeta.titular) AS tarjeta FROM tarjeta INNER JOIN tarjeta_marca ON tarjeta.tarjeta_marca_id=tarjeta_marca.id INNER JOIN banco ON tarjeta.banco_id=banco.id INNER JOIN tarjeta_resumen ON tarjeta_resumen.tarjeta_id=tarjeta.id ORDER BY tarjeta_resumen.vencimiento DESC";

$rsTemp = mysql_query($sql);

$rows = array();
while($rs = mysql_fetch_array($rsTemp)){
	
	$time = time();
	$vence = strtotime($rs['vencimiento']);
	
	if($time >= $vence and $rs['estado'] == 0){
		$estado = 'Vencido';
	}elseif($time >= $vence and $rs['estado'] == 1){
		$estado = 'Pagado';
	}elseif($rs['estado'] == 1){
		$estado = 'Pagado';
	}else{
		$estado = 'Abierto';
	}
	
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['nombre'],
			$rs['tarjeta'],
			date("d/m/Y",strtotime($rs['vencimiento'])),
			$estado
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>
