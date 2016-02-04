<?php
//PROCESA LOS DIFERENTES SISTEMAS DE PAGO
//@operacion_id (Array)
//@operacion_tipo


//proceso los pagos de cuentas a pagar
if(isset($_POST['cuenta'])){

	$cuenta = $_POST['cuenta'];
	$cuenta_monto = $_POST['cuenta_monto'];
	
	foreach($cuenta as $key=>$value){
		$sql = "INSERT INTO cuenta_a_pagar (operacion_tipo,operacion_id,monto,estado) VALUES 
			('".$operacion_tipo."',".$operacion_id[0].",".$cuenta_monto[$key].",0)";
		mysql_query($sql);
	}
}

//proceso los pagos de los cheques de terceros
if(isset($_POST['cheque_acreditar'])){
    $cheques_acreditar = $_POST['cheque_acreditar'];
    $cheques_acreditar_id = $_POST['cheque_acreditar_id'];
    $cheques_acreditar_monto = $_POST['cheque_acreditar_monto'];
    
    if(is_array($operacion_id) and count($operacion_id) > 0){
        foreach($cheques_acreditar as $key=>$value){
            //actualizo el estado del cheque
            $sql = "UPDATE cobro_cheques SET asociado_a_pagos = 1, asociado_a_pagos_fecha = NOW() WHERE id = ".$cheques_acreditar_id[$key];
            mysql_query($sql);
            
            //guardo la relacion de pago y operacion
            foreach($operacion_id as $clave=>$valor){
                $sql = "INSERT INTO `rel_pago_operacion` (`forma_pago_id`, `forma_pago`, `operacion_tipo`, `operacion_id`) VALUES
                        ($cheques_acreditar_id[$key], 'cheque_tercero', '$operacion_tipo', $valor)";
                mysql_query($sql);
            }
        }
    }
}

//proceso los pagos en efectivo
if(isset($_POST['efectivo'])){

	$efectivo 				= $_POST['efectivo'];
	$efectivo_caja_id 		= $_POST['efectivo_caja_id'];
	$efectivo_monto			= $_POST['efectivo_monto'];
	$efectivo_interes 		= $_POST['efectivo_interes'];
	$efectivo_descuento 	= $_POST['efectivo_descuento'];
	$efectivo_fecha			= $_POST['efectivo_fecha'];
	
	if( is_array($operacion_id) and count($operacion_id)>0 ){
		
		foreach($efectivo as $key=>$value){
	
			//agrego el registro de gasto de efectivo
			$sql = "INSERT INTO efectivo_consumo (caja_id,monto,interes,descuento,fecha) VALUES 
				(".$efectivo_caja_id[$key].",'".$efectivo_monto[$key]."','".$efectivo_interes[$key]."','".$efectivo_descuento[$key]."','".fechasql($efectivo_fecha[$key])."')";
			//_log($sql);
			mysql_query($sql);
			$registro_id = mysql_insert_id(); //numero id en el tipo de pago
			
			//guardo la relacion de pago y operacion
			foreach($operacion_id as $clave=>$valor){
				$sql = "INSERT INTO `rel_pago_operacion` (`forma_pago_id`, `forma_pago`, `operacion_tipo`, `operacion_id`) VALUES
					($registro_id, 'efectivo', '$operacion_tipo', $valor)";
				//_log($sql);
				mysql_query($sql);
			} 
				
			
			//resto el dinero de la caja correspondiente
			$interes 	= $efectivo_monto[$key]*$efectivo_interes[$key]/100;
			$descuento	= $efectivo_monto[$key]*$efectivo_descuento[$key]/100;
			//$efectivo	= $efectivo_monto[$key]+$interes-$descuento;
			$efectivo	= $efectivo_monto[$key];
			
			$sql = "INSERT INTO caja_movimiento (caja_id,origen,registro_id,monto,fecha) VALUES 
				(".$efectivo_caja_id[$key].",'efectivo_consumo',$registro_id,'-$efectivo','".fechasql($efectivo_fecha[$key])."')";
			//_log($sql);
			mysql_query($sql);
		
		}
		
	}else{
	
		$error = true;
		echo 	"Problemas al realizar al pago, comunicarse con el administrador. Adjuntar el siguiente error: <br>";
		echo	"Forma de pago: Efectivo <br>";
		echo	"Operacion: $operacion_tipo <br>";
		print_r($operacion_id);
	}
}

//proceso los pagos con tarjeta
if(isset($_POST['tarjeta'])){
	
	$tarjeta 			= $_POST['tarjeta'];
	$tarjeta_id 		= $_POST['tarjeta_tarjeta_id'];
	$tarjeta_monto 		= $_POST['tarjeta_monto'];
	$tarjeta_interes 	= $_POST['tarjeta_interes'];
	$tarjeta_descuento 	= $_POST['tarjeta_descuento'];
	$tarjeta_cuotas 	= $_POST['tarjeta_cuotas'];
	$tarjeta_fecha		= $_POST['tarjeta_fecha'];
	$tarjeta_comprobante = $_POST['tarjeta_comprobante'];
	
	if( is_array($operacion_id) and count($operacion_id)>0 ){
	
		foreach($tarjeta as $key=>$valor){
		
			$sql = "INSERT INTO tarjeta_consumo (tarjeta_id,monto,interes,descuento,cuotas,fecha,comprobante_nro) VALUES 
					(".$tarjeta_id[$key].",'".$tarjeta_monto[$key]."','".$tarjeta_interes[$key]."','".$tarjeta_descuento[$key]."','".$tarjeta_cuotas[$key]."','".fechasql($tarjeta_fecha[$key])."','".$tarjeta_comprobante[$key]."')";
			mysql_query($sql);
			
			$registro_id = mysql_insert_id(); //numero id en el tipo de pago
			
			//guardo la relacion de pago y operacion
			foreach($operacion_id as $clave=>$valor){
				$sql = "INSERT INTO `rel_pago_operacion` (`forma_pago_id`, `forma_pago`, `operacion_tipo`, `operacion_id`) VALUES
					($registro_id, 'tarjeta', '$operacion_tipo', $valor)";
				mysql_query($sql);
			} 
			
			//guaro las cuotas
			for($i=0;$i<$tarjeta_cuotas[$key];$i++){
				
				$nro_cuota	= $i+1;
				$fecha 		= dateAdd(fechasql($tarjeta_fecha[$key]),"Y-m-d",0,$i);
				$monto		= number_format($tarjeta_monto[$key]/$tarjeta_cuotas[$key], 2, '.', '');
				
				//$descuento	= round($tarjeta_descuento[$key]/$tarjeta_cuotas[$key],2);
				//$interes		= round($tarjeta_interes[$key]/$tarjeta_cuotas[$key],2);
				//$monto		= $monto-$descuento+$interes;
				
				$sql 		= "INSERT INTO tarjeta_consumo_cuota (fecha,tarjeta_consumo_id,nro_cuota,monto) VALUES ('$fecha',$registro_id,$nro_cuota,$monto)";
				mysql_query($sql);
				
			}
		}
	}else{
	
		$error = true;
		echo 	"Problemas al realizar al pago, comunicarse con el administrador. Adjuntar el siguiente error: <br>";
		echo	"Forma de pago: Tarjeta <br>";
		echo	"Operacion: $operacion_tipo <br>";
		print_r($operacion_id);
		
	}
}

//proceso los pagos con cheque
if(isset($_POST['cheque'])){
	
	$cheque 			= $_POST['cheque'];
	$cheque_cuenta_id 	= $_POST['cheque_cuenta_id'];
	$cheque_numero 		= $_POST['cheque_numero'];
	$cheque_titular 	= $_POST['cheque_titular'];
	$cheque_fecha 		= $_POST['cheque_fecha'];
	$cheque_monto 		= $_POST['cheque_monto'];
	$cheque_interes 	= $_POST['cheque_interes'];
	$cheque_descuento 	= $_POST['cheque_descuento'];
	
	if( is_array($operacion_id) and count($operacion_id)>0 ){
	
		foreach($cheque as $key=>$value){
		
			//guardo el registro de cheques
			$sql = "INSERT INTO cheque_consumo (numero,titular,fecha,monto,interes,descuento,cuenta_id) VALUES 
					('".$cheque_numero[$key]."','".$cheque_titular[$key]."','".fechasql($cheque_fecha[$key])."','".$cheque_monto[$key]."','".$cheque_interes[$key]."','".$cheque_descuento[$key]."','".$cheque_cuenta_id[$key]."')";
			mysql_query($sql);
			//_log($sql);
			$registro_id = mysql_insert_id(); //numero id en el tipo de pago
			
			//guardo la relacion de pago y operacion
			foreach($operacion_id as $clave=>$valor){
				$sql = "INSERT INTO `rel_pago_operacion` (`forma_pago_id`, `forma_pago`, `operacion_tipo`, `operacion_id`) VALUES
					($registro_id, 'cheque', '$operacion_tipo', $valor)";
				mysql_query($sql);
				//_log($sql);
			} 
			
		}
		
	}else{
	
		$error = true;
		echo 	"Problemas al realizar al pago, comunicarse con el administrador. Adjuntar el siguiente error: <br>";
		echo	"Forma de pago: Cheque <br>";
		echo	"Operacion: $operacion_tipo <br>";
		print_r($operacion_id);
		
	}
}

//proceso los pagos con transferencia
if(isset($_POST['transferencia'])){
	
	$transferencia 				= $_POST['transferencia'];
	$transferencia_cuenta_id 	= $_POST['transferencia_cuenta_id'];
	$transferencia_destino 		= $_POST['transferencia_cuenta_destino'];
	$transferencia_fecha 		= $_POST['transferencia_fecha'];
	$transferencia_monto 		= $_POST['transferencia_monto'];
	$transferencia_interes 		= $_POST['transferencia_interes'];
	$transferencia_descuento 	= $_POST['transferencia_descuento'];
	
	if( is_array($operacion_id) and count($operacion_id)>0 ){
	
		foreach($transferencia as $key=>$value){
		
			$sql = "INSERT INTO transferencia_consumo (cuenta_id,cuenta_destino,monto,interes,descuento,fecha) VALUES 
					('".$transferencia_cuenta_id[$key]."','".$transferencia_destino[$key]."','".$transferencia_monto[$key]."','".$transferencia_interes[$key]."','".$transferencia_descuento[$key]."','".fechasql($transferencia_fecha[$key])."')";
			
			mysql_query($sql);
			
			$registro_id = mysql_insert_id(); //numero id en el tipo de pago
			
			//guardo la relacion de pago y operacion
			foreach($operacion_id as $clave=>$valor){
				$sql = "INSERT INTO `rel_pago_operacion` (`forma_pago_id`, `forma_pago`, `operacion_tipo`, `operacion_id`) VALUES
					($registro_id, 'transferencia', '$operacion_tipo', $valor)";
				mysql_query($sql);
			} 
			
		}
		
	}else{
	
		$error = true;
		echo 	"Problemas al realizar al pago, comunicarse con el administrador. Adjuntar el siguiente error: <br>";
		echo	"Forma de pago: Transferencia <br>";
		echo	"Operacion: $operacion_tipo <br>";
		print_r($operacion_id);
		
	}
}

//proceso si hay un debito de la cuenta
if(isset($_POST['debito'])){

	$fecha				= $_POST['debito_fecha'];;
	$debito				= $_POST['debito'];
	$debito_cuenta_id 	= $_POST['debito_cuenta_id'];
	$debito_monto 		= $_POST['debito_monto'];
	$debito_interes 	= $_POST['debito_interes'];
	$debito_descuento 	= $_POST['debito_descuento'];
	$detalle			= "debitocuenta_".$operacion_id[0];
	
	if( is_array($operacion_id) and count($operacion_id)>0 ){
	
		foreach($debito as $key=>$value){
		
			$monto = $debito_monto[$key] + $debito_interes[$key] - $debito_descuento[$key];
			$insert = "INSERT INTO cuenta_movimiento (fecha,cuenta_id,origen,monto) VALUES ('".fechasql($fecha[$key])."','$debito_cuenta_id[$key]','$detalle','-$monto')";
			//echo $insert."<br>";
			mysql_query($insert);
		
			$registro_id = mysql_insert_id(); //numero id en el tipo de pago
			
			//guardo la relacion de pago y operacion
			foreach($operacion_id as $clave=>$valor){
				$sql = "INSERT INTO `rel_pago_operacion` (`forma_pago_id`, `forma_pago`, `operacion_tipo`, `operacion_id`) VALUES
					($registro_id, 'debito', '$operacion_tipo', $valor)";
				//echo $sql."<br>";
				mysql_query($sql);
			}
			
		}
		
	}else{
	
		$error = true;
		echo 	"Problemas al realizar al pago, comunicarse con el administrador. Adjuntar el siguiente error: <br>";
		echo	"Forma de pago: Debito en cuenta <br>";
		echo	"Operacion: $operacion_tipo <br>";
		print_r($operacion_id);
		
	}

}