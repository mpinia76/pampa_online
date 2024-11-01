<?php
session_start();

include_once("config/db.php");

$ano = $_GET['ano'];
$mes = $_GET['mes'];

//sector
$sql = "SELECT empleado_trabajo.*,a.sector as 'sector1', b.sector as 'sector2', espacio_trabajo.espacio, espacio_trabajo.id as 'espacio_id' FROM empleado_trabajo LEFT JOIN sector as a ON empleado_trabajo.sector_1_id = a.id LEFT JOIN sector as b ON empleado_trabajo.sector_2_id = b.id INNER JOIN espacio_trabajo ON empleado_trabajo.espacio_trabajo_id = espacio_trabajo.id ORDER BY id ASC";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['sector1'] != '' and $rs['sector2'] != ''){
		$sector[$rs['empleado_id']] = $rs['sector1']." (".$rs['porcentaje_sector_1']."),".$rs['sector2']." (".$rs['porcentaje_sector_2'].")";
	}elseif($rs['sector1'] != '' and $rs['sector2'] == ''){
		$sector[$rs['empleado_id']] = $rs['sector1']." (".$rs['porcentaje_sector_1'].")";
	}elseif($rs['sector2'] != '' and $rs['sector1'] == ''){
		$sector[$rs['empleado_id']] = $rs['sector2']." (".$rs['porcentaje_sector_2'].")";
	}
	$espacio[$rs['empleado_id']] = $rs['espacio'];
	$espacio_id[$rs['empleado_id']] = $rs['espacio_id'];
}

//sueldo + asignaciones
$sql = "SELECT * FROM empleado_sueldo WHERE ano = $ano AND mes = $mes ORDER BY id ASC";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$sueldo[$rs['empleado_id']] = $rs['sueldo'] + $rs['viaticos'] + $rs['asignaciones'] + $rs['presentismo'] ;
	$aguinaldo[$rs['empleado_id']]=$rs['aguinaldo'];
	
}

//adelantos
$sql = "SELECT * FROM empleado_adelanto WHERE ano = $ano AND mes = $mes";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
    $adelanto[$rs['empleado_id']] = $adelanto[$rs['empleado_id']] + $rs['monto'];
}

//horas extras
$sql = "SELECT ehe.cantidad_aprobada*she.valor as 'monto', ehe.* FROM empleado_hora_extra ehe INNER JOIN sector_horas_extras she ON ehe.hora_extra_id = she.id WHERE ehe.estado = 1 AND ano = $ano AND mes = $mes";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
    $hora_extra[$rs['empleado_id']] = $hora_extra[$rs['empleado_id']] + round($rs['monto'],2);
}

//pagos
$sql = "SELECT * FROM empleado_pago WHERE ano = $ano AND mes = $mes";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$pago[$rs['empleado_id']] = $rs['monto'];
	$descuento[$rs['empleado_id']]=$rs['descuentos'];
}

/*if($_GET['espacio'] == 'todos'){
	//$sql = "SELECT * FROM empleado e WHERE fecha_alta <= '".date('Y-m-d',mktime(0,0,0,$mes,1,$ano))."'";
	$sql = "SELECT alta, baja, e.* 
FROM empleado e INNER JOIN empleado_historico eh ON e.id = eh.empleado_id  
WHERE alta <= '".date('Y-m-d',mktime(0,0,0,$mes+1,1,$ano))."' AND eh.id=(SELECT max(eh2.id) FROM empleado_historico eh2 WHERE eh.empleado_id= eh2.empleado_id GROUP BY eh2.empleado_id)";
}elseif($_GET['espacio'] == 'oficina'){
	foreach($espacio_id as $empleado_id => $sector){
		if($sector == 2){
			$empleados[] = $empleado_id;
		}
	}
	$list = implode(",",$empleados);
	//$sql = "SELECT * FROM empleado e WHERE e.id IN ($list) and fecha_alta <= '".date('Y-m-d',mktime(0,0,0,$mes,1,$ano))."'";
	$sql = "SELECT alta, baja, e.* 
FROM empleado e INNER JOIN empleado_historico eh ON e.id = eh.empleado_id  
WHERE e.id IN ($list) and alta <= '".date('Y-m-d',mktime(0,0,0,$mes+1,1,$ano))."' AND eh.id=(SELECT max(eh2.id) FROM empleado_historico eh2 WHERE eh.empleado_id= eh2.empleado_id GROUP BY eh2.empleado_id)";
}elseif($_GET['espacio'] == 'hotel'){
	foreach($espacio_id as $empleado_id => $sector){
		if($sector == 1){
			$empleados[] = $empleado_id;
		}
	}
	$list = implode(",",$empleados);
	//$sql = "SELECT * FROM empleado e WHERE e.id IN ($list) and fecha_alta <= '".date('Y-m-d',mktime(0,0,0,$mes,1,$ano))."'";
	$sql = "SELECT alta, baja, e.* 
FROM empleado e INNER JOIN empleado_historico eh ON e.id = eh.empleado_id  
WHERE e.id IN ($list) and alta <= '".date('Y-m-d',mktime(0,0,0,$mes+1,1,$ano))."' AND eh.id=(SELECT max(eh2.id) FROM empleado_historico eh2 WHERE eh.empleado_id= eh2.empleado_id GROUP BY eh2.empleado_id)";
}elseif($_GET['espacio'] == 'BB'){
    foreach($espacio_id as $empleado_id => $sector){
        if($sector == 3){
            $empleados[] = $empleado_id;
        }
    }
    $list = implode(",",$empleados);
    //$sql = "SELECT * FROM empleado e WHERE e.id IN ($list) and fecha_alta <= '".date('Y-m-d',mktime(0,0,0,$mes,1,$ano))."'";
    $sql = "SELECT alta, baja, e.* 
FROM empleado e INNER JOIN empleado_historico eh ON e.id = eh.empleado_id  
WHERE e.id IN ($list) and alta <= '".date('Y-m-d',mktime(0,0,0,$mes+1,1,$ano))."' AND eh.id=(SELECT max(eh2.id) FROM empleado_historico eh2 WHERE eh.empleado_id= eh2.empleado_id GROUP BY eh2.empleado_id)";
}*/
$centro = $_GET['espacio'];
$espacio_array = explode(',', $centro);

foreach($espacio_id as $empleado_id => $sec){
    if (in_array($sec, $espacio_array )){
    //if($sector == 1){
        $empleados[] = $empleado_id;
    }
}
$list = implode(",",$empleados);
$sql = "SELECT alta, baja, e.* 
FROM empleado e INNER JOIN empleado_historico eh ON e.id = eh.empleado_id  
WHERE e.id IN ($list) and alta <= '".date('Y-m-d',mktime(0,0,0,$mes+1,1,$ano))."' AND eh.id=(SELECT max(eh2.id) FROM empleado_historico eh2 WHERE eh.empleado_id= eh2.empleado_id GROUP BY eh2.empleado_id)";

//echo $sql."<br>";
$rsTemp = mysql_query($sql);
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){
    if($rs['estado'] == '1' or (mktime(0,0,0,$mes,1,$ano) <= strtotime($rs['baja']))){
	if($pago[$rs['id']] == '' and $adelanto[$rs['id']] == ''){
		$pago[$rs['id']] = 0;
		$estado = 'Pendiente';
	}elseif($pago[$rs['id']] == '' and $adelanto[$rs['id']] != ''){
		$pago[$rs['id']] = 0;
		$estado = 'Con adelanto';
	}elseif($pago[$rs['id']] != ''){
		$estado = 'Pago';
	}
	$data = array(
		"id" => $rs['id'],
		"data" => array(
			$rs['nro_legajo'],
			$rs['nombre']." ".$rs['apellido'],
			$espacio[$rs['id']],
			$sector[$rs['id']],
			$sueldo[$rs['id']],
			$aguinaldo[$rs['id']],
			$hora_extra[$rs['id']],
			$descuento[$rs['id']],
			round($sueldo[$rs['id']]+$aguinaldo[$rs['id']]+$hora_extra[$rs['id']]-$descuento[$rs['id']],2),
			$adelanto[$rs['id']],
			
			round($sueldo[$rs['id']]+$aguinaldo[$rs['id']]+$hora_extra[$rs['id']]-$descuento[$rs['id']]-$adelanto[$rs['id']],2),
			$pago[$rs['id']],
			$estado
		)
	);
	array_push($rows,$data);
    }
}

$array = array("rows" => $rows);

$json = json_encode($array);

echo $json;

?>