<?php

$id	= $_GET['id'];
$result['razon']='';
$result['cuit']='';
include_once("../config/db.php");

$sql = "SELECT p.* FROM proveedor p  WHERE p.id='".$id."'";
	$rs = mysqli_fetch_array(mysqli_query($conn,$sql));
	//echo $sql;
	if ($rs['id']) {
		$result['razon']=$rs['razon'];
		$result['cuit']=$rs['cuit'];
	}


	

echo json_encode( $result ); 
?>