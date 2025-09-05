<?php
ini_set( "memory_limit", "-1" );
ini_set('max_execution_time', "-1");
session_start();

$data 	= explode(",",$_GET['dataid']);

if(is_array($data) and count($data)>1 and $_GET['action'] == 'consultar' ){ ?>

	<p align="center">Seleccione un s&oacute;lo registro</p>

<?php }elseif(is_array($data) and count($data)>1 and ($_GET['action'] == 'abonar') or isset($_POST['datos']) ){

	$dataid = $_GET['dataid'];
	//include("cuota_plan.pagar.php");

}elseif(is_array($data) and count($data)==1){

	$dataid = $data[0];

	include_once("functions/fechasql.php");
	include_once("functions/date.php");

	include_once("config/db.php");
	include_once("config/user.php");

	if(isset($_POST['guardar'])){ //guardo los datos extras del gasto

		$operacion_monto = $_POST['cuenta_pendiente'];
		include("functions/comprueba_pagos.php");

		if($procesa){

			$dataid			= $_POST['cuenta_id'];
			$operacion_id[] = $dataid;
			$operacion_tipo = 'cuota_plan';

			include("functions/procesa_pagos.php");
			if (!$error) {
				$result = 1;
				$sql = "UPDATE cuota_plans SET estado=1,fecha_pago=NOW() WHERE id=".$_POST['cuenta_id'];
				mysqli_query($conn,$sql);
			}

		}else{
		//if( ($operacion_monto+$monto_interes-$monto_descuento) == $monto_pagado and $fecha_error == 0 and $error_cheque == false and $error_cheque_numero == false){ $procesa = true; }

			$total_operacion = $operacion_monto+$monto_interes-$monto_descuento;

			if(bccomp($total_operacion,$monto_pagado) != 0){
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
				$result = "No se pudo procesar la operacion: $procesa TO: $total_operacion MP: $monto_pagado FE: $fecha_error ";
			}
			$dataid = $_POST['cuenta_id'];

		}
	}


	if(isset($dataid)){
		$sql 		= "SELECT cuota_plans.id, cuota_plans.plan_id, plans.plan, plans.tipo, plans.proveedor, plans.ordenes, rubro.rubro, subrubro.subrubro,
						usuario.nombre,usuario.apellido, cuota_plans.vencimiento, cuota_plans.monto, cuota_plans.fecha_pago, cuota_plans.estado
						FROM cuota_plans
						INNER JOIN plans ON cuota_plans.plan_id = plans.id
						LEFT JOIN rubro ON plans.rubro_id = rubro.id
						LEFT JOIN subrubro ON plans.subrubro_id = subrubro.id
						LEFT JOIN usuario ON plans.user_id=usuario.id
						WHERE cuota_plans.id=$dataid";

		//echo $sql;
		$rs_cuenta 	= mysqli_fetch_array(mysqli_query($conn,$sql));


		$estado = $rs_cuenta['estado'];

		$operacion_tipo = 'cuota_plan';
		$operacion_id = $rs_cuenta['id'];
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

	<?php if( isset($_POST['guardar']) ) { ?>
	<script>
	var dhxWins = parent.dhxWins;
	dhxWins.window('w_planes_pagos').attachURL('v2/cuota_plans/index');
	</script>
	<?php } ?>

	<?php include_once("config/messages.php"); ?>
	<div id="mensaje" class="error" style="display:none"></div>
	<div class="formContainer">
	<form method="post" id="idForm" name="form" action="cuota_plan.view.php">
		<input name="guardar" id="guardar" type="hidden" value="0">
		<input type="hidden" name="cuenta_id" value="<?php echo $rs_cuenta['id']?>" />
		<input type="hidden" name="cuenta_pendiente" value="<?php echo $rs_cuenta['monto']?>"  />
		<fieldset>
			<legend>Detalle de <?php echo $rs_cuenta['tipo'];?></legend>
			<ul class="form">
				<li><label>Responsable:</label><?php echo $rs_cuenta['nombre']?> <?php echo $rs_cuenta['apellido']?></li>
				<li><label>Plan:</label><?php echo $rs_cuenta['plan']?></li>
				<li><label>Proveedor:</label><?php echo $rs_cuenta['proveedor']?></li>
				<li><label>Rubro:</label><?php echo $rs_cuenta['rubro']?></li>
				<li><label>Sububro:</label><?php echo $rs_cuenta['subrubro']?></li>

				<li><label>Vencimiento:</label><?php echo fechavista($rs_cuenta['vencimiento'])?></li>
				<li><label>Monto:</label>$<?php echo $rs_cuenta['monto']?></li>
				<?php
                $ordenes=$rs_cuenta['ordenes'];
				/*switch ($rs_cuenta['tipo']) {
					case 'Gastos y compras':

						$sql = "SELECT * FROM gasto WHERE plan_id=".$rs_cuenta['plan_id'];
						//echo $sql;
						$rsTemp = mysqli_query($conn,$sql);
						if(mysqli_num_rows($rsTemp)>0){
							while($rsGasto = mysqli_fetch_array($rsTemp)){
								$ordenes .=$rsGasto['nro_orden'].' / ';
							}
						}
					break;
					case 'Impuestos, tasas y cargas sociales':

						$sql = "SELECT * FROM compra WHERE plan_id=".$rs_cuenta['plan_id'];
						//echo $sql;
						$rsTemp = mysqli_query($conn,$sql);
						if(mysqli_num_rows($rsTemp)>0){
							while($rsCompra = mysqli_fetch_array($rsTemp)){
								$ordenes .=$rsCompra['nro_orden'].' / ';
							}
						}
					break;
					default:
						$sql = "SELECT * FROM cuenta_a_pagar WHERE plan_id=".$rs_cuenta['plan_id'];
						//echo $sql;
						$rsTemp = mysqli_query($conn,$sql);
						if(mysqli_num_rows($rsTemp)>0){
							while($rsCuenta = mysqli_fetch_array($rsTemp)){
								switch ($rsCuenta['operacion_tipo']) {
		        					case 'gasto':
										$sqlGasto = "SELECT * FROM gasto WHERE id=".$rsCuenta['operacion_id'];
										//echo $sql;
										$rsTempGasto = mysqli_query($conn,$sqlGasto);
										if(mysqli_num_rows($rsTempGasto)>0){
											while($rsGasto = mysqli_fetch_array($rsTempGasto)){
												$ordenes .=$rsGasto['nro_orden'].' / ';
											}
										}
		        					break;

		        					default:
										$sqlCompra = "SELECT * FROM compra WHERE id=".$rsCuenta['operacion_id'];
										//echo $sql;
										$rsTempCompra = mysqli_query($conn,$sqlCompra);
										if(mysqli_num_rows($rsTempCompra)>0){
											while($rsCompra = mysqli_fetch_array($rsTempCompra)){
												$ordenes .=$rsCompra['nro_orden'].' / ';
											}
										}
		        					break;
		        				}


							}
						}



					break;
				}*/
				?>
				<li><label>Ordenes incluidas:</label><?php echo $ordenes?></li>

	<?php if( ($estado==0 and $_GET['action'] == 'abonar') or (isset($_POST['guardar']) and $result != 1) ){ ?>

				<li><label>Forma de pago:</label>
				<select name="forma_pago">
				<option value="n">Seleccionar...</option>
				<?php
				$sql = "SELECT id,forma_pago FROM forma_pago ORDER BY forma_pago";
				$rsTemp = mysqli_query($conn,$sql);
				while($rs = mysqli_fetch_array($rsTemp)){?>
				<option value="<?php echo $rs['id']?>"><?php echo $rs['forma_pago']?></option>
				<?php } ?>
				</select> &nbsp; <a href="#" onClick="addFormaDePago(form.forma_pago.options[form.forma_pago.selectedIndex].value)">agregar</a> <img id="forma_pago_loading" src="images/loading.gif" style="display:none" /></li>
				<div id="forma_de_pago"></div>
			</ul>
		</fieldset>
		<p align="center"><input type="button" value="Guardar datos" name="agregarSubmit" id="agregarSubmit" /></p>
	</form>

	<?php }elseif($estado == 1){ ?>
			<li><label>Fecha de pago:</label><?php echo fechavista($rs_cuenta['fecha_pago'])?></li>
			<?php include("pagos.view.php") ?>
			</ul>
		</fieldset>
	</form>
	<?php } ?>
	</div>
	</body>
	</html>
<?php } ?>
