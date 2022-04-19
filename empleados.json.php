<?php
session_start();

include_once("config/db.php");

$sql = "SELECT e.id,et.espacio_trabajo_id FROM empleado e LEFT JOIN empleado_trabajo et ON e.id = et.empleado_id ORDER BY e.id ASC";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$sector[$rs['id']] = $rs['espacio_trabajo_id'];
}
if($_GET['espacio'] == 'todos'){
	$sql = "SELECT id,nombre,apellido, CASE estado WHEN '0' THEN 'Inactivo' ELSE 'Activo' END as estado FROM empleado WHERE estado = ".$_GET['activo'];
}elseif($_GET['espacio'] == 'oficina'){
	foreach($sector as $empleado_id => $sector){
		if($sector == 2 or $sector == ''){
			$empleados[] = $empleado_id;
		}
	}
	$list = implode(",",$empleados);
	$sql = "SELECT id,nombre,apellido, CASE estado WHEN '0' THEN 'Inactivo' ELSE 'Activo' END as estado FROM empleado WHERE id IN ($list) AND estado = ".$_GET['activo'];
}elseif($_GET['espacio'] == 'hotel'){
	foreach($sector as $empleado_id => $sector){
		if($sector == 1 or $sector == ''){
			$empleados[] = $empleado_id;
		}
	}
	$list = implode(",",$empleados);
	$sql = "SELECT id,nombre,apellido, CASE estado WHEN '0' THEN 'Inactivo' ELSE 'Activo' END as estado FROM empleado WHERE id IN ($list) AND estado = ".$_GET['activo'];
}
$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){
	
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['nombre'],
			$rs['apellido'],
			$rs['estado']
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>