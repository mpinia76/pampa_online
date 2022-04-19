<?php
session_start();

include_once("config/db.php");
include_once("functions/date.php");
include_once("functions/fechasql.php");

$debitado = $_GET['debitado'];


if($debitado == "no"){
	$where = "WHERE cheque_consumo.debitado = 0 AND vencido = 0";
}elseif($debitado == "si"){
	$where = "WHERE cheque_consumo.debitado = 1 AND vencido = 0";
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
	
	$where .= "cheque_consumo.fecha >= '$inicio' AND cheque_consumo.fecha <= '$fin'";
}

$sql = "SELECT reservas.numero as reserva_numero, gasto.nro_orden as gasto_orden, compra.nro_orden as compra_orden,cheque_consumo.*,banco.banco,cuenta.sucursal,cuenta.nombre,MONTH(cheque_consumo.fecha) as mes FROM cheque_consumo INNER JOIN cuenta ON cheque_consumo.cuenta_id=cuenta.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id LEFT JOIN rel_pago_operacion ON cheque_consumo.id = rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago = 'cheque' LEFT JOIN gasto ON gasto.id = rel_pago_operacion.operacion_id and rel_pago_operacion.operacion_tipo = 'gasto' LEFT JOIN compra ON compra.id = rel_pago_operacion.operacion_id and rel_pago_operacion.operacion_tipo = 'compra' LEFT JOIN reserva_devoluciones ON reserva_devoluciones.id = rel_pago_operacion.operacion_id LEFT JOIN reservas ON reserva_devoluciones.reserva_id = reservas.id $where GROUP BY cheque_consumo.id ORDER BY `cheque_consumo`.`fecha` DESC";

$rsTemp = mysql_query($sql); 
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){
	
	$concepto = '';
	$estado	= '';

	$monto		= $rs['monto'];
	if($rs['debitado'] == 1){
		$estado = 'Debitado';
	}else{
		$estado = 'Pendiente';
	}
                  if($rs['vencido'] == 1){
                      $estado = 'Vencido';
                  }
	
	if($rs['gasto_orden'] != ''){
		$concepto = "Gasto - ".$rs['gasto_orden'];
	}elseif($rs['compra_orden'] != ''){
		$concepto = "Compra - ".$rs['compra_orden'];
                 }elseif($rs['reserva_numero'] != ''){
                     $concepto = "Devolucion de Reserva nro: ".$rs['reserva_numero'];
	}elseif($rs['concepto'] != ''){
		$concepto = $rs['concepto'];
	}
	
	if($rs['fecha_debitado'] == '0000-00-00'){
		$debitado = '';
	}else{
		$debitado = $rs['fecha_debitado'];
	}
	
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['fecha'],
			$debitado,
			mes($rs['mes']),
			$rs['banco']." (".$rs['sucursal'].")",
			$rs['nombre'],
			$rs['numero'],
			$rs['titular'],
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