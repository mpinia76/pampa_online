<?php
$monto 	= $_GET['monto'];
$tabla	= $_GET['tabla'];
$proveedor	= $_GET['proveedor'];
$factura_nro	= $_GET['factura_nro'];
$result = "";
include_once("../config/db.php");

$sql = "SELECT p.nombre as proveedor, c.id as id, c.fecha FROM $tabla c LEFT JOIN proveedor p ON c.proveedor = p.id WHERE c.proveedor='".$proveedor."' AND c.monto ='".$monto."'";
	$rs = mysqli_fetch_array(mysqli_query($conn,$sql));
	//echo $sql;
	if ($rs['id']) {
		$result['siMonto']='si';
		$result['fecha']=$rs['fecha'];
		$result['proveedor']=$rs['proveedor'];
		
		
	}
	else{
		$result['siMonto']='no';
		$sql = "SELECT p.nombre as proveedor, c.id as id, c.fecha, c.factura_tipo, case c.factura_orden  when 'B' then '0001' ELSE '0002' end as factura_orden, c.factura_nro FROM $tabla c LEFT JOIN proveedor p ON c.proveedor = p.id WHERE c.proveedor='".$proveedor."' AND c.factura_nro LIKE '%".$factura_nro."%'";
		$rs = mysqli_fetch_array(mysqli_query($conn,$sql));
		if ($rs['id']) {
			$result['siFactura']='si';
			$result['fecha']=$rs['fecha'];
			$result['proveedor']=$rs['proveedor'];
			$result['factura_tipo']=$rs['factura_tipo'];
			$result['factura_orden']=$rs['factura_orden'];
			$result['factura_nro']=$rs['factura_nro'];
			
			
		}
		else{
			$result['siFactura']='no';
		}
	}

	

echo json_encode( $result ); 
?>