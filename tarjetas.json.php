<?php
session_start();

include_once("config/db.php");

$sql = "SELECT banco.banco,tarjeta_marca.marca,tarjeta.titular,tarjeta.id,tarjeta.activa FROM tarjeta INNER JOIN tarjeta_marca ON tarjeta.tarjeta_marca_id=tarjeta_marca.id INNER JOIN banco ON tarjeta.banco_id=banco.id WHERE tarjeta.activa = ".$_GET['activo']." ORDER BY banco.banco";

$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){
	
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['banco'],
			$rs['marca'],
			$rs['titular'],
            ($rs['activa'])?'Activa':'Inactiva'
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>