<?php
include_once("functions/fechasql.php");
include_once("functions/date.php");
include_once("functions/getProveedor.php");
include_once("config/db.php");
include_once("config/user.php");

if(isset($_POST['guardar'])){ //guardo los datos extras del compra
	
	$facturas 		= $_POST['factura_nro'];
	$facturas_tipo 	= $_POST['factura_tipo'];
	$facturas_orden	= $_POST['factura_orden'];
	$remitos_nro	= $_POST['remito_nro'];
	$recibos_nro	= $_POST['recibo_nro'];
	$compras_id 	= $_POST['compra_id'];
	$compras_orden	= $_POST['compra_nro_orden'];
	$compras_monto	= $_POST['compra_monto']; 
	
	for($i=0; $i<count($compras_id); $i++){
	
		if( ($facturas[$i] != '' and $facturas_tipo[$i] != 'n') or $remitos_nro[$i] != '' or $recibos_nro[$i] != '' ){
		
			$recibos = 1;
		}else{
		
			$recibos = 0;
		}
		
	}

	if($recibos == 0){

		$result = 'No se guardo, debe completar con un n&uacute;mero de recibo, remito o factura';
		$dataid = $_POST['datos'];
		
	}elseif($_POST['forma_pago']=='n'){
	
		$result = 'Debe seleccionar al menos una forma de pago';
		$dataid = $_POST['datos'];
		
	}else{
	
		foreach($compras_monto as $compra_monto){
		
			$operacion_monto = $operacion_monto + $compra_monto;
			
		}
		for($i=0; $i<count($compras_id); $i++){

			$operacion_id[] = $compras_id[$i];
			$operacion_tipo = 'compra';

		}
		
		include("functions/comprueba_pagos.php");
		
		if($procesa){
			
			
				include("functions/procesa_pagos.php");	
				echo $error;
				if($error){
					$result = "No se pudo abonar la orden";
				}else{
					for($i=0; $i<count($compras_id); $i++){
						$result = 1;
						$sql = "UPDATE compra SET 
									estado=1,
									nro_orden='".$compras_orden[0]."',
									factura_nro='".$facturas[$i]."',
									factura_tipo='".$facturas_tipo[$i]."',
									factura_orden='".$facturas_orden[$i]."',
									remito_nro='".$remitos_nro[$i]."',
									recibo_nro='".$recibos_nro[$i]."'
								WHERE id=".$compras_id[$i];
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


	$sql = "SELECT usuario.nombre,usuario.apellido,compra.*,subrubro.subrubro,subrubro.id as subrubro_id,rubro.rubro,rubro.id as rubro_id FROM compra LEFT JOIN subrubro ON compra.subrubro_id=subrubro.id INNER JOIN usuario ON compra.user_id=usuario.id INNER JOIN rubro ON compra.rubro_id=rubro.id WHERE compra.id IN (".$dataid.") AND compra.estado = '0' AND compra.nro_orden != '0' ";
	$rsTemp = mysqli_query($conn,$sql);

	$total = mysqli_num_rows($rsTemp);
	$registros = explode(",",$dataid);
	$registros = count($registros);
	
	$operacion_tipo = 'compra';

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
</style>
<script>
$(function()
{
	$('.date-pick').datePicker().trigger('change');
});

function createCombo(tabla,campo_id,campo,value){

	var datos = ({
		'tabla' : tabla,
		'campo_id' : campo_id,
		'campo' : campo,
		'value' : value
	});
	
	$.ajax({
		beforeSend: function(){
			$('#combo_loading').show();
		},
		data: datos,
		url: 'functions/createcombo.php',
		success: function(data) {
			$('#combo_loading').hide();
			$('#subrubro_combo').html(data);
			$('#subrubro').show();
		}
	});
}
</script>
<script type="text/javascript">
function addFormaDePago(forma_pago_id){

	var datos = ({
		'forma_pago' : forma_pago_id,
		'pago':1
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
		url: 'controlar_abono_compra.php',
		success: function(data){
			
			if(data.logs){
				for(var x = 0; x < data.logs.length; x++){
					$('#mensaje').append(data.logs[x]+'<br />');
				}
				$('#mensaje').show();
				$('html,body').animate({
				    scrollTop: $("#mensaje").offset().top
				}, 200);return false;
			}else{
				$('#guardar').val('1');
				$('#agregarSubmit').val('Procesando...');
				$('#agregarSubmit').attr('disabled','disabled');
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
	dhxWins.window('w_compra').attachURL('compras.php');
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
	<form method="post" id="idForm" name="form" action="compras.view.php?dataid=<?php echo $dataid?>">
	<input name="guardar" id="guardar" type="hidden" value="0">
	<input type="hidden" name="datos" value="<?php echo $_GET['dataid']?>" />
		<fieldset>
			<legend>Detalle de los compras seleccionados</legend> 
			<ul class="form">
			<?php  while($rs = mysqli_fetch_array($rsTemp)){ ?>
				<input type="hidden" name="compra_nro_orden[]" value="<?php echo $rs['nro_orden']?>" />
				<input type="hidden" name="compra_id[]" value="<?php echo $rs['id']?>" />
				<input type="hidden" name="compra_monto[]" value="<?php echo $rs['monto']?>" />
				<li><label>Estado:</label>
				<span style="background:#FFFF99;">
				<?php  if($rs['estado'] == 0 and $rs['nro_orden'] == 0){ ?>
					<?php  $subestado = 1; ?>
					Pendiente de autorizaci&oacute;n
				<?php  }elseif($rs['estado'] == 0 and $rs['nro_orden'] != 0){ ?>
					<?php  $subestado = 2; ?>
					compra autorizado, pendiente de pago
				<?php  }elseif($rs['estado'] == 1 and $rs['nro_orden'] != 0 and $rs['factura_nro'] == ''){ ?>
					<?php  $subestado = 3; ?>
					compra autorizado, abonado, falta numero de factura 
				<?php  }elseif($rs['estado'] == 1 and $rs['nro_orden'] != 0 and $rs['factura_nro'] != ''){ ?>
					<?php  $subestado = 4; ?>
					compra autorizado, abonado, con numero de factura
				<?php  }elseif($rs['estado'] == 2){ ?>
					<?php  $subestado = 0; ?>
					compra no autorizado
				<?php  } ?>
				</span>
				</li>
				<li><label>Responsable:</label><?php echo $rs['nombre']?> <?php echo $rs['apellido']?></li>
				<li><label>Fecha devengado:</label><?php echo fechavista($rs['fecha'])?></li>
				<!--  <input type="hidden" name="fecha" value="<?php echo fechavista($rs['fecha'])?>" />-->
				<li><label>Rubro:</label><?php echo $rs['rubro']?></li>
				<!--<input type="hidden" name="rubro" value="<?php echo $rs['rubro_id']?>" />-->
				<li><label>Sububro:</label><?php echo $rs['subrubro']?></li>
				<!--<input type="hidden" name="subrubro_id" value="<?php echo $rs['subrubro_id']?>" />-->
				<li><label>Proveedor:</label><?php echo getProveedor($rs['proveedor'])?></li>
				<!--<input type="hidden" name="proveedor" value="<?php echo getProveedor($rs['proveedor'])?>" />-->
				<li><label>Descripcion:</label><?php echo $rs['descripcion']?></li>
				<!--<input type="hidden" name="descripcion" value="<?php echo $rs['descripcion']?>" />-->
				<li><label>Monto neto:</label>$<?php echo $rs['monto']?></li>
				<?php $total_abonar = $total_abonar +  $rs['monto']; ?>
				<li><label>Numero de remito:</label><input type="text" name="remito_nro[]" value="<?php echo $rs['remito_nro']?>" /></li>
				<li><label>Numero de recibo:</label><input type="text" name="recibo_nro[]" value="<?php echo $rs['recibo_nro']?>" /></li>
				<li><label>Numero de factura:</label>
				<select size="1" name="factura_tipo[]">
					<option value="n">Tipo</option>
					<option value="A" <?php if($rs['factura_tipo'] == "A"){ ?> selected="selected" <?php } ?>>A</option>
					<option value="B" <?php if($rs['factura_tipo'] == "B"){ ?> selected="selected" <?php } ?>>B</option>
					<option value="C" <?php if($rs['factura_tipo'] == "C"){ ?> selected="selected" <?php } ?>>C</option>
				</select> 
				<select size="1" name="factura_orden[]">
					<option value="B" <?php if($rs['factura_orden'] == "B"){ ?> selected="selected" <?php } ?>>0001</option>
					<option value="N" <?php if($rs['factura_orden'] == "N"){ ?> selected="selected" <?php } ?>>0002</option>
				</select> 
				<input type="text" name="factura_nro[]" value="<?php echo $rs['factura_nro']?>"/></li>
				<li><label>&nbsp;</label></li>
			<?php  } ?>
				<li><label>Monto total pendiente:</label>$<?php echo $total_abonar?></li>
				<li><label>Forma de pago:</label>
				<select name="forma_pago">
				<option value="n">Seleccionar...</option>
				<?php 
				$sql = "SELECT id,forma_pago FROM forma_pago WHERE id != 5 ORDER BY forma_pago ";
				$rsTemp = mysqli_query($conn,$sql);
				while($rs = mysqli_fetch_array($rsTemp)){?>
				<option value="<?php echo $rs['id']?>"><?php echo $rs['forma_pago']?></option>
				<?php  } ?>
				</select> &nbsp; <a style="cursor:pointer;" onclick="addFormaDePago(form.forma_pago.options[form.forma_pago.selectedIndex].value)">agregar</a> <img id="forma_pago_loading" src="images/loading.gif" style="display:none" /></li>
				<div id="forma_de_pago"></div>
			</ul>
		</fieldset> 
		<p align="center"><input type="button" value="Abonar" name="agregarSubmit" id="agregarSubmit" /></p> 
	</form>
	</div>
<?php  } ?> 
</body>
</html>
