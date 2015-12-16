<?php
session_start();

include_once("config/db.php");
$balance = 0;
$sql = "SELECT * FROM motivo WHERE motivo_grupo_id = 2";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$motivos[$rs['id']] = $rs['nombre'];
}

$sql = "SELECT * FROM tarjeta_resumen";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$resumen[$rs['id']] = 'Resumen '.$rs['nombre'];
}

$sql = "SELECT * FROM caja";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$cajas[$rs['id']] = $rs['caja'];
}

$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id ORDER BY banco.banco";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$cuentas[$rs['id']] = $rs['banco']." ".$rs['sucursal']." ".$rs['cuenta_tipo']." ".$rs['nombre'];
}

$sql = "SELECT cheque_consumo.numero,cuenta_movimiento.registro_id, rel_pago_operacion.operacion_id, rel_pago_operacion.operacion_tipo, cuenta_movimiento.id, cuenta_movimiento.cuenta_id, cuenta_movimiento.monto, cuenta_movimiento.fecha, cuenta_movimiento.origen FROM rel_pago_operacion RIGHT JOIN cuenta_movimiento ON rel_pago_operacion.forma_pago=cuenta_movimiento.origen AND rel_pago_operacion.forma_pago_id = cuenta_movimiento.registro_id LEFT JOIN cheque_consumo ON cuenta_movimiento.registro_id = cheque_consumo.id AND cuenta_movimiento.origen = 'cheque' WHERE cuenta_movimiento.cuenta_id = ".$_GET['cuenta_id']." ORDER BY fecha DESC";

$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){

	if($rs['operacion_tipo'] == ''){
	
		$detalle = $rs['origen'];
		
		if($detalle == 'cheque'){
			$detalle = 'cheque ('.$rs['numero'].')';
		}
		
		$es_cuenta = explode('_',$detalle);
		
		if($es_cuenta[0] == 'cuenta'){
			$detalle = "Transferencia desde ".$cuentas[$es_cuenta[1]];
		}elseif($es_cuenta[0] == 'hacia'){
			$detalle = "Transferencia hacia ".$cuentas[$es_cuenta[1]];
		}elseif($es_cuenta[0] == 'motivo'){
			$detalle = $motivos[$es_cuenta[1]];
		}elseif($es_cuenta[0] == 'tarjetaresumen'){
			$detalle = $resumen[$es_cuenta[1]];
		}elseif($es_cuenta[0] == 'haciacaja'){
			$detalle = "Extraccion hacia caja ".$cajas[$es_cuenta[1]];
		}elseif($es_cuenta[0] == 'desdecaja'){
			$detalle = "Deposito desde caja ".$cajas[$es_cuenta[1]];
		}
	
	}else{
	
		$detalle = $rs['operacion_tipo'];
		$operaciones[$rs['operacion_tipo']][$rs['id']] = $rs['operacion_id'];
	}
	
	$rowTemp[$rs['id']]['id'] = $rs['id'];
	$rowTemp[$rs['id']]['detalle'] = $detalle;
	$rowTemp[$rs['id']]['monto'] = $rs['monto'];
	$rowTemp[$rs['id']]['fecha'] = $rs['fecha'];
	$rowTemp[$rs['id']]['orden'] = '';
}

//agrego detalle de los gastos
$gastos = $operaciones['gasto'];
if(is_array($gastos) and count($gastos)>0){
	$sql_gastos = "SELECT id,nro_orden FROM gasto WHERE id IN (".implode(",",$gastos).")";
	$rsGastosTemp = mysql_query($sql_gastos);
	while($rsGastos = mysql_fetch_array($rsGastosTemp)){
		
		$operacion['gasto_'.$rsGastos['id']] = $rsGastos['nro_orden'];
	
	} 
	foreach($gastos as $consumo_id=>$gasto_id){
		$rowTemp[$consumo_id]['orden'] = $operacion['gasto_'.$gasto_id];
	}
} //si gastos es array y mayor a cero

//agrego detalle de los gastos
$compras = $operaciones['compra'];
if(is_array($compras) and count($compras)>0){
	$sql_compras = "SELECT id,nro_orden FROM compra WHERE id IN (".implode(",",$compras).")";
	$rsComprasTemp = mysql_query($sql_compras);
	while($rsCompras = mysql_fetch_array($rsComprasTemp)){
		
		$operacion['compra_'.$rsCompras['id']] = $rsCompras['nro_orden'];
	
	} 
	foreach($compras as $consumo_id=>$compra_id){
		$rowTemp[$consumo_id]['orden'] = $operacion['compra_'.$compra_id];
	}
} //si compras es array y mayor a cero

foreach($rowTemp as $id=>$datos){
	$data = array(
		"id" => $id,
		"data" => array(
			$datos['fecha'],
			ucwords($datos['detalle']),
			$datos['orden'],
			$datos['monto'],						$balance
		)
	);
	array_push($rows,$data);		$balance = $balance - $datos['monto'];
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>