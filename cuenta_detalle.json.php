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
$sql = "SELECT e.nombre, e.apellido, ea.id, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM empleado_adelanto ea LEFT JOIN usuario ON ea.creado_por = usuario.id INNER JOIN rel_pago_operacion rpo ON ea.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_adelanto' INNER JOIN empleado e ON e.id = ea.empleado_id";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$adelanto[$rs['id']] = "Adelanto ".$rs['apellido']." ".$rs['nombre'];
	$adelantoAbonado[$rs['id']]=$rs['user'];
}

//tomamos los pagos de sueldos si hay en cajas
$sql = "SELECT e.nombre, e.apellido, ea.id, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM empleado_pago ea LEFT JOIN usuario ON ea.abonado_por = usuario.id INNER JOIN rel_pago_operacion rpo ON ea.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_pago' INNER JOIN empleado e ON e.id = ea.empleado_id";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$salario[$rs['id']] = "Salario a ".$rs['apellido']." ".$rs['nombre'];
	$salarioAbonado[$rs['id']]=$rs['user'];
}

//cobro de reservas con cheque
$sql = "SELECT reservas.numero,cc.id as cobro_cheque_id, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM cobro_cheques cc INNER JOIN reserva_cobros rc ON cc.reserva_cobro_id = rc.id INNER JOIN reservas ON reservas.id = rc.reserva_id LEFT JOIN usuario ON rc.usuario_id = usuario.id";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
    $reserva_cobro_cheque[$rs['cobro_cheque_id']] = $rs['numero'].'-'.$rs['user'];
}

//cobro de reservas con transferencia
$sql = "SELECT reservas.numero,ct.id as cobro_transferencia_id, CONCAT(usuario.apellido,', ',usuario.nombre) as user, ct.quien_transfiere FROM cobro_transferencias ct INNER JOIN reserva_cobros rc ON ct.reserva_cobro_id = rc.id INNER JOIN reservas ON reservas.id = rc.reserva_id LEFT JOIN usuario ON ct.acreditado_por = usuario.id";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
    $reserva_cobro_transferencia[$rs['cobro_transferencia_id']] = $rs['numero'].'-'.$rs['user'].'-'.$rs['quien_transfiere'];
}

//devoluciones de reserva con transferencia
$sql = "SELECT rd.id, rd.forma_pago, r.numero, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM reserva_devoluciones rd LEFT JOIN usuario ON rd.usuario_id = usuario.id INNER JOIN reservas r ON rd.reserva_id = r.id AND (rd.forma_pago = 'TRANSFERENCIA' OR rd.forma_pago = 'CHEQUE') ";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
    $devoluciones[$rs['id']] = strtolower($rs['forma_pago']).' por Devolucion de Reserva nro: '.$rs['numero'];
    $devolucionesUser[$rs['id']] = $rs['user'];
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





$sql = "SELECT cheque_consumo.numero,cuenta_movimiento.registro_id, rel_pago_operacion.operacion_id, rel_pago_operacion.operacion_tipo, cuenta_movimiento.id, cuenta_movimiento.cuenta_id, cuenta_movimiento.monto, cuenta_movimiento.fecha, cuenta_movimiento.origen, CONCAT(usuario.apellido,', ',usuario.nombre) as user, CONCAT(usuario1.apellido,', ',usuario1.nombre) as usercheque FROM rel_pago_operacion RIGHT JOIN cuenta_movimiento ON rel_pago_operacion.forma_pago=cuenta_movimiento.origen AND rel_pago_operacion.forma_pago_id = cuenta_movimiento.registro_id LEFT JOIN cheque_consumo ON cuenta_movimiento.registro_id = cheque_consumo.id AND cuenta_movimiento.origen = 'cheque' LEFT JOIN usuario ON cuenta_movimiento.usuario_id = usuario.id LEFT JOIN usuario AS usuario1 ON cheque_consumo.debitado_por = usuario1.id WHERE cuenta_movimiento.cuenta_id = ".$_GET['cuenta_id'];
if ((isset($_GET['desde_mask']))&&($_GET['desde_mask']!='')) {
	$desde = fechasql($_GET['desde_mask']);
	$sql .=" AND cuenta_movimiento.fecha >= '".$desde."'";
}
if ((isset($_GET['hasta_mask']))&&($_GET['hasta_mask']!=''))  {
	$hasta = fechasql($_GET['hasta_mask']);
	$sql .=" AND cuenta_movimiento.fecha <= '".$hasta."'";
}
$sql .=" ORDER BY cuenta_movimiento.fecha DESC";
//echo $sql;
$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){
	$orden ='';
	if($rs['operacion_tipo'] == ''){

		$detalle = $rs['origen'];


		if($detalle == 'cheque'){
			$detalle = 'cheque ('.$rs['numero'].')';
			 $rs['user'] = $rs['usercheque'];
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
										$arrayDevolucion = explode('-', $reserva_cobro_transferencia[$es_cuenta[1]]);
                                        $detalle = "Transferencia realizada por ".$arrayDevolucion[2];


								            $orden = 'Res. '.$arrayDevolucion[0];
								            $rs['user'] = $arrayDevolucion[1];

		}elseif($es_cuenta[0] == 'reservacheque'){
                                        $detalle = "Cheque";
                                        $arrayDevolucion = explode('-', $reserva_cobro_cheque[$es_cuenta[1]]);

								            $orden = 'Res. '.$arrayDevolucion[0];
								            $rs['user'] = $arrayDevolucion[1];

		}elseif($es_cuenta[0] == 'acreditacionlote'){
                                        $detalle = $lotes[$es_cuenta[1]];
		}
		elseif($es_cuenta[0] == 'debitocuenta'){

			$orden = "";
			$sql1 = "SELECT CP.id AS id_cuotaPlan, G.id as id_gasto, G.nro_orden, ES.id as id_sueldo, CONCAT(E.nombre,' ',E.apellido) as empleadoSueldo, EA.id as id_adelanto, CONCAT(E1.nombre,' ',E1.apellido) as empleadoAdelanto, TR.id as id_tarjeta, TR.nombre as tarjetaNombre,C.id as id_compra, C.nro_orden as ordenCompra, P.plan, CP.vencimiento
					FROM rel_pago_operacion RPO
					LEFT JOIN gasto G ON RPO.operacion_id = G.id AND RPO.operacion_tipo = 'gasto'
					LEFT JOIN empleado_sueldo ES ON RPO.operacion_id = ES.id AND RPO.operacion_tipo = 'sueldo_pago'
					LEFT JOIN empleado E ON ES.empleado_id = E.id
					LEFT JOIN empleado_adelanto EA ON RPO.operacion_id = EA.id AND RPO.operacion_tipo = 'sueldo_adelanto'
					LEFT JOIN empleado E1 ON EA.empleado_id = E1.id
					LEFT JOIN tarjeta_resumen TR ON RPO.operacion_id = TR.id AND RPO.operacion_tipo = 'tarjeta_resumen'
					LEFT JOIN compra C ON RPO.operacion_id = C.id AND RPO.operacion_tipo = 'compra'
					LEFT JOIN cuota_plans CP ON RPO.operacion_id = CP.id AND RPO.operacion_tipo = 'cuota_plan'
					LEFT JOIN plans P ON CP.plan_id = P.id
					WHERE RPO.forma_pago = 'debito' AND operacion_id = ".$es_cuenta[1];
			$rsTemp1 = mysql_query($sql1);
			while($rs1 = mysql_fetch_array($rsTemp1)){
				if ($rs1['id_gasto']) {
					$detalle = "Debito de cuenta (gasto)";
					$orden .= $rs1['nro_orden'].' - ';
				}
				if ($rs1['id_sueldo']) {
					$detalle = "Debito de cuenta (haberes ".$rs1['empleadoSueldo'].")";
				}
				if ($rs1['id_adelanto']) {
					$detalle = "Debito de cuenta (adelanto ".$rs1['empleadoAdelanto'].")";
				}
				if ($rs1['id_compra']) {
					$detalle = "Debito de cuenta (compra)";
					$orden .= $rs1['ordenCompra'].' - ';
				}

				if ($rs1['id_tarjeta']) {
					$detalle = "Debito de cuenta (Tarjeta ".$rs1['tarjetaNombre'].")";
				}
                if ($rs1['id_cuotaPlan']) {
                    $detalle = "Debito de cuenta (Cuota Plan ".$rs1['plan']." Vencimiento ".$rs1['vencimiento'].")";
                }

			}
			$orden = substr( $orden, 0, strlen($orden)-3);
		}

	}elseif($rs['operacion_tipo'] == 'sueldo_pago'){
		$detalle = $salario[$rs['operacion_id']];
		$rs['user'] = $salarioAbonado[$rs['operacion_id']];
	}elseif($rs['operacion_tipo'] == 'sueldo_adelanto'){
		$detalle = $adelanto[$rs['operacion_id']];
		$rs['user'] = $adelantoAbonado[$rs['operacion_id']];
                 }elseif($rs['operacion_tipo'] == 'reserva_devolucion'){
                     $detalle = $devoluciones[$rs['operacion_id']];
                     $rs['user'] = $devolucionesUser[$rs['operacion_id']];

                 }else{
		if($rs['origen'] == 'cheque'){
			$detalle = 'cheque ('.$rs['numero'].')';
			 $rs['user'] = $rs['usercheque'];
		}
		else
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
	$sql_gastos = "SELECT gasto.id,gasto.nro_orden, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM gasto LEFT JOIN usuario ON gasto.user_id = usuario.id WHERE gasto.id IN (".implode(",",$gastos).")";
	$rsGastosTemp = mysql_query($sql_gastos);
	while($rsGastos = mysql_fetch_array($rsGastosTemp)){

		$operacion['gasto_'.$rsGastos['id']] = $rsGastos['nro_orden'];
		$operacion['gasto_user_'.$rsGastos['id']] = $rsGastos['user'];

	}
	foreach($gastos as $consumo_id=>$gasto_id){
		if (substr($rowTemp[$consumo_id]['detalle'], 0, 6)=='cheque') {
			$rowTemp[$consumo_id]['orden'] = 'Gto: '.$operacion['gasto_'.$gasto_id];
		}
		else{
			$rowTemp[$consumo_id]['orden'] = $operacion['gasto_'.$gasto_id];
			$rowTemp[$consumo_id]['user'] = $operacion['gasto_user_'.$gasto_id];
		}

	}
} //si gastos es array y mayor a cero

//agrego detalle de los gastos
$compras = $operaciones['compra'];
if(is_array($compras) and count($compras)>0){
	$sql_compras = "SELECT compra.id,compra.nro_orden, CONCAT(usuario.apellido,', ',usuario.nombre) as user FROM compra LEFT JOIN usuario ON compra.user_id = usuario.id WHERE compra.id IN (".implode(",",$compras).")";
	$rsComprasTemp = mysql_query($sql_compras);
	while($rsCompras = mysql_fetch_array($rsComprasTemp)){

		$operacion['compra_'.$rsCompras['id']] = $rsCompras['nro_orden'];
		$operacion['compra_user_'.$rsCompras['id']] = $rsCompras['user'];

	}
	foreach($compras as $consumo_id=>$compra_id){
		if (substr($rowTemp[$consumo_id]['detalle'], 0, 6)=='cheque') {
			$rowTemp[$consumo_id]['orden'] = 'Comp: '.$operacion['compra_'.$compra_id];
		}
		else{
			$rowTemp[$consumo_id]['orden'] = $operacion['compra_'.$compra_id];
			$rowTemp[$consumo_id]['user'] = $operacion['compra_user_'.$compra_id];
		}
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
