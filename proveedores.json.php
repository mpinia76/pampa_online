<?php
session_start();

include_once("config/db.php");

$sql = "SELECT id,nombre FROM proveedor ORDER BY nombre";

$rsTemp = mysqli_query($conn,$sql);
$rows = array();
while($rs = mysqli_fetch_array($rsTemp)){
	
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['nombre']
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>