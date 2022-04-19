<?php
include_once("config/db.php");
include_once("functions/fechasql.php");
	include_once("functions/date.php");
	/*include_once("functions/getProveedor.php");
	
	include_once("config/user.php");*/
//print_r($_POST);
	if($_POST['forma_pago']=='n'){
		
		$log[] = 'Debe seleccionar al menos una forma de pago';
		
			
	
		
			
	}elseif(($_POST['descuentos']!='0')&&($_POST['motivo_descuentos']=='')){
		
		$log[] = 'Debe indicar el motivo del descuento';
		
			
	
		
			
	}else{
		
		$operacion_monto = $_POST['monto_pendiente']-$_POST['descuentos'];
                   
        include("functions/comprueba_pagos.php");
			
			
			if(!$procesa){
				
					
				if(($operacion_monto+$monto_interes-$monto_descuento) != $monto_pagado){
					$log[] = 'Verifique que el sueldo pendiente de pago ('.$operacion_monto.') coincida con el monto que intenta abonar ('.$monto_pagado.')';
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
	
	

	
	header('Content-type: application/json');
	echo json_encode(array("logs" => $log));
	

?>