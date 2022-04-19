<?php
session_start();

include_once("config/db.php");

//busco los motivos de movimientos de tarjeta
$sql = "SELECT * FROM motivo WHERE motivo_grupo_id = 3";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$motivos[$rs['id']] = $rs['nombre'];
}

//detalle del resumen
$resumen_id = $_GET['resumen_id'];
$sql = "SELECT * FROM tarjeta_resumen WHERE id=$resumen_id";
$rs = mysql_fetch_array(mysql_query($sql));
$inicio = $rs['inicio'];
$fin	= $rs['fin'];
$tarjeta = $rs['tarjeta_id'];

$sql = "SELECT
			tarjeta_consumo_cuota.fecha,CONCAT(tarjeta_consumo_cuota.nro_cuota,'/',tarjeta_consumo.cuotas) AS cuotas,tarjeta_consumo_cuota.monto,'Gastos y compras' as operacion, gasto.nro_orden
		FROM tarjeta_consumo_cuota
		INNER JOIN tarjeta_consumo
			ON tarjeta_consumo_cuota.tarjeta_consumo_id=tarjeta_consumo.id
		INNER JOIN rel_pago_operacion
			ON tarjeta_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago = 'tarjeta'
		INNER JOIN gasto
			ON rel_pago_operacion.operacion_tipo = 'gasto' AND rel_pago_operacion.operacion_id=gasto.id
		WHERE tarjeta_consumo_cuota.fecha >= '$inicio' and tarjeta_consumo_cuota.fecha <= '$fin' AND tarjeta_consumo.tarjeta_id='$tarjeta'
		UNION
		SELECT
			tarjeta_consumo_cuota.fecha,CONCAT(tarjeta_consumo_cuota.nro_cuota,'/',tarjeta_consumo.cuotas) AS cuotas,tarjeta_consumo_cuota.monto,'Impuestos,tasas y Cargas sociales' as operacion, compra.nro_orden
		FROM tarjeta_consumo_cuota
		INNER JOIN tarjeta_consumo
			ON tarjeta_consumo_cuota.tarjeta_consumo_id=tarjeta_consumo.id
		INNER JOIN rel_pago_operacion
			ON tarjeta_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago = 'tarjeta'
		INNER JOIN compra
			ON rel_pago_operacion.operacion_tipo = 'compra' AND rel_pago_operacion.operacion_id=compra.id
		WHERE tarjeta_consumo_cuota.fecha >= '$inicio' and tarjeta_consumo_cuota.fecha <= '$fin' AND tarjeta_consumo.tarjeta_id='$tarjeta'
		UNION
		SELECT
			tarjeta_consumo_cuota.fecha,CONCAT(tarjeta_consumo_cuota.nro_cuota,'/',tarjeta_consumo.cuotas) AS cuotas,tarjeta_consumo_cuota.monto,CONCAT('Resumen ',tarjeta_resumen.nombre) as operacion, '' as nro_orden
		FROM tarjeta_consumo_cuota
		INNER JOIN tarjeta_consumo
			ON tarjeta_consumo_cuota.tarjeta_consumo_id=tarjeta_consumo.id
		INNER JOIN rel_pago_operacion
			ON tarjeta_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago = 'tarjeta'
		INNER JOIN tarjeta_resumen
			ON rel_pago_operacion.operacion_tipo = 'tarjeta_resumen' AND rel_pago_operacion.operacion_id=tarjeta_resumen.id
		WHERE tarjeta_consumo_cuota.fecha >= '$inicio' and tarjeta_consumo_cuota.fecha <= '$fin' AND tarjeta_consumo.tarjeta_id='$tarjeta'
		UNION
		SELECT
			tarjeta_consumo_cuota.fecha,CONCAT(tarjeta_consumo_cuota.nro_cuota,'/',tarjeta_consumo.cuotas) AS cuotas,tarjeta_consumo_cuota.monto,CONCAT('Cuota Plan ',plans.plan, ' Vencimiento ',cuota_plans.vencimiento) as operacion, '' as nro_orden
		FROM tarjeta_consumo_cuota
		INNER JOIN tarjeta_consumo
			ON tarjeta_consumo_cuota.tarjeta_consumo_id=tarjeta_consumo.id
		INNER JOIN rel_pago_operacion
			ON tarjeta_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago = 'tarjeta'
		INNER JOIN cuota_plans
			ON rel_pago_operacion.operacion_tipo = 'cuota_plan' AND rel_pago_operacion.operacion_id=cuota_plans.id
		INNER JOIN plans
			ON plans.id=cuota_plans.plan_id
		WHERE tarjeta_consumo_cuota.fecha >= '$inicio' and tarjeta_consumo_cuota.fecha <= '$fin' AND tarjeta_consumo.tarjeta_id='$tarjeta'
		UNION
		SELECT
			tarjeta_movimiento.fecha, '' as cuotas,tarjeta_movimiento.monto,tarjeta_movimiento.detalle as operacion, '' as nro_orden
		FROM tarjeta_movimiento
		WHERE tarjeta_movimiento.tarjeta_resumen_id=$resumen_id
		";
//echo $sql;
$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){

	$es_mov = explode("_",$rs['operacion']);

	if($es_mov[0] == 'motivo'){
		$detalle = $motivos[$es_mov[1]];
	}else{
		$detalle = $rs['operacion'];
	}

	$i++;

	$data = array(
		"id" => $i,
		"data" => array(
			$detalle,
			$rs['nro_orden'],
			$rs['cuotas'],
			$rs['monto']
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>
