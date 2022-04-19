<?php
include_once("config/db.php");
include_once("functions/fechasql.php");
	include_once("functions/date.php");
	/*include_once("functions/getProveedor.php");
	
	include_once("config/user.php");*/
//print_r($_POST);

$data 	= explode(",",$_POST['datos']);
if(is_array($data) and count($data)>1  ){

$dataid = $_GET['dataid'];
$facturas 		= $_POST['factura_nro'];
	$facturas_tipo 	= $_POST['factura_tipo'];
	$facturas_orden	= $_POST['factura_orden'];
	$remitos_nro	= $_POST['remito_nro'];
	$recibos_nro	= $_POST['recibo_nro'];
	$compras_id 	= $_POST['compra_id'];
	$compras_orden	= $_POST['compra_nro_orden'];
	$compras_monto	= $_POST['compra_monto']; 
	
	for($i=0; $i<count($compras_id); $i++){
	
		if( ($facturas[$i] != '' and $facturas_tipo[$i] != 'n') or $remitos_nro[$i] != '' or $recibos_nro[$i] != '' ){
		
			$recibos = 1;
		}else{
		
			$recibos = 0;
		}
		
	}

	if($recibos == 0){

		$log[] = 'Debe completar con un n&uacute;mero de recibo, remito o factura';
		
		
	}elseif($_POST['forma_pago']=='n'){
	
		$log[] = 'Debe seleccionar al menos una forma de pago';
		
		
	}else{
	
		foreach($compras_monto as $compra_monto){
		
			$operacion_monto = $operacion_monto + $compra_monto;
			
		}
		for($i=0; $i<count($compras_id); $i++){

			$operacion_id[] = $compras_id[$i];
			$operacion_tipo = 'compra';

		}
		
		include("functions/comprueba_pagos.php");
		
		if(!$procesa){
			if(($operacion_monto+$monto_interes-$monto_descuento) != $monto_pagado){
				$log[] = 'Verifique que monto original ('.$operacion_monto.') mas los intereses ('.$monto_interes.') menos los descuentos ('.$monto_descuento.') sea igual al valor que intenta pagar ('.$monto_pagado.')';
			}elseif($fecha_error != 0){
				$log[] = 'La fecha ingresada no es correcta en alguna de las formas de pago';
			}elseif($error_cheque == true){
				$log[] = 'Debe completar el titular del cheque';
			}elseif($error_cheque_numero == true){
				$log[] = 'Ya existe un cheque del banco seleccionado y el numero ingresado';				
			}elseif($fecha_hoy == false){
				$log[] = 'Le fecha de pago no puede ser posterior a hoy';				
			}else{
				$log[] = 'No se pudo procesar la operacion';
			}
		}
	}

}elseif(is_array($data) and count($data)==1){
	
	$facturas 		= $_POST['factura_nro'];
	$facturas_tipo 	= $_POST['factura_tipo'];
	$facturas_orden	= $_POST['factura_orden'];
	$remitos_nro	= $_POST['remito_nro'];
	$recibos_nro	= $_POST['recibo_nro'];
	$compras_id 	= $_POST['compra_id'];
	$compras_orden	= $_POST['compra_nro_orden'];
	$compras_monto	= $_POST['compra_monto']; 
	
	if($_POST['forma_pago']=='n'){
	
		$log[] = 'Debe seleccionar al menos una forma de pago';
		
		
	}elseif( ($_POST['factura_nro'] != '' and $_POST['factura_tipo'] != 'n') or $_POST['remito_nro'] != '' or $_POST['recibo_nro'] != '' ){
	
		/*foreach($compras_monto as $compra_monto){
		
			$operacion_monto = $operacion_monto + $compra_monto;
			
		}
		for($i=0; $i<count($compras_id); $i++){

			$operacion_id[] = $compras_id[$i];
			$operacion_tipo = 'compra';

		}*/
		
		$operacion_monto = $compras_monto;
		
		include("functions/comprueba_pagos.php");
		
		
		
		if(!$procesa){
			if(($operacion_monto+$monto_interes-$monto_descuento) != $monto_pagado){
					$log[] = 'Verifique que monto original ('.$operacion_monto.') mas los intereses ('.$monto_interes.') menos los descuentos ('.$monto_descuento.') sea igual al valor que intenta pagar ('.$monto_pagado.')';
				}elseif($fecha_error != 0){
					$log[] = 'La fecha ingresada no es correcta en alguna de las formas de pago';
				}elseif($error_cheque == true){
					$log[] = 'Debe completar el titular del cheque';
				}elseif($error_cheque_numero == true){
					$log[] = 'Ya existe un cheque del banco seleccionado y el numero ingresado';				
				}elseif($fecha_hoy == false){
					$log[] = 'Le fecha de pago no puede ser posterior a hoy';				
				}else{
					$log[] = 'No se pudo procesar la operacion';
				}
		}
	}
	else{
			$log[] = 'Debe completar con un n&uacute;mero de recibo, remito o factura';
		}
	
}
	
	header('Content-type: application/json');
	echo json_encode(array("logs" => $log));
	

?>