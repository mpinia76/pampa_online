<?php
include_once("functions/fechasql.php");
include_once("functions/date.php");
include_once("functions/getProveedor.php");
include_once("config/db.php");
	
$resumen_id = $_GET['resumen_id'];

if(isset($_POST['guardar'])){ //guardo los datos extras del gasto

	$operacion_monto = $_POST['resumen_monto'];
	include("functions/comprueba_pagos.php");
	
	if($procesa){
		
		$operacion_id[] = $_POST['resumen_id'];
		$operacion_tipo = 'tarjeta_resumen';
		
		include("functions/procesa_pagos.php");
		if (!$error) {
			$result = 1;
			$sql = "UPDATE tarjeta_resumen SET estado=1,fecha_pago=NOW(),monto=$monto_pagado WHERE id=".$_POST['resumen_id'];
			mysql_query($sql);
		}
		
		$resumen_id = $_POST['resumen_id'];
		
	}else{
	
		if(($operacion_monto+$monto_interes-$monto_descuento) != $monto_pagado){
				$result = 'Verifique que monto original ('.$operacion_monto.') mas los intereses ('.$monto_interes.') menos los descuentos ('.$monto_descuento.') sea igual al valor que intenta pagar ('.$monto_pagado.')';
		}elseif($fecha_error != 0){
			$result = 'La fecha ingresada no es correcta en alguna de las formas de pago';
		}elseif($error_cheque == true){
			$result = 'Debe completar el titular del cheque';
		}elseif($error_cheque_numero == true){
			$result = 'Ya existe un cheque del banco seleccionado y el numero ingresado';				
		}else{
			$result = 'No se pudo procesar la operacion';
		}			
		$resumen_id = $_POST['resumen_id'];
		
	}
}

//detalle del resumen
$sql = "SELECT * FROM tarjeta_resumen WHERE id=$resumen_id";
$resumen = mysql_fetch_array(mysql_query($sql));
$inicio = $resumen['inicio'];
$fin	= $resumen['fin'];
$tarjeta = $resumen['tarjeta_id'];

$sql = "SELECT 
			tarjeta_consumo_cuota.fecha,CONCAT(tarjeta_consumo_cuota.nro_cuota,'/',tarjeta_consumo.cuotas) AS cuotas,tarjeta_consumo_cuota.monto,'Gastos y compras' as operacion, gasto.nro_orden 
		FROM tarjeta_consumo_cuota 
		INNER JOIN tarjeta_consumo 
			ON tarjeta_consumo_cuota.tarjeta_consumo_id=tarjeta_consumo.id 
		INNER JOIN rel_pago_operacion 
			ON tarjeta_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago = 'tarjeta' 
		INNER JOIN gasto
			ON rel_pago_operacion.operacion_tipo = 'gasto' AND rel_pago_operacion.operacion_id=gasto.id
		WHERE tarjeta_consumo_cuota.fecha >= '$inicio' and tarjeta_consumo_cuota.fecha <= '$fin' AND tarjeta_consumo.tarjeta_id='$tarjeta'  
		UNION 
		SELECT 
			tarjeta_consumo_cuota.fecha,CONCAT(tarjeta_consumo_cuota.nro_cuota,'/',tarjeta_consumo.cuotas) AS cuotas,tarjeta_consumo_cuota.monto,'Impuestos,tasas y Cargas sociales' as operacion, compra.nro_orden 
		FROM tarjeta_consumo_cuota 
		INNER JOIN tarjeta_consumo 
			ON tarjeta_consumo_cuota.tarjeta_consumo_id=tarjeta_consumo.id 
		INNER JOIN rel_pago_operacion 
			ON tarjeta_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago = 'tarjeta' 
		INNER JOIN compra
			ON rel_pago_operacion.operacion_tipo = 'compra' AND rel_pago_operacion.operacion_id=compra.id
		WHERE tarjeta_consumo_cuota.fecha >= '$inicio' and tarjeta_consumo_cuota.fecha <= '$fin' AND tarjeta_consumo.tarjeta_id='$tarjeta'  
		UNION
		SELECT 
			tarjeta_consumo_cuota.fecha,CONCAT(tarjeta_consumo_cuota.nro_cuota,'/',tarjeta_consumo.cuotas) AS cuotas,tarjeta_consumo_cuota.monto,CONCAT('Resumen ',tarjeta_resumen.nombre) as operacion, '' as nro_orden 
		FROM tarjeta_consumo_cuota 
		INNER JOIN tarjeta_consumo 
			ON tarjeta_consumo_cuota.tarjeta_consumo_id=tarjeta_consumo.id 
		INNER JOIN rel_pago_operacion 
			ON tarjeta_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago = 'tarjeta' 
		INNER JOIN tarjeta_resumen
			ON rel_pago_operacion.operacion_tipo = 'tarjeta_resumen' AND rel_pago_operacion.operacion_id=tarjeta_resumen.id
		WHERE tarjeta_consumo_cuota.fecha >= '$inicio' and tarjeta_consumo_cuota.fecha <= '$fin' AND tarjeta_consumo.tarjeta_id='$tarjeta'
		UNION 
		SELECT 
			tarjeta_movimiento.fecha, '' as cuotas,tarjeta_movimiento.monto,tarjeta_movimiento.detalle as operacion, '' as nro_orden 
		FROM tarjeta_movimiento
		WHERE tarjeta_movimiento.tarjeta_resumen_id=$resumen_id
		";

$rsTemp = mysql_query($sql); 
$rows = array();
while($rs = mysql_fetch_array($rsTemp)){

	$monto_total = $monto_total + $rs['monto'];
	
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
}$(document).ready( function() {   // Esta parte del código se ejecutará automáticamente cuando la página esté lista.
    $("#guardarSubmit").click( function() {     // Con esto establecemos la acción por defecto de nuestro botón de enviar.
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
		url: 'controlar_abono_tarjeta_resumen.php',
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
				$('#guardarSubmit').val('Procesando...');
				$('#guardarSubmit').attr('disabled','disabled');
				$('#guardar').val('1');
				$("#idForm").submit();
			}
			
		}
	});
	
	
	
}
</script>

</head>

<body>
<?php if(isset($_POST['guardar'])){ ?>
<script>
var dhxWins = parent.dhxWins;
dhxWins.window('w_tarjeta_resumen_detalle').attachURL('tarjeta_resumen_detalle.php?resumen_id=<?php echo $_POST['resumen_id']?>');
</script>
<?php } ?>

<?php include_once("config/messages.php"); ?>
<div id="mensaje" class="error" style="display:none"></div>
<div class="formContainer">
<form method="post" id="idForm" name="form" action="tarjeta_resumen.pagar.php?resumen_id=<?php echo $resumen_id?>" >
<input type="hidden" name="resumen_id" value="<?php echo $resumen['id']?>" />
<input type="hidden" name="resumen_monto" value="<?php echo $monto_total?>"  />
<input name="guardar" id="guardar" type="hidden" value="0">
	<fieldset>
		<legend><?php echo $resumen['tarjeta']?></legend> 
		<ul class="form">
			<li><label>Periodo:</label><?php echo $resumen['nombre']?></li>
			<li><label>Monto:</label>$<?php echo $monto_total?></li>
		<?php if($resumen['estado'] == 0){ ?>
			<li><label>Forma de pago:</label>
			<select name="forma_pago">
			<option value="n">Seleccionar...</option>
			<?php
			$sql = "SELECT id,forma_pago FROM forma_pago where id!=4 ORDER BY forma_pago";
			$rsTemp = mysql_query($sql);
			while($rs = mysql_fetch_array($rsTemp)){?>
			<option value="<?php echo $rs['id']?>"><?php echo $rs['forma_pago']?></option>
			<?php } ?>
			</select> &nbsp; <a href="#" onClick="addFormaDePago(form.forma_pago.options[form.forma_pago.selectedIndex].value)">agregar</a> <img id="forma_pago_loading" src="images/loading.gif" style="display:none" /></li>
			<div id="forma_de_pago"></div>
		<?php }else{ ?>
			<li><label>Fecha de pago:</label><?php echo fechavista($resumen['fecha_pago'])?></li>
			<?php
			$operacion_tipo = 'tarjeta_resumen';
			$operacion_id = $resumen['id']; 
			include("pagos.view.php") 
			?>
		<?php } ?>
		</ul>
	</fieldset> 
	<?php if($resumen['estado'] == 0){ ?>
	<p align="center"><input type="button" value="Abonar resumen" name="guardarSubmit" id="guardarSubmit" /></p> 
	<?php } ?>
</form>
</body>
</html>
