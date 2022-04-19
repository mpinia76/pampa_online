<?php
include_once("config/db.php");
include_once("functions/fechasql.php");
	include_once("functions/date.php");
	/*include_once("functions/getProveedor.php");
	
	include_once("config/user.php");*/
//print_r($_POST);
$data 	= explode(",",$_POST['datos']);
if(is_array($data) and count($data)>1 ){

	$dataid = $_GET['dataid'];
	$cuentas_pendiente 	= $_POST['cuentas_pendiente'];
	$cuentas_id			= $_POST['cuentas_id'];
	$cuentas_operacion	= $_POST['cuentas_operacion'];
	$datos				= $_POST['datos'];
	$operacion_tipo		= $_POST['operacion_tipo'];
	$operacion_orden	= $_POST['operacion_orden'];
	
	if($_POST['forma_pago']=='n'){
		
		$log[] = 'Debe seleccionar al menos una forma de pago';
		
			
	}else{

		foreach($cuentas_pendiente as $cuenta_pendiente){
		
			$operacion_monto = $operacion_monto + $cuenta_pendiente;
			
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
	if($_POST['forma_pago']=='n'){
		
		$log[] = 'Debe seleccionar al menos una forma de pago';
		
			
	}else{
	
		$operacion_monto = $_POST['cuenta_pendiente'];
		include("functions/comprueba_pagos.php");
			
		if(!$procesa){
			$total_operacion = $operacion_monto+$monto_interes-$monto_descuento;
				
			if(bccomp($total_operacion,$monto_pagado) != 0){
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
				$log[] = "No se pudo procesar la operacion: $procesa TO: $total_operacion MP: $monto_pagado FE: $fecha_error ";
			}	
			
		}
	}
	
	
}
	
	header('Content-type: application/json');
	echo json_encode(array("logs" => $log));
	

?>