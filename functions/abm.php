<?php
function validar($tabla,$campos,$conn=''){
	switch ($tabla) {
		case 'empleado':
			$sql="SELECT id, CONCAT(nombre,' ',apellido) as empleado, CASE WHEN estado ='0' THEN 'Inactivo' ELSE 'Activo' END as estado FROM $tabla WHERE dni = '".$campos['dni']."'";
			$rs = mysqli_fetch_array(mysqli_query($conn,$sql));
			//echo $sql;
			if ($rs['id']) {
				$msg=" El DNI ya se encuentra registrado para ".$rs['empleado']." - ".$rs['estado'];
				return $msg;
			}
		break;
		
		default:
			
		break;
	}
	return 0;
}
function mysql_insert($tabla,$campos,$conn=''){
$msg=validar($tabla, $campos,$conn);	
if (!$msg) {
	$query='INSERT INTO `'.$tabla.'` (';
	$count=0;
	foreach($campos as $campo => $valor){
		$campo = str_replace("\'","`",$campo);
			if($count==0)
			{
			$query.=$campo;
			}else{
			$query.=','.$campo;
			
			}
	$count++;
	}
	$query.=') VALUES (';
		$count=0;
	foreach($campos as $campo => $valor){
	if (strcmp ($valor,'NOW()') == 0){
	$valor="".$valor."";
	}else{
	$valor="'".$valor."'";}
			if($count==0)
			{
			$query.=$valor;
			}else{
			$query.=",".$valor;
			
			}
	$count++;
	}
	$query.=')';
	//echo $query;
	mysqli_query($conn,$query);
	$result=mysql_affected_rows();
	//print_r($result);
	if($result=="-1"){
	//$msg="No se pudieron cargar los datos: ".mysql_error();
		//$msg="No se pudieron cargar los datos: ";
	}
	
	else{
		if ($tabla=='empleado') {
			$sql = "INSERT INTO empleado_historico (empleado_id, alta) VALUES (".mysql_insert_id().", '".fechasql($_POST['fecha_alta'])."')";
			//echo $sql;
			mysqli_query($conn,$sql);
		}
		
		$msg=1;
	}
}

return $msg;
}

function mysql_update($tabla,$campos,$id,$conn='') {
$query='UPDATE `'.$tabla.'` SET ';
$count=0;
foreach($campos as $campo => $valor)
{
	$campo = str_replace("\'","`",$campo);
	if($count==0)
		{
	$query.=''.$campo.'=\''.$valor.'\'';
		}else{
	$query.=','.$campo.'=\''.$valor.'\'';	
		}
$count++;
}
$query.=' WHERE id = \''.$id.'\'';


mysqli_query($conn,$query);
$result=mysql_affected_rows();
if($result=="-1"){
	$msg="No se pudieron actualizar los datos: ".mysql_error();
	//$msg="No se pudieron actualizar los datos: ";
}
else{
	if (($tabla=='cheque_consumo')&&($_POST['vencido'])) {
		$numero = str_pad($_POST['numero'], 8,'0',STR_PAD_LEFT);
		$sql = "UPDATE chequera_cheques 
INNER JOIN chequeras ON chequera_cheques.chequera_id = chequeras.id
SET chequera_cheques.estado = 2 WHERE chequeras.cuenta_id = '".$_POST['cuenta_id']."' AND chequera_cheques.numero = '".$numero."'";
		//echo $sql;
		mysqli_query($conn,$sql);
		if(mysql_affected_rows() > 0){
			$sql = "SELECT chequeras.id FROM chequera_cheques INNER JOIN chequeras ON chequera_cheques.chequera_id = chequeras.id WHERE chequeras.cuenta_id = '".$_POST['cuenta_id']."' AND chequera_cheques.numero = '".$numero."'";
			
			$rsTempChequera = mysqli_query($conn,$sql);
			if($rsChequera = mysqli_fetch_array($rsTempChequera)){
				$sql = "SELECT chequera_cheques.chequera_id FROM chequera_cheques  WHERE chequera_cheques.chequera_id = '".$rsChequera['id']."' AND chequera_cheques.estado = '0'";
				
				mysqli_query($conn,$sql);
				$estadoChequera = (mysql_affected_rows() > 0)?'1':'3';
				$sql = "UPDATE chequeras SET estado = ".$estadoChequera." WHERE id = '".$rsChequera['id']."'";
				echo $sql;
				mysqli_query($conn,$sql);
			}
		}
	}
$msg=2;}
return $msg;
}

?>