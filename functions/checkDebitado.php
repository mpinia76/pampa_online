<?php
$id 	= $_GET['id'];
$tabla	= $_GET['tabla'];

include_once("../config/db.php");

$sql = "SELECT debitado FROM $tabla WHERE id = $id";

$rs = mysqli_fetch_array(mysqli_query($conn,$sql));

if($rs['debitado']){

	echo "si";
	
}else{

	echo "no";
	
}

?>