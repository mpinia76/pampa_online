<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
session_start();
$user_id = $_SESSION['userid'];

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");
include_once("config/user.php");

//indicar tabla a editar
$tabla = 'caja_movimiento';
if(($_POST['agregar'])){
	
	$fecha 		= fechasql($_POST['fecha']);
	$caja_id 	= $_POST['caja_id'];
	
	if($_POST['motivo_id'] != 'otro'){
		$detalle = "motivo_".$_POST['motivo_id'];
	}else{
		$detalle = $_POST['otro_motivo'];
	}
	
	$monto 		= $_POST['monto'];
	$descubierto = 1;
	if ($monto < 0) {
		$saldo_sql = "SELECT SUM(monto) as saldo FROM caja_movimiento WHERE caja_id=$caja_id";
		$saldo_rs = mysql_fetch_array(mysql_query($saldo_sql));
		$saldo = $saldo_rs['saldo'];
		
		if ($saldo<0) {
			$sql = "SELECT * FROM caja WHERE id = ".$caja_id;
			$rsTemp = mysql_query($sql);
			if($rs = mysql_fetch_array($rsTemp)){
				$descubierto = $rs['descubierto'];
			}
		}
		
	}
	
	
	
	if ($descubierto) {
		
		/*$sql = "SELECT MAX(fecha) AS fecha  
		FROM caja_sincronizada
		WHERE caja_id = ".$caja_id;
		
		$rs = mysql_fetch_array(mysql_query($sql));
		$fecha_sincronizacion = $rs['fecha'];
		
		$sql = "SELECT * 
		FROM caja
		WHERE id = ".$caja_id;
		
		$rs = mysql_fetch_array(mysql_query($sql));
		$sincronizacion = $rs['sincronizacion'];
		$dias_sincronizacion = $rs['dias_sincronizacion'];*/
		$sincronizada=1;
		
		/*if ($sincronizacion) {
			$fechaInicial = new DateTime($fecha_sincronizacion);
			$fechaActual = new DateTime($fecha); // la fecha del ordenador
		
			// Obtenemos la diferencia en milisegundos
			
			
			$interval = $fechaInicial->diff($fechaActual);
			$days = intval($interval->format('%R%a'));
			
			if ($days>$dias_sincronizacion) {
				$sincronizada=0;
			}
			
		}*/


		if($sincronizada){
			$time = time();

			$hora = date("H:i:s", $time);
		
			$fecha .=' '.$hora;	
			
			$insert = "INSERT INTO $tabla (fecha,caja_id,origen,monto,usuario_id) VALUES ('$fecha','$caja_id','$detalle','$monto',$user_id)";
			mysql_query($insert);
			
			$result = '1';
		}
		else{
			
			$result = "Por favor, realice la conciliacion y sincronice la caja sobre la que desea generar la operacion para continuar";
		}
	}
	else{
		
		$result = "La caja no tiene saldo suficiente y no permite Movimientos Descubiertos";
	}
	

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<script type="text/javascript" src="library/jquery/jquery-1.4.2.min.js"></script>

<!--JQuery Uploadify-->
<script type="text/javascript" src="library/uploadify/jquery.uploadify.v2.1.0.min.js"></script>
<script type="text/javascript" src="library/uploadify/swfobject.js"></script>
<link href="library/uploadify/uploadify.css" rel="stylesheet" type="text/css" />
<!--/JQuery Uploadify-->

<!--JQuery editor-->
<script type="text/javascript" src="library/jwysiwyg/jquery.wysiwyg.js"></script>
<link rel="stylesheet" href="library/jwysiwyg/jquery.wysiwyg.css" type="text/css" />
<!--/JQuery editor-->

<!--JQuery Date Picker-->
<script type="text/javascript" src="library/datepicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="library/datepicker/jquery.bgiframe.js"></script><![endif]-->
<script type="text/javascript" src="library/datepicker/jquery.datePicker.min-2.1.2.js"></script>
<link href="library/datepicker/datePicker.css" rel="stylesheet" type="text/css" />
<style>
a.dp-choose-date {
	float: left;
	width: 16px;
	height: 16px;
	padding: 0;
	margin: 5px 3px 0;
	display: block;
	text-indent: -2000px;
	overflow: hidden;
	background: url(images/calendar.png) no-repeat; 
}
a.dp-choose-date.dp-disabled {
	background-position: 0 -20px;
	cursor: default;
}
/* makes the input field shorter once the date picker code
 * has run (to allow space for the calendar icon
 */
input.dp-applied {
	width: 140px;
	float: left;
}
</style>
<!--/JQuery Date Picker-->

<script>

$(function(){

	$('.fecha').datePicker();

});
function consultarDescubierto (){
	
	var datos = ({
		'caja_id' : $('#efectivo_caja_id').val(),
	});
	
	$.ajax({
		beforeSend: function(){
			$('#loading').show();
		},
		data: datos,
		url: 'functions/consultarDescubierto.php',
		success: function(data) {
			
			if(data == 'no'){		
				alert('La caja de origen no tiene fondos suficientes');
				$('#origen').val(0);
			}
			$('#loading').hide();
			
		}
	});

}
function consultarSincronismo (){
	
	var datos = ({
		'caja_id' : $('#efectivo_caja_id').val(),
		'fecha' : $('#fecha').val()
	});
	
	$.ajax({
		beforeSend: function(){
			$('#loading').show();
		},
		data: datos,
		url: 'functions/consultarSincronismo.php',
		success: function(data) {
			
			if(data == 'no'){		
				alert('Por favor, realice la conciliacion y sincronice la caja sobre la que desea generar la operacion para continuar');
				$('#efectivo_caja_id').val(0);
			}
			$('#loading').hide();
			
		}
	});

}

function consultarSincronismoFecha (){

var datos = ({
	'caja_id' : $('#efectivo_caja_id').val(),
	'fecha' : $('#fecha').val()
});

$.ajax({
	beforeSend: function(){
		$('#loading').show();
	},
	data: datos,
	url: 'functions/consultarSincronismoFecha.php',
	success: function(data) {
		
		if(data == 'no'){		
			alert('Movimiento no permitido: La caja se encuentra conciliada y sincronizada para la fecha del movimiento que intenta realizar. Contactar administrador');
			$('#efectivo_caja_id').val(0);
		}
		$('#loading').hide();
		
	}
});

}

</script>

<script language="javascript" type="text/javascript">

function vacio(q) {

	//funcion que chequea que los campos no sean espacios en blanco

	for ( i = 0; i < q.length; i++ ) {

			if ( q.charAt(i) != " " ) {

					return true

			}

	}

return false

}
function valida(F) {
	
	
	if($('#otro_motivo').css('display') != 'none' && $('#otro_motivo').val() == ''){
		
		alert('Debe completar el detalle');
		$('#otro_motivo').focus();
		return false;
		
	}
	
	if(F.caja_id.value == 'null') {

	alert("Caja es obligatorio");

	F.caja_id.focus();

	return false

	}

	if(F.motivo_id.value == 'null') {

	alert("Detalle es obligatorio");

	F.motivo_id.focus();

	return false

	}
	
	if(F.motivo_id.value == 'otro' && $('#otro_motivo').val() == '' ) {
	
	alert("Detalle es obligatorio");
	
	return false
	
	}
	
	if(vacio(F.monto.value) == false) {

	alert("Monto es obligatorio");

	F.monto.focus();

	return false

		

	}
	$('#agregarSubmit').val('Procesando...');
	$('#agregarSubmit').attr('disabled','disabled');
	$('#agregar').val('1');
	}
</script>

<link href="styles/form2.css" rel="stylesheet" type="text/css" />
</head>

<body>

<?php include_once("config/messages.php"); ?>

<div class="container">

<form method="POST" name="form" action="cajas_detalle.am.php" onSubmit="return valida(this);">
<input name="agregar" id="agregar" type="hidden" value="0">
<div class="label"></div><div class="content"><input  size="" type="hidden" value="" name="id" /></div><div style="clear:both;"></div>
<div class="label">Fecha</div><div class="content"><input type="text" class="fecha dp-applied" value="<?php echo date("d/m/Y")?>" name="fecha" id="fecha" /></div><div style="clear:both;"></div>
<div class="label">Caja</div>
<div class="content">
	<select name="caja_id" id="efectivo_caja_id" onChange="consultarSincronismo();consultarSincronismoFecha();">
	<option value="0">Seleccionar...</option>
	<?php 
	$sql = "SELECT id,caja FROM caja INNER JOIN usuario_caja ON usuario_caja.caja_id = caja.id AND usuario_caja.usuario_id = $user_id ";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){ ?>
	?>
	<option  value="<?php echo $rs['id']?>"><?php echo $rs['caja']?></option>
	<?php } ?>
	</select>
</div>
<div style="clear:both;"></div>
<div class="label">Detalle</div>
<div class="content">
	<select name="motivo_id" onchange="if($(this).val() == 'otro'){ $('#otro_motivo').show(); }else{ $('#otro_motivo').hide(); } ">
	<option value="null">Seleccionar...</option>
	<?php 
	$sql = "SELECT * FROM motivo WHERE motivo_grupo_id = 1 ORDER BY nombre ASC";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){ ?>
	?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['nombre']?></option>
	<?php } ?>
	<?php if(ACCION_42){ ?>
	<option value="otro">Otro</option> 
	<?php } ?>
	</select><br />	
	<input id="otro_motivo" style="display:none; " type="text" name="otro_motivo" value="" />
	
</div>
<div style="clear:both;"></div>
<div class="label">Monto</div><div class="content"><input  size="5" type="text" value="" name="monto" id="monto" /></div><div style="clear:both;"></div>

<p align="center"><input type="submit" value="Agregar movimiento" name="agregarSubmit" id="agregarSubmit" /></p>

</form>

</div>

</body>
</html>
