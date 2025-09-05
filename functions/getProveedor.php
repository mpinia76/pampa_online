<?php
//funcion para obtener el nombre del proveedor segun corresponda
function getProveedor($nombre){
	
	$sql_prov = "SELECT nombre FROM proveedor WHERE id='$nombre'";
	$rs_prov = mysqli_fetch_array(mysqli_query($conn,$sql_prov));
	
	if($rs_prov['nombre'] == ''){
	
		return $nombre;
		
	}else{
	
		return $rs_prov['nombre'];
		
	}
}
?>