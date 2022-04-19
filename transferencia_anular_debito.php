<?php
$id 	= $_GET['id'];


include_once("./config/db.php");

$sql	= "SELECT * FROM transferencia_consumo WHERE id=".$id;
$rs		= mysql_fetch_array(mysql_query($sql));

$cuenta_id	= $rs['cuenta_id'];

$registro_id	= $id;

mysql_query("DELETE FROM cuenta_movimiento WHERE cuenta_id = ".$cuenta_id." AND registro_id = ".$registro_id);

$update = "UPDATE transferencia_consumo SET debitado=0, fecha_debitada='0000-00-00'  WHERE id=".$id;
mysql_query($update);



echo "si";
	


?>