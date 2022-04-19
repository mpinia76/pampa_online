<?php
$monto 	= $_GET['monto'];
$tabla	= $_GET['tabla'];

include_once("../config/db.php");

$sql = "SELECT count(*) as cantidad FROM $tabla WHERE monto like '$monto'";

$rs = mysql_fetch_array(mysql_query($sql));

if($rs['cantidad'] > 0){

	echo "si";
	
}else{

	echo "no";
	
}

?>