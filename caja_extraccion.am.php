<?php 
date_default_timezone_set('America/Argentina/Buenos_Aires');
//DATOS DEL USUARIO
session_start();
$user_id = $_SESSION['userid'];

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");

if(($_POST['agregar'])){
	
	$time = time();

	$hora = date("H:i:s", $time);
	
	$fecha =fechasql($_POST['fecha']).' '.$hora;
	
	//proceso la salida de la caja
	$sql_entra = "INSERT INTO caja_movimiento (fecha,origen,caja_id,monto,usuario_id) 
				VALUES 
				('".$fecha."','haciacuenta_".$_POST['cuenta_id']."','".$_POST['caja_id']."','-".$_POST['monto']."',$user_id)";
	mysql_query($sql_entra);
	
	echo mysql_error();
	//proceso la entrada de plata a la cuenta
	$sql_sale = "INSERT INTO cuenta_movimiento (fecha,origen,cuenta_id,monto,usuario_id) 
				VALUES 
				('".fechasql($_POST['fecha'])."','desdecaja_".$_POST['caja_id']."','".$_POST['cuenta_id']."','".$_POST['monto']."',$user_id)";
	mysql_query($sql_sale);
	
	$result = 1;
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
$(document).ready(function() {
	
	
	$( "#monto" ).change(function() {
		
		consultarDescubierto ($( "#origen" ).val());
    });
});
$(function(){

	$('.fecha').datePicker();

});
function consultarDescubierto (){
	
	var datos = ({
		'caja_id' : $('#efectivo_caja_id').val(),
		'monto':$( "#monto" ).val()
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
				$('#efectivo_caja_id').val(0);
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

	if(F.caja_id.value == '0') {

	alert("Seleccione una caja")

	F.caja_id.focus();

	return false

	}

	if(F.cuenta_id.value == 'null') {

	alert("Seleccione una cuenta")

	F.caja_id.focus();

	return false

	}

	if(vacio(F.monto.value) == false) {

	alert("Monto es obligatorio")

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

<?php  include_once("config/messages.php"); ?>

<div class="container">

<form method="POST" name="form" action="caja_extraccion.am.php" onSubmit="return valida(this);">
<input name="agregar" id="agregar" type="hidden" value="0">
<div class="label">Fecha</div><div class="content"><input type="text" class="fecha dp-applied" name="fecha" id="fecha" value="<?php echo date("d/m/Y")?>" /></div><div style="clear:both;"></div>

<div class="label">Caja origen</div>
<div class="content">
	<select name="caja_id" id="efectivo_caja_id" onChange="consultarDescubierto();consultarSincronismo();consultarSincronismoFecha();">
	<option value="0">Seleccionar...</option>
	<?php  $sql = "SELECT id,caja FROM caja INNER JOIN usuario_caja ON usuario_caja.caja_id = caja.id AND usuario_caja.usuario_id = $user_id ";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){ 
		
		
		?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['caja']?></option>
	<?php  } ?>
	</select>
</div>
<div style="clear:both;"></div>

<div class="label">Cuenta destino</div>
<div class="content">
	<select name="cuenta_id">
	<option value="null">Seleccionar...</option>
	<?php  $sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN usuario_cuenta ON usuario_cuenta.cuenta_id = cuenta.id AND usuario_cuenta.usuario_id = $user_id ORDER BY banco.banco";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){ ?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['banco']?> <?php echo $rs['sucursal']?> <?php echo $rs['cuenta_tipo']?> <?php echo $rs['nombre']?></option>
	<?php  } ?>
	</select>
</div>
<div style="clear:both;"></div>



<div class="label">Monto</div><div class="content"><input  size="5" type="text" value="" name="monto" id="monto" /></div><div style="clear:both;">

</div>

<p align="center"><input type="submit" value="Hacer deposito" name="agregarSubmit" id="agregarSubmit" /></p>

</form>

</div>


</body>
</html>
