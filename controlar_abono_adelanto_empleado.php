<?php
include_once("config/db.php");
include_once("functions/fechasql.php");
	include_once("functions/date.php");
	/*include_once("functions/getProveedor.php");
	
	include_once("config/user.php");*/
//print_r($_POST);
	if($_POST['forma_pago']=='n'){
		
		$log[] = 'Debe seleccionar al menos una forma de pago';
		
			
	}elseif($_POST['monto']==''){
		
		$log[] = 'Debe completar con un monto';
		
			
	}elseif($_POST['ano']==''){
		
		$log[] = 'El año es obligatorio';
		
			
	}elseif($_POST['mes']=='n'){
		
		$log[] = 'Debe seleccionar un mes';
		
			
	
		
			
	}else{
		
		$fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
		$mesAnterior = $_POST['mes'];
		$fecha_entrada = strtotime("01-".$mesAnterior."-".$_POST['ano']." 00:00:00");
		
		
	
		
		if($fecha_actual<$fecha_entrada){
			$log[] = 'Mes y año no pueden ser posteriores a hoy';
		}
		else{
			$operacion_monto = $_POST['monto'];
			include("functions/comprueba_pagos.php");
			
			if(!$procesa){
				
					
				if(($operacion_monto+$monto_interes-$monto_descuento) != $monto_pagado){
						$log[] = 'Verifique que monto de adelanto ('.$operacion_monto.') sea igual al valor que intenta pagar ('.$monto_pagado.')';
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
	}
	
	

	
	header('Content-type: application/json');
	echo json_encode(array("logs" => $log));
	

?>