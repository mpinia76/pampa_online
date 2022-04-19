<?php
session_start();

include_once("config/db.php");
include_once("config/user.php");
$sql = "SELECT id,nombre FROM proveedor";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$provs[$rs['id']] = $rs['nombre'];
}
function getProveedor($nombre,$provs){

	if($provs[$nombre] == ''){

		return $nombre;

	}else{

		return $provs[$nombre];

	}
}

if($_GET['pagado'] == 'no'){
	$estado = 'WHERE cuenta_a_pagar.estado = 0';
}elseif($_GET['pagado'] == 'si'){
	$estado = 'WHERE cuenta_a_pagar.estado = 1';
}elseif($_GET['pagado'] == 'p'){
	$estado = 'WHERE cuenta_a_pagar.estado = 2';
}elseif($_GET['pagado'] == 't'){
	$estado = '';
}
$sql = "";
if(ACCION_125){
	$sql .= "SELECT
			'Gastos y compras' as operacion,cuenta_a_pagar.*,gasto.proveedor,rubro.rubro, subrubro.subrubro,gasto.nro_orden,gasto.orden_pago,gasto.factura_nro,gasto.fecha, usuario.nombre, usuario.apellido
		FROM cuenta_a_pagar
		INNER JOIN gasto
			ON cuenta_a_pagar.operacion_id=gasto.id AND cuenta_a_pagar.operacion_tipo='gasto'
			INNER JOIN usuario
			ON usuario.id=gasto.user_id
			LEFT JOIN subrubro ON gasto.subrubro_id=subrubro.id INNER JOIN rubro ON gasto.rubro_id = rubro.id
		$estado
		UNION ";
}
if(ACCION_126){
		$sql .= "SELECT
			'Impuestos,tasas y Cargas sociales' as operacion,cuenta_a_pagar.*,compra.proveedor,rubro.rubro,subrubro.subrubro,compra.nro_orden,compra.orden_pago,compra.factura_nro,compra.fecha, usuario.nombre, usuario.apellido
		FROM cuenta_a_pagar
		INNER JOIN compra
			ON cuenta_a_pagar.operacion_id=compra.id AND cuenta_a_pagar.operacion_tipo='compra'
			INNER JOIN usuario
			ON usuario.id=compra.user_id
			LEFT JOIN subrubro ON compra.subrubro_id=subrubro.id INNER JOIN rubro ON compra.rubro_id = rubro.id
			$estado
		UNION ";
}
		$sql .= "SELECT
			CONCAT('Resumen ',tarjeta_resumen.nombre) as operacion,cuenta_a_pagar.*, '' as proveedor, '' as rubro, '' as subrubro, '' as nro_orden, '' as orden_pago ,'' as factura_nro, '' as fecha, '' as nombre, '' as apellido
		FROM cuenta_a_pagar
		INNER JOIN tarjeta_resumen
			ON cuenta_a_pagar.operacion_id=tarjeta_resumen.id AND cuenta_a_pagar.operacion_tipo='tarjeta_resumen' $estado";
//echo $sql;
$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){
   if($rs['estado']==0){
		$estado = 'Pendiente de pago';
		$segundos= strtotime('now')-strtotime($rs['fecha']);
   }elseif($rs['estado']==1){
       $estado = 'Pagado';
       $segundos= strtotime($rs['fecha_pago'])-strtotime($rs['fecha']);
   }else{
       $estado = 'Plan de pago';
       $segundos= ($rs['fecha_pago'])?strtotime($rs['fecha_pago'])-strtotime($rs['fecha']):strtotime('now')-strtotime($rs['fecha']);
   }

	$cantidad_dias=intval($segundos/60/60/24);


	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['nro_orden'],
			$rs['operacion'],
			getProveedor($rs['proveedor'],$provs),
			$rs['rubro'],
			$rs['subrubro'],
			$rs['monto'],
			$estado,
			$rs['fecha'],
			$cantidad_dias,
			$rs['factura_nro'],
			$rs['nombre'].' '.$rs['apellido']
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>
