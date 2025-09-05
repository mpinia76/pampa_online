<?php

include_once("functions/fechasql.php");
include_once("functions/date.php");
include_once("functions/getProveedor.php");
include_once("config/db.php");
include_once("config/user.php");

if(isset($_POST['guardar'])){ //guardo los datos extras del gasto

	$cuentas_pendiente 	= $_POST['cuentas_pendiente'];
	$cuentas_id			= $_POST['cuentas_id'];
	$cuentas_operacion	= $_POST['cuentas_operacion'];
	$datos				= $_POST['datos'];
	$operacion_tipo		= $_POST['operacion_tipo'];
	$operacion_orden	= $_POST['operacion_orden'];
	
	if($_POST['forma_pago']=='n'){
		
		$result = 'Debe seleccionar al menos una forma de pago';
		$dataid = $_POST['datos'];
			
	}else{

		foreach($cuentas_pendiente as $cuenta_pendiente){
		
			$operacion_monto = $operacion_monto + $cuenta_pendiente;
			
		}
	
		include("functions/comprueba_pagos.php");
		
		if($procesa){
		
			for($i=0; $i<count($cuentas_id); $i++){
				
				$operacion_id[] = $cuentas_operacion[$i];
			}
			
			include("functions/procesa_pagos.php");
			if (!$error) {
				for($i=0; $i<count($cuentas_id); $i++){
					$result = 1;
					$sql = "UPDATE cuenta_a_pagar SET estado=1,fecha_pago=NOW() WHERE id=".$cuentas_id[$i];
					mysqli_query($conn,$sql);
					
					$sql = "UPDATE $operacion_tipo SET nro_orden='".$operacion_orden[0]."' WHERE id=".$cuentas_operacion[$i];
					//echo $sql;
					mysqli_query($conn,$sql);
				}
			}
			
		}else{
		
			if(($operacion_monto+$monto_interes-$monto_descuento) != $monto_pagado){
				$result = 'Verifique que monto original ('.$operacion_monto.') mas los intereses ('.$monto_interes.') menos los descuentos ('.$monto_descuento.') sea igual al valor que intenta pagar ('.$monto_pagado.')';
			}elseif($fecha_error != 0){
				$result = 'La fecha ingresada no es correcta en alguna de las formas de pago';
			}elseif($error_cheque == true){
				$result = 'Debe completar el titular del cheque';
			}elseif($error_cheque_numero == true){
				$result = 'Ya existe un cheque del banco seleccionado y el numero ingresado';
			}elseif($fecha_hoy == false){
				$result = 'Le fecha de pago no puede ser posterior a hoy';					
			}else{
				$result = 'No se pudo procesar la operacion';
			}			
			$dataid = $_POST['datos'];
			
		}
	
	}

}


if(isset($dataid)){
	$sql 		= "SELECT * FROM cuenta_a_pagar WHERE id IN ( $dataid )";
	$rsTemp = mysqli_query($conn,$sql);
	
	while ($rs_cuenta = mysqli_fetch_array($rsTemp)){
		$tablas[]  								= $rs_cuenta['operacion_tipo'];
		$ids[]									= $rs_cuenta['operacion_id'];
		$estados[]								= $rs_cuenta['estado'];
		$monto[$rs_cuenta['operacion_id']] 		= $rs_cuenta['monto'];
		$cuenta_id[$rs_cuenta['operacion_id']] 	= $rs_cuenta['id'];
	}
	
	$tabla = $tablas[count($tablas)-1];

	foreach($tablas as $key=>$value){
		if($tabla != $value){
			$result = "No se pueden seleccionar operaciones de distinto tipo";
			$error = true;
			break;
		}else{
			$error = false;
		}
	}
	
	if(!$error){
		$ids = implode(",",$ids);
		
		$sql = "SELECT usuario.nombre,usuario.apellido,$tabla.*,subrubro.subrubro,rubro.rubro FROM $tabla LEFT JOIN subrubro ON $tabla.subrubro_id=subrubro.id INNER JOIN usuario ON $tabla.user_id=usuario.id INNER JOIN rubro ON $tabla.rubro_id=rubro.id WHERE $tabla.id IN ($ids)";
		//echo $sql;
		
		$rsTemp = mysqli_query($conn,$sql);
		
		$total = mysqli_num_rows($rsTemp);
		$registros = explode(",",$dataid); 
		$registros = count($registros);
		
		$operacion_tipo = $tabla;
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="styles/form.css" rel="stylesheet" type="text/css" />
<script src="library/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>

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

.ui-autocomplete {
	max-height: 100px;
	overflow-y: auto;
}

</style>

<script type="text/javascript">
function addFormaDePago(forma_pago_id){

	var datos = ({
		'forma_pago' : forma_pago_id,
		'pago' : 1
	});
	
	$.ajax({
		beforeSend: function(){
			$('#forma_pago_loading').show();
		},
		data: datos,
		url: 'functions/formadepago.php',
		success: function(data) {
			$('#forma_pago_loading').hide();
			$('#forma_de_pago').append(data);
			$('.date-pick').datePicker().trigger('change');
		}
	});
}

$(document).ready( function() {   // Esta parte del c�digo se ejecutar� autom�ticamente cuando la p�gina est� lista.
    $("#agregarSubmit").click( function() {     // Con esto establecemos la acci�n por defecto de nuestro bot�n de enviar.
        if(validaForm()){      
	       
        }
    }); 
   
});
function validaForm() {
	$('#mensaje').html('');
	$('#mensaje').hide();
	$.ajax({
	
		type : 'POST',
		data: $("#idForm").serialize(),
		url: 'controlar_abono_cuentas_pagar.php',
		success: function(data){
			
			if(data.logs){
				for(var x = 0; x < data.logs.length; x++){
					$('#mensaje').append(data.logs[x]+'<br />');
				}
				$('#mensaje').show();
				$('html,body').animate({
				    scrollTop: $("#mensaje").offset().top
				}, 200);
				return false;
			}else{
				$('#agregarSubmit').val('Procesando...');
				$('#agregarSubmit').attr('disabled','disabled');
				$('#guardar').val('1');
				$("#idForm").submit();
			}
			
		}
	});
	
	
	
}
</script>

</head>

<body>

<?php 
if(isset($_POST['datos']) and $result == 1){ 
	include_once("config/messages.php");
?>
	<script>
	var dhxWins = parent.dhxWins;
	dhxWins.window('w_cuenta_a_pagar').attachURL('cuentas_pagar.php');
	</script>
<?php
}elseif($total != $registros){ 
	$result = 'No se pueden abonar los registros seleccionados, verifique los estados';
	include_once("config/messages.php");
}else{
	include_once("config/messages.php");
?>
<div id="mensaje" class="error" style="display:none"></div> 
<div class="formContainer">
<form method="post" id="idForm" name="form" action="cuentas_pagar.view.php">
<input name="guardar" id="guardar" type="hidden" value="0">
<input type="hidden" name="datos" value="<?php echo $_GET['dataid']?>" />
<input type="hidden" name="operacion_tipo" value="<?php echo $operacion_tipo?>"  />
	<fieldset>
		<legend>Detalle de <?php echo $operacion_tipo?></legend> 
		<ul class="form">

		<?php while($rs = mysqli_fetch_array($rsTemp)){ ?>
			<input type="hidden" name="cuentas_id[]" value="<?php echo $cuenta_id[$rs['id']]?>" />
			<input type="hidden" name="cuentas_pendiente[]" value="<?php echo $monto[$rs['id']]?>"  />
			<input type="hidden" name="cuentas_operacion[]" value="<?php echo $rs['id']?>" />
			<input type="hidden" name="operacion_orden[]" value="<?php echo $rs['nro_orden']?>" />
			<li><label><strong>Responsable:</strong></label><?php echo $rs['nombre']?> <?php echo $rs['apellido']?></li>
			<li><label>Fecha:</label><?php echo fechavista($rs['fecha'])?></li>
			<li><label>Rubro:</label><?php echo $rs['rubro']?></li>
			<li><label>Sububro:</label><?php echo $rs['subrubro']?></li>
			<li><label>Proveedor:</label><?php echo getProveedor($rs['proveedor'])?></li>
			<li><label>Descripcion:</label><?php echo $rs['descripcion']?></li>
			<li><label>Monto de la operacion:</label>$<?php echo $rs['monto']?></li>
			<li><label>Monto pendiente:</label>$<?php echo $monto[$rs['id']]?></li>
            <?php $total_abonar = $total_abonar + $monto[$rs['id']]; ?>
		<?php } ?>
			<li><label>Monto total pendiente:</label>$<?php echo $total_abonar?></li>
			<li><label>Forma de pago:</label>
			<select name="forma_pago">
			<option value="n">Seleccionar...</option>
			<?php
			$sql = "SELECT id,forma_pago FROM forma_pago WHERE id != 5 ORDER BY forma_pago ";
			$rsTemp = mysqli_query($conn,$sql);
			while($rs = mysqli_fetch_array($rsTemp)){?>
			<option value="<?php echo $rs['id']?>"><?php echo $rs['forma_pago']?></option>
			<?php } ?>
			</select> &nbsp; <a style="cursor:pointer;" onclick="addFormaDePago(form.forma_pago.options[form.forma_pago.selectedIndex].value)">agregar</a> <img id="forma_pago_loading" src="images/loading.gif" style="display:none" /></li>
			<div id="forma_de_pago"></div>
		</ul>
	</fieldset> 
	<p align="center"><input type="button" value="Guardar datos" name="agregarSubmit" id="agregarSubmit" /></p> 
</form>
<?php } ?>
</div> 
</body>
</html>