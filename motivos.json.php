<?php
session_start();

include_once("config/db.php");

$sql = "SELECT motivo.*,motivo_grupo.grupo FROM motivo INNER JOIN motivo_grupo ON motivo.motivo_grupo_id = motivo_grupo.id ORDER BY motivo_grupo_id";

$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){
	
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['grupo'],
			$rs['nombre']
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>