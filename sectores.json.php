<?php
session_start();

include_once("config/db.php");

$sql = "SELECT s.*,she.valor FROM sector s LEFT JOIN sector_horas_extras she ON s.hora_extra_activa = she.id ORDER BY sector ASC";

$rsTemp = mysqli_query($conn,$sql);
$rows = array();
while($rs = mysqli_fetch_array($rsTemp)){
	
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['sector'],
			$rs['valor']
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>