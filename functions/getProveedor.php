<?php
//funcion para obtener el nombre del proveedor segun corresponda
function getProveedor($nombre){
	
	$sql_prov = "SELECT nombre FROM proveedor WHERE id='$nombre'";
	$rs_prov = mysql_fetch_array(mysql_query($sql_prov));
	
	if($rs_prov['nombre'] == ''){
	
		return $nombre;
		
	}else{
	
		return $rs_prov['nombre'];
		
	}
}
?>