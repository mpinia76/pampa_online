<?php
session_start();

include_once("config/db.php");
$balance = 0;


$sql = "SELECT *,DATE(fecha) as fecha2, TIME(TIMESTAMPADD(HOUR,2,fecha)) as hora FROM caja_sincronizada WHERE caja_id =".$_GET['caja_id'];
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){	
    $sincronizada[round($rs['monto'],1)]['fecha'] = $rs['fecha2'];	
    $sincronizada[round($rs['monto'],1)]['monto'] = $rs['monto'];	
    $sincronizada[round($rs['monto'],1)]['usuario_id'] = $rs['usuario_id']; 
    $sincronizada[round($rs['monto'],1)]['hora'] = $rs['hora'];    
}


$sql = "SELECT * FROM tarjeta_resumen";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
    $resumen[$rs['id']] = 'Resumen '.$rs['nombre'];
}

//devoluciones de reserva en efectivo
$sql = "SELECT rd.id, r.numero FROM reserva_devoluciones rd INNER JOIN reservas r ON rd.reserva_id = r.id AND rd.forma_pago = 'EFECTIVO' ";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
    $devoluciones[$rs['id']] = 'Devolucion de Reserva nro: '.$rs['numero'];
}

$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id ORDER BY banco.banco";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$cuentas[$rs['id']] = $rs['banco']." ".$rs['sucursal']." ".$rs['cuenta_tipo']." ".$rs['nombre'];
}

//cobro de reservas
$sql = "SELECT reservas.numero,ce.id as cobro_efectivo_id FROM cobro_efectivos ce INNER JOIN reserva_cobros rc ON ce.reserva_cobro_id = rc.id INNER JOIN reservas ON reservas.id = rc.reserva_id";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
    $reserva_cobro[$rs['cobro_efectivo_id']] = $rs['numero'];
}
//tomamos los adelantos si hay en cajas
$sql = "SELECT e.nombre, e.apellido, cm.id FROM empleado_adelanto ea INNER JOIN rel_pago_operacion rpo ON ea.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_adelanto' INNER JOIN efectivo_consumo ec ON rpo.forma_pago_id = ec.id AND rpo.forma_pago = 'efectivo' INNER JOIN caja_movimiento cm ON cm.registro_id = ec.id AND cm.origen = 'efectivo_consumo' AND cm.caja_id = ".$_GET['caja_id']." INNER JOIN empleado e ON e.id = ea.empleado_id";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$adelanto[$rs['id']] = "Adelanto ".$rs['apellido']." ".$rs['nombre'];
}

//tomamos los pagos de sueldos si hay en cajas
$sql = "SELECT e.nombre, e.apellido, cm.id FROM empleado_pago ea INNER JOIN rel_pago_operacion rpo ON ea.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_pago' INNER JOIN efectivo_consumo ec ON rpo.forma_pago_id = ec.id AND rpo.forma_pago = 'efectivo' INNER JOIN caja_movimiento cm ON cm.registro_id = ec.id AND cm.origen = 'efectivo_consumo' AND cm.caja_id = ".$_GET['caja_id']." INNER JOIN empleado e ON e.id = ea.empleado_id";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$salario[$rs['id']] = "Salario a ".$rs['apellido']." ".$rs['nombre'];
}

$sql = "SELECT rel_pago_operacion.operacion_id, rel_pago_operacion.operacion_tipo, caja_movimiento.id, caja_movimiento.caja_id, caja_movimiento.monto, caja_movimiento.fecha, caja_movimiento.origen, caja_movimiento.registro_id FROM rel_pago_operacion RIGHT JOIN caja_movimiento ON rel_pago_operacion.forma_pago = 'efectivo' AND rel_pago_operacion.forma_pago_id = caja_movimiento.registro_id WHERE caja_id =".$_GET['caja_id']." ORDER BY fecha DESC";

$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){

	if($rs['operacion_tipo'] == ''){
	
		$detalle = $rs['origen'];
                                    $es_caja = explode('_',$detalle);
                                    if(($es_caja[0] == 'caja')||($es_caja[0] == 'hacia')){
                                    	$sqlCaja = "SELECT * FROM caja WHERE id = ".$es_caja[1];
										$rsCaja = mysql_query($sqlCaja);
                                    	if($rsCaja = mysql_fetch_array($rsCaja)){
												$detalle = ($es_caja[0] == 'caja')?"Transferencia desde ".$rsCaja['caja']:"Transferencia hacia ".$rsCaja['caja'];
											}
                                            
                                    }elseif($es_caja[0] == 'motivo'){
                                            $sqlMotivo = "SELECT nombre FROM motivo WHERE motivo_grupo_id = 1 AND id = ".$es_caja[1];
											$rsMotivo = mysql_query($sqlMotivo);
											if($rsMotivo = mysql_fetch_array($rsMotivo)){
												$detalle = $rsMotivo['nombre'];
											}
		                                            //$detalle = $motivos[$es_caja[1]];
                                    }elseif($es_caja[0] == 'haciacuenta'){
                                            $detalle = "Deposito en cuenta ".$cuentas[$es_caja[1]];
                                    }elseif($es_caja[0] == 'desdecuenta'){
                                            $detalle = "Extaccion de cuenta ".$cuentas[$es_caja[1]];
                                    }elseif($es_caja[0] == 'tarjetaresumen'){			
                                            $detalle = $resumen[$es_caja[1]];
                                    }elseif($es_caja[0] == 'reservacobro'){
                                            $detalle = 'Cobro de Reserva nro: '.$reserva_cobro[$es_caja[1]];
                                    }
		
	}else{
	
		$detalle = $rs['operacion_tipo'];				
		if($detalle == 'tarjeta_resumen'){					
			$detalle = $resumen[$rs['operacion_id']];				
		}elseif($detalle == 'sueldo_adelanto'){
			$detalle = $adelanto[$rs['id']];
		}elseif($detalle == 'sueldo_pago'){
			$detalle = $salario[$rs['id']];
		}elseif($detalle == 'reserva_devolucion'){
                                                    $detalle = $devoluciones[$rs['operacion_id']];
                                    }
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
			$datos['monto'],						
			round($balance,2)
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>