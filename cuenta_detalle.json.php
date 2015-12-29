<?php
session_start();

include_once("config/db.php");
include_once("functions/date.php");
include_once("functions/fechasql.php");
$sql = "SELECT * FROM motivo WHERE motivo_grupo_id = 2";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$motivos[$rs['id']] = $rs['nombre'];
}$sql = "SELECT *,DATE(fecha) as fecha2, TIME(TIMESTAMPADD(HOUR,2,fecha)) as hora FROM cuenta_sincronizada WHERE cuenta_id =".$_GET['cuenta_id'];
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$sincronizada[round($rs['monto'],1)]['fecha'] = $rs['fecha2'];	
	$sincronizada[round($rs['monto'],1)]['monto'] = $rs['monto'];	
	$sincronizada[round($rs['monto'],1)]['usuario_id'] = $rs['usuario_id']; 
	$sincronizada[round($rs['monto'],1)]['usuario_id'] = $rs['usuario_id']; 
	$sincronizada[round($rs['monto'],1)]['hora'] = $rs['hora'];
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

//tomamos los adelantos si hay en cajas
$sql = "SELECT e.nombre, e.apellido, ea.id FROM empleado_adelanto ea INNER JOIN rel_pago_operacion rpo ON ea.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_adelanto' INNER JOIN empleado e ON e.id = ea.empleado_id";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$adelanto[$rs['id']] = "Adelanto ".$rs['apellido']." ".$rs['nombre'];
}

//tomamos los pagos de sueldos si hay en cajas
$sql = "SELECT e.nombre, e.apellido, ea.id FROM empleado_pago ea INNER JOIN rel_pago_operacion rpo ON ea.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_pago' INNER JOIN empleado e ON e.id = ea.empleado_id";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$salario[$rs['id']] = "Salario a ".$rs['apellido']." ".$rs['nombre'];
}

//cobro de reservas con cheque
$sql = "SELECT reservas.numero,cc.id as cobro_cheque_id FROM cobro_cheques cc INNER JOIN reserva_cobros rc ON cc.reserva_cobro_id = rc.id INNER JOIN reservas ON reservas.id = rc.reserva_id";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
    $reserva_cobro_cheque[$rs['cobro_cheque_id']] = $rs['numero'];
}

//cobro de reservas con transferencia
$sql = "SELECT reservas.numero,ct.id as cobro_transferencia_id FROM cobro_transferencias ct INNER JOIN reserva_cobros rc ON ct.reserva_cobro_id = rc.id INNER JOIN reservas ON reservas.id = rc.reserva_id";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
    $reserva_cobro_transferencia[$rs['cobro_transferencia_id']] = $rs['numero'];
}

//devoluciones de reserva con transferencia
$sql = "SELECT rd.id, rd.forma_pago, r.numero FROM reserva_devoluciones rd INNER JOIN reservas r ON rd.reserva_id = r.id AND (rd.forma_pago = 'TRANSFERENCIA' OR rd.forma_pago = 'CHEQUE') ";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
    $devoluciones[$rs['id']] = strtolower($rs['forma_pago']).' por Devolucion de Reserva nro: '.$rs['numero'];
}

$sql = "SELECT lote.numero,lote.id,tarjeta.marca,locacion.posnet FROM cobro_tarjeta_lotes lote INNER JOIN cobro_tarjeta_tipos tarjeta ON lote.cobro_tarjeta_tipo_id = tarjeta.id INNER JOIN cobro_tarjeta_posnets locacion ON tarjeta.cobro_tarjeta_posnet_id = locacion.id WHERE lote.fecha_acreditacion != '0000-00-00'";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
    $lotes[$rs['id']] = "Lote ".$rs['numero']." de ".$rs['marca']." ".$rs['posnet'];
}

$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id ORDER BY banco.banco";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$cuentas[$rs['id']] = $rs['banco']." ".$rs['sucursal']." ".$rs['cuenta_tipo']." ".$rs['nombre'];
}

$sql = "SELECT cheque_consumo.numero,cuenta_movimiento.registro_id, rel_pago_operacion.operacion_id, rel_pago_operacion.operacion_tipo, cuenta_movimiento.id, cuenta_movimiento.cuenta_id, cuenta_movimiento.monto, cuenta_movimiento.fecha, cuenta_movimiento.origen, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM rel_pago_operacion RIGHT JOIN cuenta_movimiento ON rel_pago_operacion.forma_pago=cuenta_movimiento.origen AND rel_pago_operacion.forma_pago_id = cuenta_movimiento.registro_id LEFT JOIN cheque_consumo ON cuenta_movimiento.registro_id = cheque_consumo.id AND cuenta_movimiento.origen = 'cheque' LEFT JOIN usuario ON cuenta_movimiento.usuario_id = usuario.id WHERE cuenta_movimiento.cuenta_id = ".$_GET['cuenta_id'];
if ((isset($_GET['desde_mask']))&&($_GET['desde_mask']!='')) {
	$desde = fechasql($_GET['desde_mask']);
	$sql .=" AND cuenta_movimiento.fecha >= '".$desde."'";
}
if ((isset($_GET['hasta_mask']))&&($_GET['hasta_mask']!=''))  {
	$hasta = fechasql($_GET['hasta_mask']);
	$sql .=" AND cuenta_movimiento.fecha <= '".$hasta."'";
}
$sql .=" ORDER BY cuenta_movimiento.fecha DESC";

$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){

	if($rs['operacion_tipo'] == ''){
	
		$detalle = $rs['origen'];
		$orden ='';
		
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
		}elseif($es_cuenta[0] == 'reservatransferencia'){
                                        $detalle = "Transferencia";
                                        $orden ='Res. '.$reserva_cobro_transferencia[$es_cuenta[1]];
		}elseif($es_cuenta[0] == 'reservacheque'){
                                        $detalle = "Cheque";
                                        $orden ='Res. '.$reserva_cobro_transferencia[$es_cuenta[1]];
		}elseif($es_cuenta[0] == 'acreditacionlote'){
                                        $detalle = $lotes[$es_cuenta[1]];
		}
	
	}elseif($rs['operacion_tipo'] == 'sueldo_pago'){
		$detalle = $salario[$rs['operacion_id']];
	}elseif($rs['operacion_tipo'] == 'sueldo_adelanto'){
		$detalle = $adelanto[$rs['operacion_id']];
                 }elseif($rs['operacion_tipo'] == 'reserva_devolucion'){
                     $detalle = $devoluciones[$rs['operacion_id']];
	}else{
	
		$detalle = $rs['operacion_tipo'];
		$operaciones[$rs['operacion_tipo']][$rs['id']] = $rs['operacion_id'];
	}
	
	$rowTemp[$rs['id']]['id'] = $rs['id'];
	$rowTemp[$rs['id']]['detalle'] = $detalle;
	$rowTemp[$rs['id']]['user'] = $rs['user'];
	$rowTemp[$rs['id']]['monto'] = $rs['monto'];
	$rowTemp[$rs['id']]['fecha'] = $rs['fecha'];
	$rowTemp[$rs['id']]['orden'] = $orden;
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
	$total = $total + $datos['monto'];	
}
$i = 1;
foreach($rowTemp as $id=>$datos){
	if($i == 1){
		$balance = $total; 		
		$prev_id = $id;	
	}
	else{
		$balance = $balance - $rowTemp[$prev_id]['monto'];		
		$prev_id = $id;	
	}	
	$i++;	
	if($sincronizada[round($balance,1)]['monto'] != ''){
		$datos['detalle'] = $datos['detalle'].' (sincronizada '.$sincronizada[round($balance,1)]['hora'].')';	
	}	
	$data = array(
		"id" => $id,
		"data" => array(
			$datos['fecha'],
			ucwords($datos['detalle']),
			$datos['orden'],
			ucwords($datos['user']),
			($datos['monto']>=0)?$datos['monto']:'',	
			($datos['monto']<0)?$datos['monto']:'',					
			round($balance,2)
		)
	);
	array_push($rows,$data);		
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>