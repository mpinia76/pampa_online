<?php
session_start();

include_once("config/db.php");
include_once("functions/date.php");

$sql = "SELECT ehe.id, CONCAT(u.nombre,' ',u.apellido) as usuario,CONCAT(e.nombre,' ',e.apellido) as empleado,ehe.cantidad_solicitada,ehe.cantidad_aprobada,ehe.mes,ehe.ano,ehe.creado,s.sector, ehe.estado 
		FROM empleado e 
			INNER JOIN empleado_hora_extra ehe ON e.id = ehe.empleado_id 
			INNER JOIN sector_horas_extras she ON she.id = ehe.hora_extra_id 
			INNER JOIN usuario u ON u.id = ehe.creado_por
			INNER JOIN sector s ON she.sector_id = s.id
		WHERE empleado_id = ".$_GET['empleado_id'];
//echo $sql;
$rsTemp = mysql_query($sql); echo mysql_error();
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){
	
	if($rs['estado'] == 1){
		$estado = 'Aprobada';
	}elseif($rs['estado'] == 2){
		$estado = 'Desaprobada';
	}else{
		$estado = 'Pendiente';
	}
	
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['empleado'],
			$rs['creado'],
			$rs['sector'],
			$rs['cantidad_solicitada'],
			$rs['cantidad_aprobada'],
			mes($rs['mes']),
			$rs['ano'],
			$rs['usuario'],
			$estado
		)
	);
	array_push($rows,$data);
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>