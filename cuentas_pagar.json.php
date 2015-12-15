<?php
session_start();

include_once("config/db.php");
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
}elseif($_GET['pagado'] == 't'){
	$estado = '';
}

$sql = "SELECT 
			'Gasto' as operacion,cuenta_a_pagar.*,gasto.proveedor,gasto.nro_orden,gasto.factura_nro,gasto.fecha, usuario.nombre, usuario.apellido 
		FROM cuenta_a_pagar 
		INNER JOIN gasto 
			ON cuenta_a_pagar.operacion_id=gasto.id AND cuenta_a_pagar.operacion_tipo='gasto'
			INNER JOIN usuario 
			ON usuario.id=gasto.user_id  $estado
		UNION 
		SELECT 
			'Compra' as operacion,cuenta_a_pagar.*,compra.proveedor,compra.nro_orden,compra.factura_nro,compra.fecha, usuario.nombre, usuario.apellido
		FROM cuenta_a_pagar 
		INNER JOIN compra 
			ON cuenta_a_pagar.operacion_id=compra.id AND cuenta_a_pagar.operacion_tipo='compra'  
			INNER JOIN usuario 
			ON usuario.id=compra.user_id  $estado
		UNION
		SELECT 
			CONCAT('Resumen ',tarjeta_resumen.nombre) as operacion,cuenta_a_pagar.*, '' as proveedor, '' as nro_orden ,'' as factura_nro, '' as fecha, '' as nombre, '' as apellido
		FROM cuenta_a_pagar
		INNER JOIN tarjeta_resumen
			ON cuenta_a_pagar.operacion_id=tarjeta_resumen.id AND cuenta_a_pagar.operacion_tipo='tarjeta_resumen' $estado";

$rsTemp = mysql_query($sql); echo mysql_error();
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['estado']==0){
		$estado = 'Pendiente de pago';
	}else{
		$estado = 'Pagado';
	}

	$segundos= strtotime('now')-strtotime($rs['fecha']);
	$cantidad_dias=intval($segundos/60/60/24);
	
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['nro_orden'],
			$rs['operacion'],
			getProveedor($rs['proveedor'],$provs),
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