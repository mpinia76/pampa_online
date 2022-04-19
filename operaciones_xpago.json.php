<?
session_start();

include_once("config/db.php");

$sql = "SELECT 'gasto' as operacion, rel_pago_operacion.forma_pago, gasto.fecha, gasto.nro_orden, gasto.monto FROM gasto INNER JOIN rel_pago_operacion ON gasto.id=rel_pago_operacion.operacion_id UNION 
SELECT 'compra' as operacion, rel_pago_operacion.forma_pago, compra.fecha, compra.nro_orden, compra.monto FROM compra INNER JOIN rel_pago_operacion ON compra.id=rel_pago_operacion.operacion_id ORDER BY fecha DESC";

$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){

	$data = array(
	"id" => $i,
		"data" => array(
			ucfirst($rs['operacion']),
			ucfirst($rs['forma_pago']),
			$rs['fecha'],
			$rs['nro_orden'],
			$rs['monto']
		)
	);
	array_push($rows,$data);
	$i++;
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>