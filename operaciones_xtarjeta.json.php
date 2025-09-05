<?
session_start();

include_once("config/db.php");

$sql = "SELECT 'gasto' as operacion, rel_pago_operacion.forma_pago, gasto.fecha, gasto.nro_orden, gasto.monto, tarjeta_consumo.monto as monto_tarjeta, tarjeta_consumo.cuotas, tarjeta.titular, tarjeta_marca.marca FROM gasto 
INNER JOIN rel_pago_operacion ON gasto.id=rel_pago_operacion.operacion_id AND rel_pago_operacion.forma_pago='tarjeta' INNER JOIN tarjeta_consumo ON rel_pago_operacion.forma_pago_id=tarjeta_consumo.id INNER JOIN tarjeta ON tarjeta_consumo.tarjeta_id=tarjeta.id INNER JOIN tarjeta_marca ON tarjeta.tarjeta_marca_id=tarjeta_marca.id

UNION

SELECT 'compra' as operacion, rel_pago_operacion.forma_pago, compra.fecha, compra.nro_orden, compra.monto, tarjeta_consumo.monto as monto_tarjeta, tarjeta_consumo.cuotas, tarjeta.titular, tarjeta_marca.marca FROM compra 
INNER JOIN rel_pago_operacion ON compra.id=rel_pago_operacion.operacion_id AND rel_pago_operacion.forma_pago='tarjeta' INNER JOIN tarjeta_consumo ON rel_pago_operacion.forma_pago_id=tarjeta_consumo.id INNER JOIN tarjeta ON tarjeta_consumo.tarjeta_id=tarjeta.id INNER JOIN tarjeta_marca ON tarjeta.tarjeta_marca_id=tarjeta_marca.id

ORDER BY fecha DESC
";

$rsTemp = mysqli_query($conn,$sql);
$rows = array();
while($rs = mysqli_fetch_array($rsTemp)){

	$data = array(
	"id" => $i,
		"data" => array(
			ucfirst($rs['titular']),
			ucfirst($rs['marca']),
			ucfirst($rs['operacion']),
			$rs['fecha'],
			$rs['nro_orden'],
			$rs['monto'],
			$rs['monto_tarjeta'],
			$rs['cuotas']
		)
	);
	array_push($rows,$data);
	$i++;
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>