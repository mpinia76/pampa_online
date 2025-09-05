<?
session_start();

include_once("config/db.php");

if($_GET['rubro']){
	$where = "rubro.id = ".$_GET['rubro'];
}

if($_GET['desaprobadas'] == 0){
	$where = "gasto.estado != 2";
}elseif($_GET['desaprobadas'] == 1){
	$where = "gasto.estado = 2";
}

if(isset($where) and $where != ''){
	$where = "WHERE ".$where;
}

$sql = "SELECT rubro.rubro,subrubro.subrubro,gasto.fecha,DATE_FORMAT(gasto.created,'%Y-%m-%d') as created,gasto.monto,gasto.estado,gasto.nro_orden,gasto.id,gasto.factura_nro FROM gasto LEFT JOIN subrubro ON gasto.subrubro_id=subrubro.id INNER JOIN rubro ON gasto.rubro_id = rubro.id $where ORDER BY created DESC";
$rsTemp = mysqli_query($conn,$sql); echo mysql_error();
$rows = array();
while($rs = mysqli_fetch_array($rsTemp)){

	if($rs['estado'] == 0 and $rs['nro_orden'] == 0){
		$nro_orden	= 'Pendiente';
		$estado 	= 'Esperando nro. orden';
	}elseif($rs['estado'] == 0 and $rs['nro_orden'] != 0){
		$nro_orden 	= $rs['nro_orden'];
		$estado 	= 'Falta abonar';
	}elseif($rs['estado'] == 1 and $rs['nro_orden'] != 0 and $rs['factura_nro'] == ''){
		$nro_orden 	= $rs['nro_orden'];
		$estado 	= 'Procesado: Falta factura';
	}elseif($rs['estado'] == 1 and $rs['nro_orden'] != 0 and $rs['factura_nro'] != ''){
		$nro_orden 	= $rs['nro_orden'];
		$estado 	= 'Procesado';
	}elseif($rs['estado'] == 2){
		$nro_orden 	= '';
		$estado 	= 'Desaprobado';
	}
	
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['created'],
			$rs['fecha'],
			$rs['rubro'],
			$rs['subrubro'],
			$rs['monto'],
			$nro_orden,
			$estado
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>