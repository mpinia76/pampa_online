<?php
//COMPRUEBA LOS MONTOS INGRESADOS

$monto_pagado = 0;
$monto_descuento = 0;
$monto_interes = 0;
$error_cheque_numero = false;
$error_cheque = false;

$fecha = array();
$fecha_hoy = true;
include_once("functions/util.php");
//proceso los pagos de cuentas a pagar
if(isset($_POST['cuenta'])){

	$cuenta = $_POST['cuenta'];
	$cuenta_monto = $_POST['cuenta_monto'];
	
	foreach($cuenta as $key=>$value){
		$monto_pagado = $monto_pagado + $cuenta_monto[$key];
	}
}

//proceso los pagos de cheques pendientes de acreditar
if(isset($_POST['cheque_acreditar'])){

	$cheques_acreditar = $_POST['cheque_acreditar'];
	$cheques_acreditar_monto = $_POST['cheque_acreditar_monto'];
	
	foreach($cheques_acreditar as $key=>$value){
                        if($cheques_acreditar_monto[$key] != '' and $cheques_acreditar_monto[$key] > 0){
                            $monto_pagado = $monto_pagado + $cheques_acreditar_monto[$key];
                        }
	}
}

//proceso los pagos en efectivo
if(isset($_POST['efectivo'])){

	$efectivo 			= $_POST['efectivo'];
	$efectivo_monto 	= $_POST['efectivo_monto'];
	$efectivo_fecha		= $_POST['efectivo_fecha'];
	$efectivo_descuento = $_POST['efectivo_descuento'];
	$efectivo_interes 	= $_POST['efectivo_interes'];
	
	foreach($efectivo as $key=>$value){
		$fecha[] = is_date($efectivo_fecha[$key]);
		$monto_pagado = $monto_pagado + $efectivo_monto[$key];
		$monto_descuento = $monto_descuento + $efectivo_descuento[$key];
		$monto_interes = $monto_interes + $efectivo_interes[$key];
		
		if(strtotime(fechasql($efectivo_fecha[$key])) > (time()+(2*60*60)) ){
			$fecha_hoy = false;
		}
	}
}

//proceso los pagos con tarjeta
if(isset($_POST['tarjeta'])){
	
	$tarjeta 			= $_POST['tarjeta'];
	$tarjeta_monto 		= $_POST['tarjeta_monto'];
	$tarjeta_fecha		= $_POST['tarjeta_fecha'];
	$tarjeta_descuento  = $_POST['tarjeta_descuento'];
	$tarjeta_interes	= $_POST['tarjeta_interes'];
	
	foreach($tarjeta as $key=>$valor){
		$fecha[] = is_date($tarjeta_fecha[$key]);
		$monto_pagado = $monto_pagado + $tarjeta_monto[$key];
		$monto_descuento = $monto_descuento + $tarjeta_descuento[$key];
		$monto_interes = $monto_interes + $tarjeta_interes[$key];
		
		if(strtotime(fechasql($tarjeta_fecha[$key])) > (time()+(2*60*60)) ){
			$fecha_hoy = false;
		}
	}
}

//proceso los pagos con cheque
if(isset($_POST['cheque'])){
	
	$cheque 			= $_POST['cheque'];
	$cheque_numero		= $_POST['cheque_numero'];
	$cheque_cuenta		= $_POST['cheque_cuenta_id'];
	$cheque_monto 		= $_POST['cheque_monto'];
	$cheque_fecha		= $_POST['cheque_fecha'];
	$cheque_titular		= $_POST['cheque_titular'];
	$cheque_descuento	= $_POST['cheque_descuento'];
	$cheque_interes		= $_POST['cheque_interes'];
	
	$chequera_cheque_id 		= $_POST['chequera_cheque_id'];
	$cheques = array();
	//_log(print_r($_POST));
	foreach($cheque as $key=>$value){
		$clave = array_search($cheque_numero[$key], $cheques);
		//echo $clave."==".$cheque_cuenta[$key]."<br>";
		if($clave==$cheque_cuenta[$key]){
			$error_cheque_numero = true;
		}
		$cheques[$cheque_cuenta[$key]]=$cheque_numero[$key];
		//verifico si ya no existe un cheque con esa info
		$sql = "SELECT * FROM cheque_consumo WHERE numero = '".intval($cheque_numero[$key])."' AND cuenta_id = '".$cheque_cuenta[$key]."' AND chequera_id = '".$chequera_cheque_id[$key]."'";
		//echo $sql."<br>";
		$rsTemp = mysqli_query($conn,$sql);
		if(mysqli_num_rows($rsTemp)>0){
			$error_cheque_numero = true;
		}
		
		if($cheque_titular[$key] == ''){
			$error_cheque = true;
		}
		
		$fecha[] = is_date($cheque_fecha[$key]);
		
		$monto_pagado = $monto_pagado + $cheque_monto[$key];
		$monto_descuento = $monto_descuento + $cheque_descuento[$key];
		$monto_interes = $monto_interes + $cheque_interes[$key];
	}
}

//proceso los pagos con transferencia
if(isset($_POST['transferencia'])){

	$transferencia 		 		= $_POST['transferencia'];
	$transferencia_monto 		= $_POST['transferencia_monto'];
	$transferencia_fecha 		= $_POST['transferencia_fecha'];
	$transferencia_descuento 	= $_POST['transferencia_descuento'];
	$transferencia_interes		= $_POST['transferencia_interes'];
	
	foreach($transferencia as $key=>$value){
		$fecha[] = is_date($transferencia_fecha[$key]);
		$monto_pagado = $monto_pagado + $transferencia_monto[$key];
		$monto_descuento = $monto_descuento + $transferencia_descuento[$key];
		$monto_interes = $monto_interes + $transferencia_interes[$key];
	}
}

if(isset($_POST['debito'])){

	$debito 		 	= $_POST['debito'];
	$debito_fecha 		= $_POST['debito_fecha'];
	$debito_monto 		= $_POST['debito_monto'];
	$debito_descuento 	= $_POST['debito_descuento'];
	$debito_interes		= $_POST['debito_interes'];
	
	
	
	foreach($debito as $key=>$value){
		$fecha[] = is_date($debito_fecha[$key]);
		$monto_pagado = $monto_pagado + $debito_monto[$key];
		$monto_descuento = $monto_descuento + $debito_descuento[$key];
		$monto_interes = $monto_interes + $debito_interes[$key];
		if(strtotime(fechasql($debito_fecha[$key])) > (time()+(2*60*60)) ){
			$fecha_hoy = false;
		}
	}
}


//comprueba que todas las fechas cargadas sean  correctas
$fecha_error = 0;
foreach($fecha as $clave=>$valor){

	if(!$valor){ $fecha_error++; };
	
}

$monto_interes = round($monto_interes,2);
$monto_pagado = round($monto_pagado,2);
$monto_descuento = round($monto_descuento,2);
$operacion_monto = round($operacion_monto,2);
$total_operacion = $operacion_monto+$monto_interes-$monto_descuento;

if(     
        (bccomp($total_operacion,$monto_pagado) == 0) 
        and $fecha_error == 0 
        and $error_cheque == false 
        and $error_cheque_numero == false 
        and $fecha_hoy
){ 
        $procesa = true; 
}
	