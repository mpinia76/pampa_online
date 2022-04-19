<?php
include_once("config/db.php");
include_once("functions/fechasql.php");
	include_once("functions/date.php");
	/*include_once("functions/getProveedor.php");
	
	include_once("config/user.php");*/
//print_r($_POST);
	if($_POST['forma_pago']=='n'){
		
		$log[] = 'Debe seleccionar al menos una forma de pago';
		
			
	
		
			
	}else{
		
		$operacion_monto = $_POST['resumen_monto'];
                   
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
				}else{
					$log[] = 'No se pudo procesar la operacion';
				}
				
			}
		
	}
	
	

	
	header('Content-type: application/json');
	echo json_encode(array("logs" => $log));
	

?>