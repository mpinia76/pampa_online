<?php
if(isset($_GET['dataid']) and isset($_GET['delete'])){
	$dataid = $_GET['dataid'];
	include_once("config/db.php");
	if($tabla=='gasto' or $tabla =='compra'){
		$sql = "DELETE FROM $tabla WHERE id=$dataid AND estado=0";
	}else{
		$sql = "DELETE FROM $tabla WHERE id=$dataid";
	}
	mysql_query($sql);
}
?>