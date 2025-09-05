<?php
session_start();

include_once("config/db.php");
include_once("functions/date.php");
include_once("functions/fechasql.php");

$debitado = $_GET['debitado'];

if($debitado == "no"){
	$where = "WHERE transferencia_consumo.debitado = 0";
}elseif($debitado == "si"){
	$where = "WHERE transferencia_consumo.debitado = 1";
}elseif($debitado == "t"){
	$where = "";
}

if($_GET['inicio'] != '' and $_GET['fin'] != ''){
	$inicio = fechasql($_GET['inicio']);
	$fin 	= fechasql($_GET['fin']);

	if($where == ""){
		$where = "WHERE ";
	}else{
		$where .= " AND ";
	}

	$where .= "transferencia_consumo.fecha >= '$inicio' AND transferencia_consumo.fecha <= '$fin'";
}
$sql = "SELECT rel_pago_operacion.operacion_tipo, gasto.nro_orden as gasto_orden, reservas.numero as reserva_numero, compra.nro_orden as compra_orden,transferencia_consumo.*,banco.banco,cuenta.sucursal,cuenta.nombre,MONTH(transferencia_consumo.fecha) as mes, cuota_plans.id AS id_cuotaPlan, plans.plan, cuota_plans.vencimiento  FROM transferencia_consumo INNER JOIN cuenta ON transferencia_consumo.cuenta_id=cuenta.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id LEFT JOIN rel_pago_operacion ON transferencia_consumo.id = rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago = 'transferencia' LEFT JOIN gasto ON gasto.id = rel_pago_operacion.operacion_id and rel_pago_operacion.operacion_tipo = 'gasto' LEFT JOIN compra ON compra.id = rel_pago_operacion.operacion_id and rel_pago_operacion.operacion_tipo = 'compra' LEFT JOIN cuota_plans ON cuota_plans.id = rel_pago_operacion.operacion_id and rel_pago_operacion.operacion_tipo = 'cuota_plan'
LEFT JOIN plans ON cuota_plans.plan_id = plans.id LEFT JOIN reserva_devoluciones ON reserva_devoluciones.id = rel_pago_operacion.operacion_id LEFT JOIN reservas ON reserva_devoluciones.reserva_id = reservas.id $where GROUP BY transferencia_consumo.id ORDER BY transferencia_consumo.fecha DESC";

$rsTemp = mysqli_query($conn,$sql); echo mysql_error();
$rows = array();
while($rs = mysqli_fetch_array($rsTemp)){

	$concepto = '';
	$estado	= '';

	$monto		= $rs['monto'];
	if($rs['debitado'] == 1){
		$estado = 'Debitado';
	}else{
		$estado = 'Pendiente';
	}

    if($rs['gasto_orden'] != ''){
        $concepto = "Gasto - ".$rs['gasto_orden'];
    }elseif($rs['compra_orden'] != ''){
        $concepto = "Compra - ".$rs['compra_orden'];
    }elseif($rs['concepto'] != ''){
        $concepto = $rs['concepto'];
    }elseif($rs['id_cuotaPlan'] != ''){
        $concepto = 'Cuota Plan '.$rs['plan'].' Venc. '.$rs['vencimiento'];
    }elseif($rs['reserva_numero'] != ''){
        $concepto = 'Devolucion de Reserva nro: '.$rs['reserva_numero'];
    }else{
        $concepto = $rs['operacion_tipo'];
    }

	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['fecha'],
			mes($rs['mes']),
			$rs['banco']." (".$rs['sucursal'].")",
			$rs['nombre'],
			$rs['cuenta_destino'],
			$concepto,
			round($monto,'2'),
			$estado
		)
	);

	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>
