<?php
session_start();

include_once("config/db.php");

$sql = "SELECT rubro.rubro,subrubro.id,subrubro.subrubro, subrubro.activo FROM subrubro INNER JOIN rubro ON subrubro.rubro_id=rubro.id ORDER BY rubro.rubro";

$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){

	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['rubro'],
			$rs['subrubro'],
			($rs['activo'])?'SI':'NO'
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>