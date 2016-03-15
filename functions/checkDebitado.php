<?php
$id 	= $_GET['id'];
$tabla	= $_GET['tabla'];

include_once("../config/db.php");

$sql = "SELECT debitado FROM $tabla WHERE id = $id";

$rs = mysql_fetch_array(mysql_query($sql));

if($rs['debitado']){

	echo "si";
	
}else{

	echo "no";
	
}

?>