<?php
$monto 	= $_GET['monto'];
$tabla	= $_GET['tabla'];
$fecha	= $_GET['fecha'];
$factura_nro	= $_GET['factura_nro'];
$result = "";
include_once("../config/db.php");
include_once("fechasql.php");
$sql = "SELECT CASE WHEN p.nombre is NULL THEN c.proveedor ELSE p.nombre END as proveedor, c.id as id, c.fecha, c.factura_tipo, case c.factura_orden  when 'B' then '0001' ELSE '0002' end as factura_orden, c.factura_nro FROM $tabla c LEFT JOIN proveedor p ON c.proveedor = p.id WHERE c.fecha='".fechasql($fecha)."' AND c.monto LIKE'".$monto."'";
	$rs = mysql_fetch_array(mysql_query($sql));
	//echo $sql;
	if ($rs['id']) {
		$result['siMonto']='si';
		$result['fecha']=$rs['fecha'];
		$result['proveedor']=$rs['proveedor'];
		$result['factura_tipo']=$rs['factura_tipo'];
		$result['factura_orden']=$rs['factura_orden'];
		$result['factura_nro']=$rs['factura_nro'];
		
	}
	else{
		$result['siMonto']='no';
		//$sql = "SELECT p.nombre as proveedor, c.id as id, c.fecha, c.factura_tipo, case c.factura_orden  when 'B' then '0001' ELSE '0002' end as factura_orden, c.factura_nro FROM $tabla c LEFT JOIN proveedor p ON c.proveedor = p.id WHERE c.fecha='".fechasql($fecha)."' AND c.factura_nro LIKE '%".$factura_nro."%'";
		if ($factura_nro!="") {
			$sql = "SELECT CASE WHEN p.nombre is NULL THEN c.proveedor ELSE p.nombre END as proveedor, c.id as id, c.fecha, c.factura_tipo, case c.factura_orden  when 'B' then '0001' ELSE '0002' end as factura_orden, c.factura_nro FROM $tabla c LEFT JOIN proveedor p ON c.proveedor = p.id WHERE c.factura_nro LIKE '%".$factura_nro."%'";
			$rs = mysql_fetch_array(mysql_query($sql));
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
		else{
			$result['siFactura']='no';
		}
	}

	

echo json_encode( $result ); 
?>