<?php
session_start();

include_once("config/db.php");

//$sql = "SELECT * FROM usuario WHERE admin!=1 AND id!=".$_SESSION['userid'];
$sql = "SELECT * FROM documento";

$rsTemp = mysqli_query($conn,$sql);
$rows = array();
while($rs = mysqli_fetch_array($rsTemp)){
	$path = "<a href=\"documentos/".$rs['path']."\" target=\"_blank\">".$rs['path']."</a>" ;
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$path
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>
