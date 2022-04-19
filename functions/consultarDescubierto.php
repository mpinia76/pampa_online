<?php



include_once("../config/db.php");
include_once("../config/user.php");


$caja_id 	= $_GET['caja_id'];
$monto 	= $_GET['monto'];
$descubierto=1;

$saldo_sql = "SELECT SUM(monto) as saldo FROM caja_movimiento WHERE caja_id=".$caja_id;
$saldo_rs = mysql_fetch_array(mysql_query($saldo_sql));
$saldo = $saldo_rs['saldo']-$monto;		
if ($saldo<0) {
	$sql = "SELECT * FROM caja WHERE id = ".$caja_id;
	$rsTemp1 = mysql_query($sql);
	if($rs1 = mysql_fetch_array($rsTemp1)){
		$descubierto = $rs1['descubierto'];
	}
}
	

if ($descubierto) {



	echo "si";
	
}else{

	echo "no";
	
}

?>