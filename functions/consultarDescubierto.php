<?php



include_once("../config/db.php");
include_once("../config/user.php");


$caja_id 	= $_GET['caja_id'];
$monto 	= $_GET['monto'];
$descubierto=1;

$saldo_sql = "SELECT SUM(monto) as saldo FROM caja_movimiento WHERE caja_id=".$caja_id;
$saldo_rs = mysqli_fetch_array(mysqli_query($conn,$saldo_sql));
$saldo = $saldo_rs['saldo']-$monto;		
if ($saldo<0) {
	$sql = "SELECT * FROM caja WHERE id = ".$caja_id;
	$rsTemp1 = mysqli_query($conn,$sql);
	if($rs1 = mysqli_fetch_array($rsTemp1)){
		$descubierto = $rs1['descubierto'];
	}
}
	

if ($descubierto) {



	echo "si";
	
}else{

	echo "no";
	
}

?>