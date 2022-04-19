<?php
if(isset($_GET['dataid'])){
	$dataid = $_GET['dataid'];
}

if(isset($_POST['agregar'])){
	
	foreach($campos as $key=>$atr){
		
		if(($atr['type']=='date')||($atr[0]=='date')){
			$datos[$key] = fechasql($_POST[$key]);
		}
		elseif(($atr['type']=='checkbox')||($atr[0]=='checkbox')){
			$datos[$key] = ($_POST[$key])?1:0;
		}
		elseif(($atr['type']=='textarea')||($atr[0]=='textarea')){
			$datos[$key] = addslashes($_POST[$key]);
		}elseif(($atr['type']=='text_info')||($atr[0]=='text_info')){
			$var = 'null';
		}else{
			$datos[$key] = $_POST[$key];
		}
	}	
	//print_r($datos);
	if (!$datos['id']) {
		$datos['id']='0';
	}	
	$result = mysql_insert($tabla,$datos, $conn);
	$usuario_id = mysql_insert_id();
	//creo el histórico de empleados
	if ($tabla == 'empleado' ) {
		$alta = fechasql($_POST['fecha_alta']);
		$sql = "INSERT INTO empleado_historico (empleado_id,alta) VALUES ($usuario_id,'$alta')";
		//echo $sql;
		mysql_query($sql);
	}
	//guardo si hay permisos actualizados
	if(isset($_POST['permisos'])){
	
		$permisos = $_POST['permisos'];
		
		foreach($permisos as $key => $permiso_id){
		
			$sql = "INSERT INTO usuario_permiso (usuario_id,permiso_id) VALUES ($usuario_id,$permiso_id)";
			mysql_query($sql);
		
		}
		
	}
	if(isset($_POST['rubros'])){
	
		$rubros = $_POST['rubros'];
		
		foreach($rubros as $key => $rubro_id){
		
			$sql = "INSERT INTO usuario_rubro (usuario_id,rubro_id) VALUES ($usuario_id,$rubro_id)";
			mysql_query($sql);
		
		}
		
	}
	
	//guardo si hay cajas seleccionadas
	if(isset($_POST['cajas'])){
	
		$cajas = $_POST['cajas'];
		
		foreach($cajas as $key => $caja_id){
		
			$sql = "INSERT INTO usuario_caja (usuario_id,caja_id) VALUES ($usuario_id,$caja_id)";
			mysql_query($sql);
		
		}
		
	}	
	
	if(isset($_POST['cuentas'])){
	
		$cuentas = $_POST['cuentas'];
		
		foreach($cuentas as $key => $cuenta_id){
		
			$sql = "INSERT INTO usuario_cuenta (usuario_id,cuenta_id) VALUES ($usuario_id,$cuenta_id)";
			mysql_query($sql);
		
		}
		
	}	
}
if(isset($_POST['editar'])){
	
	foreach($campos as $key=>$atr){
		
		if(($atr['type']=='date')||($atr[0]=='date')){
			$datos[$key] = fechasql($_POST[$key]);
		}
		elseif(($atr['type']=='checkbox')||($atr[0]=='checkbox')){
			$datos[$key] = ($_POST[$key])?1:0;
		}
		elseif(($atr['type']=='textarea')||($atr[0]=='textarea')){
			$datos[$key] = addslashes($_POST[$key]);
		}elseif(($atr['type']=='text_info')||($atr[0]=='text_info')){
			$var = 'null';
		}else{
			$datos[$key] = $_POST[$key];
		}
	}
	
	$result = mysql_update($tabla,$datos,$datos['id'],$conn);
	$dataid = $datos['id'];
	$usuario_id = $dataid;
	
	if(isset($_POST['cajas'])){
	
		$sql = "DELETE FROM usuario_caja WHERE usuario_id=$usuario_id";
		mysql_query($sql);
		
		$cajas = $_POST['cajas'];
		
		foreach($cajas as $key => $caja_id){
		
			$sql = "INSERT INTO usuario_caja (usuario_id,caja_id) VALUES ($usuario_id,$caja_id)";
			mysql_query($sql);
		
		}
		
	}


	if(isset($_POST['permisos'])){
	
		$sql = "DELETE FROM usuario_permiso WHERE usuario_id=$usuario_id";
		mysql_query($sql);
		
		$permisos = $_POST['permisos'];
		
		foreach($permisos as $key => $permiso_id){
		
			$sql = "INSERT INTO usuario_permiso (usuario_id,permiso_id) VALUES ($usuario_id,$permiso_id)";
			mysql_query($sql);
		
		}
		
	}
	
	if(isset($_POST['rubros'])){
	
		$sql = "DELETE FROM usuario_rubro WHERE usuario_id=$usuario_id";
		mysql_query($sql);
		
		$rubros = $_POST['rubros'];
		
		foreach($rubros as $key => $rubro_id){
		
			$sql = "INSERT INTO usuario_rubro (usuario_id,rubro_id) VALUES ($usuario_id,$rubro_id)";
			mysql_query($sql);
		
		}
		
	}
	
	if(isset($_POST['cuentas'])){
		
		$sql = "DELETE FROM usuario_cuenta WHERE usuario_id=$usuario_id";
		mysql_query($sql);
	
		$cuentas = $_POST['cuentas'];
		
		foreach($cuentas as $key => $cuenta_id){
		
			$sql = "INSERT INTO usuario_cuenta (usuario_id,cuenta_id) VALUES ($usuario_id,$cuenta_id)";
			mysql_query($sql);
		
		}
		
	}	
}
?>