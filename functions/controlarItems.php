<?php
$ids 	= $_GET['items'];


include_once("../config/db.php");
include_once("getProveedor.php");
//print_r($ids);
$ids = explode(",",$ids);
$ok=1;
$pendientes=1;
$tipos=1;
$proveedorAnt='';
$rubroAnt='';
$subrubroAnt='';
$tipoAnt='';
$arrayIds=array();
foreach ($ids as $id) {
	
	
	$sqlGasto = "SELECT 
			cuenta_a_pagar.estado,gasto.id,gasto.proveedor,gasto.rubro_id, gasto.subrubro_id 
		FROM cuenta_a_pagar 
		INNER JOIN gasto 
			ON cuenta_a_pagar.operacion_id=gasto.id AND cuenta_a_pagar.operacion_tipo='gasto'
			
			 WHERE cuenta_a_pagar.id = $id";
	//echo $sqlGasto.' ; ';
	//echo $id.': '.$proveedorAnt.' - '.$rubroAnt.' - '.$subrubroAnt.' - '.$tipoAnt.' / ';
	if($rsGasto = mysqli_fetch_array(mysqli_query($conn,$sqlGasto))){
		//echo 'G->.'.$id.': '.$proveedorAnt.' - '.$rubroAnt.' - '.$subrubroAnt.' - '.$tipoAnt.' / ';	
		if ($rs['estado']==0) {
			if (($tipoAnt!='')&&($tipoAnt!='gasto')) {
				$tipos=0;
			}
			$arrayIds[]=$rsGasto['id'];
			$tipoAnt='gasto';
			
			//echo $rsGasto['proveedor']." / ";
		 	$proveedor=getProveedor($rsGasto['proveedor']);
			
			
			if (($proveedorAnt!='')&&(strcasecmp($proveedorAnt, $proveedor) != 0)) {
	           	//echo $proveedorAnt.' - '.$proveedor." / ";
	        	$ok=0;
			            	
		   }
		   $proveedorAnt=$proveedor;
			//echo $proveedorAnt.' - '.$proveedor;	 	           
		   if (($rubroAnt!='')&&($rubroAnt!=$rsGasto['rubro_id'])) {
		        				
		        $ok=0;
				            	
			}
			$rubroAnt=$rsGasto['rubro_id'];
				           
		    if (($subrubroAnt!='')&&($subrubroAnt!=$rsGasto['subrubro_id'])) {
		    	$ok=0;
				            	
			}
			$subrubroAnt=$rsGasto['subrubro_id'];
		}
		else{
			$pendientes=0;
		}
	}
	
	$sqlCompra = "SELECT 
			cuenta_a_pagar.estado,compra.id,compra.proveedor,compra.rubro_id, compra.subrubro_id 
		FROM cuenta_a_pagar 
		INNER JOIN compra 
			ON cuenta_a_pagar.operacion_id=compra.id AND cuenta_a_pagar.operacion_tipo='compra'  
			
			 WHERE cuenta_a_pagar.id = $id";
	
	if($rsCompra = mysqli_fetch_array(mysqli_query($conn,$sqlCompra))){
		//echo 'C->.'.$id.': '.$proveedorAnt.' - '.$rubroAnt.' - '.$subrubroAnt.' - '.$tipoAnt.' / ';
		if ($rsCompra['estado']==0) {
			if (($tipoAnt!='')&&($tipoAnt!='compra')) {
				$tipos=0;
			}
			$arrayIds[]=$rsCompra['id'];
			
			$tipoAnt='compra';
			
			$proveedor=getProveedor($rsCompra['proveedor']);
			if (($proveedorAnt!='')&&(strcasecmp($proveedorAnt, $proveedor) != 0)) {
	            //echo $proveedorAnt.' - '.$proveedor;
	        	$ok=0;
			            	
		   }
		   $proveedorAnt=$proveedor;
			          
		   if (($rubroAnt!='')&&($rubroAnt!=$rsCompra['rubro_id'])) {
		        				
		        $ok=0;
				            	
			}
			$rubroAnt=$rsCompra['rubro_id'];
				           
		    if (($subrubroAnt!='')&&($subrubroAnt!=$rsCompra['subrubro_id'])) {
		    	$ok=0;
				            	
			}
			$subrubroAnt=$rsCompra['subrubro_id'];
			
			
		}
		else{
			$pendientes=0;
		}
	}
	
	
}

$result['iguales']=$ok;
$result['tipos']=$tipos;
$result['pendientes']=$pendientes;
$result['tipo']=$tipoAnt;
$result['ids']=$arrayIds;
//print_r($result);
echo json_encode( $result ); 
?>