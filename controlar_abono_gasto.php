<?php
include_once("config/db.php");
include_once("functions/fechasql.php");
	include_once("functions/date.php");
	/*include_once("functions/getProveedor.php");
	
	include_once("config/user.php");*/
//print_r($_POST);

	
	
	$facturas 		= $_POST['factura_nro'];
	$facturas_tipo 	= $_POST['factura_tipo'];
	$facturas_orden	= $_POST['factura_orden'];
	$remitos_nro	= $_POST['remito_nro'];
	$recibos_nro	= $_POST['recibo_nro'];
	$gastos_id 		= $_POST['gasto_id'];
	$gastos_orden	= $_POST['gasto_nro_orden'];
	$gastos_monto	= $_POST['gasto_monto']; 
	
	if($_POST['forma_pago']=='n'){
		
			$log[] = 'Debe seleccionar al menos una forma de pago';
			
		
	}elseif( ($_POST['factura_nro'] != '' and $_POST['factura_tipo'] != 'n') or $_POST['remito_nro'] != '' or $_POST['recibo_nro'] != '' ){
	
		$operacion_monto = $_POST['gasto_monto'];
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
			$log[] = 'debe completar con un n&uacute;mero de recibo, remito o factura';
		}

	
	header('Content-type: application/json');
	echo json_encode(array("logs" => $log));
	

?>