<?php
session_start();

include_once("config/db.php");

$sql = "SELECT * FROM rubro";

$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){

	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['rubro'],
			($rs['gastos'])?'SI':'NO',
			($rs['impuestos'])?'SI':'NO',
			($rs['activo'])?'SI':'NO'
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>