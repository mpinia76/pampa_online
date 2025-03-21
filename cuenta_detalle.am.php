<?php
session_start();
$user_id = $_SESSION['userid'];

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");
include_once("config/user.php");

//indicar tabla a editar
$tabla = 'cuenta_movimiento';

if(($_POST['agregar'])){
	
	$fecha 		= fechasql($_POST['fecha']);
	$cuenta_id 	= $_POST['cuenta_id'];
	
	if($_POST['motivo_id'] != 'otro'){
		$detalle = "motivo_".$_POST['motivo_id'];
	}else{
		$detalle = $_POST['otro_motivo'];
	}
	
	$monto 		= $_POST['monto'];
	
	$insert = "INSERT INTO $tabla (fecha,cuenta_id,origen,monto,usuario_id) VALUES ('$fecha','$cuenta_id','$detalle','$monto',$user_id)";
	mysql_query($insert);
	//echo $insert;
	$result = '1'.mysql_error();

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

	if(F.cuenta_id.value == 'null') {

	alert("Seleccione una cuenta");

	F.cuenta_id.focus();

	return false

	}

	if(F.motivo_id.value == 'null') {

	alert("El detalle es obligatorio");

	F.motivo_id.focus();

	return false

	}
	
	if(F.motivo_id.value == 'otro' && $('#otro_motivo').val() == '' ) {
	
	alert("El detalle es obligatorio");
	
	return false
	
	}
	
	if(vacio(F.monto.value) == false) {

	alert("El monto es obligatorio");

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

<form method="POST" name="form" action="cuenta_detalle.am.php" onSubmit="return valida(this);">
<input name="agregar" id="agregar" type="hidden" value="0">
<div class="label"></div><div class="content"><input  size="" type="hidden"  name="id" /></div><div style="clear:both;"></div>
<div class="label">Fecha</div><div class="content"><input type="text" class="fecha dp-applied" value="<?php echo date("d/m/Y")?>" name="fecha" /></div><div style="clear:both;"></div>
<div class="label">Cuenta</div>
<div class="content">
	<select name="cuenta_id">
	<option value="null">Seleccionar...</option>
	<?php
	$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN usuario_cuenta ON usuario_cuenta.cuenta_id = cuenta.id AND usuario_cuenta.usuario_id = $user_id ORDER BY banco.banco";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){ ?>
	?>
	<option  value="<?php echo $rs['id']?>"><?php echo $rs['banco']?> <?php echo $rs['sucursal']?> <?php echo $rs['cuenta_tipo']?> <?php echo $rs['nombre']?></option>
	<?php } ?>
	</select>
</div>
<div style="clear:both;"></div>
<div class="label">Detalle</div>
<div class="content">
	<select name="motivo_id" onchange="if($(this).val() == 'otro'){ $('#otro_motivo').show(); }else{ $('#otro_motivo').hide(); } ">
	<option value="null">Seleccionar...</option>
	<?php
	$sql = "SELECT * FROM motivo WHERE motivo_grupo_id = 2 ORDER BY nombre ASC";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){ ?>
	?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['nombre']?></option>
	<?php } ?>
	<?php if(ACCION_41){ ?>
	<option value="otro">Otro</option> 
	<?php } ?>
	</select><br />	
	<input id="otro_motivo" style="display:none; " type="text" name="otro_motivo" value="" />
</div>
<div style="clear:both;"></div>
<div class="label">Monto</div><div class="content"><input  size="5" type="text" value="" name="monto" /></div><div style="clear:both;"></div>

<p align="center"><input type="submit" value="Agregar movimiento" name="agregarSubmit" id="agregarSubmit" /></p>

</form>

</div>

</body>
</html>
