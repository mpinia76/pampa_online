<?php
$monto 	= $_GET['monto'];
$tabla	= $_GET['tabla'];

include_once("../config/db.php");

$sql = "SELECT count(*) as cantidad FROM $tabla WHERE monto like '$monto'";

$rs = mysqli_fetch_array(mysqli_query($conn,$sql));

if($rs['cantidad'] > 0){

	echo "si";
	
}else{

	echo "no";
	
}

?>