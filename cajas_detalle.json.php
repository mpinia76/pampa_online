<?php
session_start();

include_once("config/db.php");
include_once("functions/date.php");
include_once("functions/fechasql.php");
$balance = 0;

$sql = "SELECT * FROM motivo WHERE motivo_grupo_id = 1";
$rsTemp = mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){
	$motivos[$rs['id']] = $rs['nombre'];
}
$sql = "SELECT *,(fecha) as fecha2, TIME(TIMESTAMPADD(HOUR,2,fecha)) as hora FROM caja_sincronizada WHERE caja_id =".$_GET['caja_id'];$rsTemp = mysqli_query($conn,$sql);
//echo $sql;
while($rs = mysqli_fetch_array($rsTemp)){	
    $sincronizada[round($rs['monto'],1)]['fecha'] = $rs['fecha2'];	
    $sincronizada[round($rs['monto'],1)]['monto'] = $rs['monto'];	
    $sincronizada[round($rs['monto'],1)]['usuario_id'] = $rs['usuario_id']; 
    $sincronizada[round($rs['monto'],1)]['hora'] = $rs['hora'];    
}
$sql = "SELECT * FROM caja";
$rsTemp = mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){
	$cajas[$rs['id']] = $rs['caja'];
}

$sql = "SELECT * FROM tarjeta_resumen";
$rsTemp = mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){
    $resumen[$rs['id']] = 'Resumen '.$rs['nombre'];
}

//devoluciones de reserva en efectivo
$sql = "SELECT rd.id, r.numero, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM reserva_devoluciones rd LEFT JOIN usuario ON rd.usuario_id = usuario.id INNER JOIN reservas r ON rd.reserva_id = r.id WHERE rd.forma_pago = 'EFECTIVO' ";
$rsTemp = mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){
    $devoluciones[$rs['id']] = 'Devolucion de Reserva-'.$rs['user'];
}

$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id ORDER BY banco.banco";
$rsTemp = mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){
	$cuentas[$rs['id']] = $rs['banco']." ".$rs['sucursal']." ".$rs['cuenta_tipo']." ".$rs['nombre'];
}

//cobro de reservas
$sql = "SELECT reservas.numero,ce.id as cobro_efectivo_id, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM cobro_efectivos ce INNER JOIN reserva_cobros rc ON ce.reserva_cobro_id = rc.id LEFT JOIN usuario ON rc.usuario_id = usuario.id INNER JOIN reservas ON reservas.id = rc.reserva_id";
$rsTemp = mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){
    $reserva_cobro[$rs['cobro_efectivo_id']] = $rs['numero'].'-'.$rs['user'];
}
//cobro de reservas con cheque
$sql = "SELECT reservas.numero,cc.id as cobro_cheque_id, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM cobro_cheques cc INNER JOIN reserva_cobros rc ON cc.reserva_cobro_id = rc.id INNER JOIN reservas ON reservas.id = rc.reserva_id LEFT JOIN usuario ON rc.usuario_id = usuario.id";
$rsTemp = mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){
    $reserva_cobro_cheque[$rs['cobro_cheque_id']] = $rs['numero'].'-'.$rs['user'];
}
//tomamos los adelantos si hay en cajas
$sql = "SELECT e.nombre, e.apellido, cm.id, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM empleado_adelanto ea LEFT JOIN usuario ON ea.creado_por = usuario.id INNER JOIN rel_pago_operacion rpo ON ea.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_adelanto' INNER JOIN efectivo_consumo ec ON rpo.forma_pago_id = ec.id AND rpo.forma_pago = 'efectivo' INNER JOIN caja_movimiento cm ON cm.registro_id = ec.id AND cm.origen = 'efectivo_consumo' AND cm.caja_id = ".$_GET['caja_id']." INNER JOIN empleado e ON e.id = ea.empleado_id";
$rsTemp = mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){
	$adelanto[$rs['id']] = "Adelanto ".$rs['apellido']." ".$rs['nombre'];
	$adelantoAbonado[$rs['id']]=$rs['user'];
}

//tomamos los pagos de sueldos si hay en cajas
$sql = "SELECT e.nombre, e.apellido, cm.id, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM empleado_pago ea LEFT JOIN usuario ON ea.abonado_por = usuario.id INNER JOIN rel_pago_operacion rpo ON ea.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_pago' INNER JOIN efectivo_consumo ec ON rpo.forma_pago_id = ec.id AND rpo.forma_pago = 'efectivo' INNER JOIN caja_movimiento cm ON cm.registro_id = ec.id AND cm.origen = 'efectivo_consumo' AND cm.caja_id = ".$_GET['caja_id']." INNER JOIN empleado e ON e.id = ea.empleado_id";
$rsTemp = mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){
	$salario[$rs['id']] = "Salario a ".$rs['apellido']." ".$rs['nombre'];
	$salarioAbonado[$rs['id']]=$rs['user'];
}

//Pagos de cuotas de planes de pagos
$sql = "SELECT cuota_plans.id, plans.plan, cuota_plans.vencimiento, CONCAT(usuario.apellido,', ',usuario.nombre) as user 
FROM cuota_plans 
INNER JOIN plans ON cuota_plans.plan_id = plans.id 
LEFT JOIN usuario ON plans.user_id = usuario.id 
INNER JOIN rel_pago_operacion rpo ON cuota_plans.id = rpo.operacion_id AND rpo.operacion_tipo = 'cuota_plan' 
INNER JOIN efectivo_consumo ec ON rpo.forma_pago_id = ec.id AND rpo.forma_pago = 'efectivo' 
INNER JOIN caja_movimiento cm ON cm.registro_id = ec.id AND cm.origen = 'efectivo_consumo' AND cm.caja_id = ".$_GET['caja_id'];
$rsTemp = mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){
	$plan[$rs['id']] = "Cuota Plan ".$rs['plan']." Vencimiento ".$rs['vencimiento'];
	$planAbonado[$rs['id']]=$rs['user'];
}

/*$sql = "SELECT rel_pago_operacion.operacion_id, rel_pago_operacion.operacion_tipo, caja_movimiento.id, caja_movimiento.caja_id, caja_movimiento.monto, caja_movimiento.fecha, caja_movimiento.origen, caja_movimiento.registro_id, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM rel_pago_operacion RIGHT JOIN caja_movimiento ON rel_pago_operacion.forma_pago = 'efectivo' AND rel_pago_operacion.forma_pago_id = caja_movimiento.registro_id LEFT JOIN usuario ON caja_movimiento.usuario_id = usuario.id WHERE caja_id =".$_GET['caja_id'];
if ((isset($_GET['desde_mask']))&&($_GET['desde_mask']!='')) {
	$desde = fechasql($_GET['desde_mask']);
	$sql .=" AND caja_movimiento.fecha >= '".$desde."'";
}
if ((isset($_GET['hasta_mask']))&&($_GET['hasta_mask']!=''))  {
	$hasta = fechasql($_GET['hasta_mask']);
	$sql .=" AND caja_movimiento.fecha <= '".$hasta."'";
}
$sql .=" ORDER BY caja_movimiento.fecha DESC, caja_movimiento.id DESC";*/

$sql ="SELECT T.operacion_id, T.operacion_tipo, T.id, T.caja_id, 
T.monto, T.fecha, T.origen, T.registro_id, 
T.user, T.moneda_id, T.monto_moneda, T.cambio FROM (SELECT rel_pago_operacion.operacion_id, rel_pago_operacion.operacion_tipo, caja_movimiento.id, caja_movimiento.caja_id, 
caja_movimiento.monto, caja_movimiento.fecha, caja_movimiento.origen, caja_movimiento.registro_id, 
CONCAT(usuario.apellido,', ',usuario.nombre) as user, caja_movimiento.moneda_id, caja_movimiento.monto_moneda, caja_movimiento.cambio 
FROM rel_pago_operacion INNER JOIN caja_movimiento ON rel_pago_operacion.forma_pago = 'efectivo' AND rel_pago_operacion.forma_pago_id = caja_movimiento.registro_id 
LEFT JOIN usuario ON caja_movimiento.usuario_id = usuario.id 
WHERE caja_id =".$_GET['caja_id']."
UNION ALL
SELECT null as operacion_id, null as operacion_tipo, caja_movimiento.id, caja_movimiento.caja_id, 
caja_movimiento.monto, caja_movimiento.fecha, caja_movimiento.origen, caja_movimiento.registro_id, CONCAT(usuario.apellido,', ',usuario.nombre) AS user
, caja_movimiento.moneda_id, caja_movimiento.monto_moneda, caja_movimiento.cambio 
FROM caja_movimiento LEFT JOIN usuario ON caja_movimiento.usuario_id = usuario.id
WHERE caja_id =".$_GET['caja_id']." ) T";

if ((isset($_GET['desde_mask']))&&($_GET['desde_mask']!='')) {
	$desde = fechasql($_GET['desde_mask']);
	$sql .=" WHERE T.fecha >= '".$desde."'";
}
if ((isset($_GET['hasta_mask']))&&($_GET['hasta_mask']!=''))  {
	$hasta = fechasql($_GET['hasta_mask']);
	$sql .=" AND T.fecha <= '".$hasta."'";
}


$sql .=" ORDER BY T.fecha DESC, T.id DESC, T.operacion_id ASC";


//echo $sql;
$rsTemp = mysqli_query($conn,$sql);
//print_r($rsTemp);
$rows = array();
while($rs = mysqli_fetch_array($rsTemp)){
	
	$orden = '';
	if($rs['operacion_tipo'] == ''){
		
		
		$detalle = $rs['origen'];
		
                                    $es_caja = explode('_',$detalle);
                                    
                                    if($es_caja[0] == 'caja'){
                                            $detalle = "Transferencia desde ".$cajas[$es_caja[1]];
                                    		if ($rs['moneda_id']==2) {
                                            	$detalle .= '(U$S, TC: '.$rs['cambio'].')';
                                            }
                                    		if ($rs['moneda_id']==3) {
                                            	$detalle .= '(€, TC: '.$rs['cambio'].')';
                                            }
                                    }elseif($es_caja[0] == 'hacia'){
                                            $detalle = "Transferencia hacia ".$cajas[$es_caja[1]];
                                    		if ($rs['moneda_id']==2) {
                                            	$detalle .= '(U$S, TC: '.$rs['cambio'].')';
                                            }
                                    		if ($rs['moneda_id']==3) {
                                            	$detalle .= '(€, TC: '.$rs['cambio'].')';
                                            }
                                    }elseif($es_caja[0] == 'cajacambio'){
                                            $detalle = "Cambio moneda extranjera desde ".$cajas[$es_caja[1]];
                                    		if ($es_caja[2]==2) {
                                            	$detalle .= '(U$S, TC: '.$es_caja[3].')';
                                            }
                                    		if ($es_caja[2]==3) {
                                            	$detalle .= '(€, TC: '.$es_caja[3].')';
                                            }
                                    }elseif($es_caja[0] == 'haciacambio'){
                                            $detalle = "Cambio moneda extranjera hacia ".$cajas[$es_caja[1]];
                                    		if ($rs['moneda_id']==2) {
                                            	$detalle .= '(U$S, TC: '.$rs['cambio'].')';
                                            }
                                    		if ($rs['moneda_id']==3) {
                                            	$detalle .= '(€, TC: '.$rs['cambio'].')';
                                            }
                                    }elseif($es_caja[0] == 'motivo'){
                                            $detalle = $motivos[$es_caja[1]];
                                    }elseif($es_caja[0] == 'haciacuenta'){
                                            $detalle = "Deposito en cuenta ".$cuentas[$es_caja[1]];
                                    }elseif($es_caja[0] == 'desdecuenta'){
                                            $detalle = "Extaccion de cuenta ".$cuentas[$es_caja[1]];
                                    }elseif($es_caja[0] == 'tarjetaresumen'){			
                                            $detalle = $resumen[$es_caja[1]];
                                    }elseif($es_caja[0] == 'reservacobro'){
                                            $detalle = 'Cobro de Reserva';
                                            if ($rs['moneda_id']==2) {
                                            	$detalle .= '(U$S, TC: '.$rs['cambio'].')';
                                            }
                                    		if ($rs['moneda_id']==3) {
                                            	$detalle .= '(€, TC: '.$rs['cambio'].')';
                                            }
                                            $arrayDevolucion = explode('-', $reserva_cobro[$es_caja[1]]);
								           
								            $orden = 'Nro: '.$arrayDevolucion[0];
								            $rs['user'] = $arrayDevolucion[1];
                                    }elseif($es_caja[0] == 'reservacheque'){
                                            $detalle = 'Cobro cheque de 3ros';
                                            
                                            $arrayDevolucion = explode('-', $reserva_cobro_cheque[$es_caja[1]]);
								           
								            $orden = 'Res. '.$arrayDevolucion[0];
								            $rs['user'] = $arrayDevolucion[1];
                                    }
		
	}else{
		$usuario=$rs['user'];
		$detalle = $rs['operacion_tipo'];				
		if($detalle == 'tarjeta_resumen'){					
			$detalle = $resumen[$rs['operacion_id']];				
		}elseif($detalle == 'sueldo_adelanto'){
			$detalle = $adelanto[$rs['id']];
			$rs['user'] = $adelantoAbonado[$rs['id']];
		}elseif($detalle == 'cuota_plan'){
			$detalle = $plan[$rs['operacion_id']];
			$rs['user'] = $planAbonado[$rs['operacion_id']];
		}elseif($detalle == 'sueldo_pago'){
			$detalle = $salario[$rs['id']];
			$rs['user'] = $salarioAbonado[$rs['id']];
		}elseif($detalle == 'reserva_devolucion'){
			$arrayDevolucion = explode('-', $devoluciones[$rs['operacion_id']]);
            $detalle = $arrayDevolucion[0];
            $rs['user'] = $arrayDevolucion[1];
                                    }
                                   
		$operaciones[$rs['operacion_tipo']][$rs['id']] = $rs['operacion_id'];
		if ($usuario) {
			$operaciones[$rs['operacion_tipo'].'_usuarios'][$rs['operacion_id']] = $usuario;
		}
		
	}
	
	$monto_usd = ($rs['moneda_id']==2)?$rs['monto_moneda']:0;
	$monto_usd_restar = ($rs['moneda_id']==2)?$rs['monto_moneda']*$rs['cambio']:0;
	$monto_euros = ($rs['moneda_id']==3)?$rs['monto_moneda']:0;
	$monto_euros_restar = ($rs['moneda_id']==3)?$rs['monto_moneda']*$rs['cambio']:0;
	//$monto_pesos = $rs['monto']-$monto_usd_restar-$monto_euros_restar;
	
	$rowTemp[$rs['id']]['id'] = $rs['id'];
	$rowTemp[$rs['id']]['detalle'] = $detalle;
	$rowTemp[$rs['id']]['user'] = $rs['user'];
	$rowTemp[$rs['id']]['monto'] = $rs['monto'];
	$rowTemp[$rs['id']]['monto_pesos'] = $rs['monto'];
	$rowTemp[$rs['id']]['monto_euros'] = $monto_euros;
	$rowTemp[$rs['id']]['monto_usd'] = $monto_usd;
	$rowTemp[$rs['id']]['fecha'] = $rs['fecha'];
	$rowTemp[$rs['id']]['orden'] = $orden;
	
	
}

//agrego detalle de los gastos
$gastos = $operaciones['gasto'];
//print_r($gastos);
if(is_array($gastos) and count($gastos)>0){
	$sql_gastos = "SELECT gasto.id,gasto.nro_orden, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM gasto LEFT JOIN usuario ON gasto.user_id = usuario.id WHERE gasto.id IN (".implode(",",$gastos).")";
	//echo $sql_gastos;
	$rsGastosTemp = mysqli_query($conn,$sql_gastos);
	while($rsGastos = mysqli_fetch_array($rsGastosTemp)){
		
		$operacion['gasto_'.$rsGastos['id']] = $rsGastos['nro_orden'];
		if ($operaciones['gasto_usuarios'][$rsGastos['id']]) {
			$rsGastos['user']=$operaciones['gasto_usuarios'][$rsGastos['id']];
		}
		$operacion['gasto_user_'.$rsGastos['id']] = $rsGastos['user'];
	
	} 
	foreach($gastos as $consumo_id=>$gasto_id){
		$rowTemp[$consumo_id]['orden'] = $operacion['gasto_'.$gasto_id];
		$rowTemp[$consumo_id]['user'] = $operacion['gasto_user_'.$gasto_id];
	}
} //si gastos es array y mayor a cero

//agrego detalle de los gastos
$compras = $operaciones['compra'];

if(is_array($compras) and count($compras)>0){
	$sql_compras = "SELECT compra.id,compra.nro_orden, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM compra LEFT JOIN usuario ON compra.user_id = usuario.id WHERE compra.id IN (".implode(",",$compras).")";
	$rsComprasTemp = mysqli_query($conn,$sql_compras);
	while($rsCompras = mysqli_fetch_array($rsComprasTemp)){
		
		$operacion['compra_'.$rsCompras['id']] = $rsCompras['nro_orden'];
		if ($operaciones['compra_usuarios'][$rsCompras['id']]) {
			$rsCompras['user']=$operaciones['compra_usuarios'][$rsCompras['id']];
		}
		$operacion['compra_user_'.$rsCompras['id']] = $rsCompras['user'];
	
	} 
	foreach($compras as $consumo_id=>$compra_id){
		$rowTemp[$consumo_id]['orden'] = $operacion['compra_'.$compra_id];
		$rowTemp[$consumo_id]['user'] = $operacion['compra_user_'.$compra_id];
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
	//print_r($sincronizada);
	if($sincronizada[round($balance,1)]['monto'] != ''){
		$datos['detalle'] = $datos['detalle'].' (sincronizada '.$sincronizada[round($balance,1)]['fecha'].')';	
	}	
	$data = array(
		"id" => $id,
		"data" => array(
			$datos['fecha'],
			ucwords($datos['detalle']),
			$datos['orden'],
			ucwords($datos['user']),
			round($datos['monto_euros'],2),
			round($datos['monto_usd'],2),
			($datos['monto']>=0)?round($datos['monto_pesos'],2):'',	
			($datos['monto']<0)?round($datos['monto'],2):'',					
			round($balance,2)
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>