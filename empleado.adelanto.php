<?php
session_start();
$user_id = $_SESSION['userid'];
if($user_id == '') { header("Location: index.php"); }
include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("config/user.php");
include_once("functions/abm.php");

$empleado_id = $_GET['empleado_id'];
$ano = ($_GET['ano'])?$_GET['ano']:date('Y');
$mes = ($_GET['mes'])?$_GET['mes']:'0';
if(isset($_POST['agregar'])){
	$sql = "SELECT * FROM empleado_pago WHERE empleado_id = ".$_POST['empleado_id']." AND ano = ".$_POST['ano'];
	$rsTemp = mysqli_query($conn,$sql);
	while($rs = mysqli_fetch_array($rsTemp)){
		$pagado[$rs['ano']."_".$rs['mes']] = true;
	}

                  $today = new DateTime();
                  $today->modify('-1 month');
                  $mes_anterior = $today->format('n');
                  $ano_anterior = $today->format('Y');

                  if(!$pagado[$_POST['ano']."_".$_POST['mes']] and ((ACCION_102) or (($_POST['ano'] == $ano_anterior and $_POST['mes'] == $mes_anterior) or ($_POST['ano'] ==  date('Y') and $_POST['mes'] == date('m')) ) )){

		$operacion_monto = $_POST['monto'];
		include("functions/comprueba_pagos.php");

		if($procesa){

			$sql = "INSERT INTO empleado_adelanto
						(empleado_id,monto,comentarios,mes,ano,creado_por,creado)
					VALUES
						(".$_POST['empleado_id'].",'".$_POST['monto']."','".$_POST['comentarios']."',".$_POST['mes'].",".$_POST['ano'].",$user_id,NOW())";
				mysqli_query($conn,$sql);

			$operacion_id[] = mysql_insert_id();
			$operacion_tipo = 'sueldo_adelanto';

			include("functions/procesa_pagos.php");


            $empleado_id = $_POST['empleado_id'];

				$result = 1;
				echo "
				<script>window.open('reciboPDF.php?id=".$operacion_id[0]."&adelanto=1', 'Adelanto de sueldo');</script>";

            /*echo "<script>

		    	window.parent.dhxWins.window('w_empleado_adelanto').close();

				</script>";*/
		}else{

			if(($operacion_monto+$monto_interes-$monto_descuento) != $monto_pagado){
				$result = 'Verifique que monto de adelanto ('.$operacion_monto.') sea igual al valor que intenta pagar ('.$monto_pagado.')';
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

			$empleado_id = $_POST['empleado_id'];

		}
	}else{
 		$result = "No se puede otorgar el adelanto, controle la fecha";
		$empleado_id = $_POST['empleado_id'];
                }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Administrador de empleados</title>
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
<link href="styles/form.css" rel="stylesheet" type="text/css" />
<link href="styles/form2.css" rel="stylesheet" type="text/css" />
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

	/*if(F.forma_pago.value == 'null') {
	alert("Debe seleccionar una forma de pago");
	F.forma_pago.focus();
	return false
	}
	if(vacio(F.monto.value) == false) {
	alert("Debe completar con un monto")
	F.monto.focus();
	return false
	}
	if(vacio(F.ano.value) == false) {
	alert("El ano es obligatorio")
	F.ano.focus();
	return false
	}
	if(F.mes.value == 'null') {
	alert("Debe seleccionar un mes");
	F.mes.focus();
	return false
	}
	var fecha = new Date();
	var fecha2 = new Date(F.ano.value,(F.mes.value-1),1);
	if(fecha.getTime()<fecha2.getTime()){
		alert("Mes y ano no pueden ser posteriores a hoy");
		F.mes.focus();
		return false
	}*/
	$('#mensaje').html('');
	$('#mensaje').hide();
	$.ajax({

		type : 'POST',
		data: $("#idForm").serialize(),
		url: 'controlar_abono_adelanto_empleado.php',
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
				$('#agregar').val('1');
				$("#idForm").submit();
			}

		}
	});



}
function cargarMeses(year, mes){
	$("#input_mes").empty();
	$("#input_mes").append("<option value=\"n\">Seleccione...</option>");
	var hoy = new Date();
	var select ='';
	var meses={
			  1:"Enero",
			  2:"Febrero",
			  3:"Marzo",
			  4:"Abril",
			  5:"Mayo",
			  6:"Junio",
			  7:"Julio",
			  8:"Agosto",
			  9:"Septiembre",
			  10:"Octubre",
			  11:"Noviembre",
			  12:"Diciembre",
			};
	$.each(meses, function(k,v){

		fecha2 = new Date(year.value,(k-1),1);
		if(hoy.getTime()>=fecha2.getTime()){

			select = (mes==k)?"selected=\"selected\"":"";

			$("#input_mes").append("<option value=\""+k+"\"  "+select+">"+v+"</option>");
		}
	});

}
</script>
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
var dhxWins = parent.dhxWins;

</script>
</head>

<body onLoad="$('#input_ano').change();">

<?php  if(isset($_POST['agregar'])){ ?>
	<script>
	var dhxWins = parent.dhxWins;
	dhxWins.window('w_empleado_view').attachURL('empleados.ficha.php?empleado_id=<?php echo $_POST['empleado_id']?>');
	</script>
<?php  } ?>

<?php  include_once("config/messages.php"); ?>
<div id="mensaje" class="error" style="display:none"></div>
<div class="container">

<form method="POST" id="idForm" name="form" action="empleado.adelanto.php">
	<input type="hidden" name="empleado_id" value="<?php echo $empleado_id?>" />
    <input name="agregar" id="agregar" type="hidden" value="0">
    <div class="label">Monto</div>
        <div class="content">
        <input type="text" size="5" name="monto" />
        </div>
        <div style="clear:both;"></div>

    <div class="label">A&ntilde;o</div>
        <div class="content">
        <input type="text" size="2" id="input_ano" name="ano" value="<?php echo $ano?>" onChange="cargarMeses(this,<?php echo $mes?>)"/>
        </div>
        <div style="clear:both;"></div>
    <div class="label">Mes</div>
        <div class="content">
        <select name="mes" id="input_mes">

        </select>
        </div>
        <div style="clear:both;"></div>

	<div class="label">Comentarios</div>
        <div class="content">
        <textarea cols="20" rows="5" name="comentarios"></textarea>
        </div>
        <div style="clear:both;"></div>

    <div class="label">Forma de pago</div>
        <div class="content">
        <select name="forma_pago">
		<option value="n">Seleccionar...</option>
		<?php
		$sql = "SELECT id,forma_pago FROM forma_pago WHERE id IN (1,3,4,6) ORDER BY forma_pago ";
		$rsTemp = mysqli_query($conn,$sql);
		while($rs = mysqli_fetch_array($rsTemp)){?>
		<option value="<?php echo $rs['id']?>"><?php echo $rs['forma_pago']?></option>
		<?php  } ?>
		</select> &nbsp; <a style="cursor:pointer;" onclick="addFormaDePago(form.forma_pago.options[form.forma_pago.selectedIndex].value)">agregar</a> <img id="forma_pago_loading" src="images/loading.gif" style="display:none" /></li>
        </div>
        <div style="clear:both;"></div>

	<div class="form" id="forma_de_pago" style="font-family:arial; font-size:12px;"></div>

    <p align="center"><input type="button" value="Guardar" name="agregarSubmit" id="agregarSubmit" /></p>
</form>

</div>
</body>
</html>
