<?php
session_start();

include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/util.php");
$errorFechas=0;
if(isset($_POST['monto'])){
	if ((is_date($_POST['fecha']))&&(is_date($_POST['fecha_vencimiento']))) {



		$sql = "SELECT valor FROM configuracion WHERE id='gasto_aprobado'";
		$rs = mysql_fetch_array(mysql_query($sql));

		if($_POST['monto'] >= $rs['valor']){  //el monto del gasto supera al valor permitido automatico

			$estado = 0;
			$nro_orden = 0;

		}else{

			$estado = 0;
			$sql = "SELECT nro_orden FROM gasto ORDER BY nro_orden DESC LIMIT 1";
			$rs = mysql_fetch_array(mysql_query($sql));
			$nro_orden = $rs['nro_orden'] + 1;

		}
		$iva_27 = (ISSET($_POST['iva_27'])&&($_POST['iva_27']!=''))?$_POST['iva_27']:0;
		$iva_21 = (ISSET($_POST['iva_21'])&&($_POST['iva_21']!=''))?$_POST['iva_21']:0;
		$iva_10_5 = (ISSET($_POST['iva_10_5'])&&($_POST['iva_10_5']!=''))?$_POST['iva_10_5']:0;
		$otra_alicuota = (ISSET($_POST['otra_alicuota'])&&($_POST['otra_alicuota']!=''))?$_POST['otra_alicuota']:0;
		$perc_iva = (ISSET($_POST['perc_iva'])&&($_POST['perc_iva']!=''))?$_POST['perc_iva']:0;
		$perc_iibb_bsas = (ISSET($_POST['perc_iibb_bsas'])&&($_POST['perc_iibb_bsas']!=''))?$_POST['perc_iibb_bsas']:0;
		$perc_iibb_caba = (ISSET($_POST['perc_iibb_caba'])&&($_POST['perc_iibb_caba']!=''))?$_POST['perc_iibb_caba']:0;
		$exento = (ISSET($_POST['exento'])&&($_POST['exento']!=''))?$_POST['exento']:0;
        $quitar_egresos = ($_POST['quitar_egresos'])?1:0;
		$sql = "INSERT INTO gasto (nro_orden,fecha,fecha_vencimiento,rubro_id,subrubro_id,proveedor,descripcion,remito_nro,recibo_nro,factura_nro,factura_tipo,factura_orden,monto,user_id,estado,iva_27,iva_21,iva_10_5,otra_alicuota,origen,razon,cuit, factura_punto_venta,perc_iva,perc_iibb_bsas,perc_iibb_caba,exento,quitar_egresos)
				VALUES ($nro_orden,'".fechasql($_POST['fecha'])."','".fechasql($_POST['fecha_vencimiento'])."',".$_POST['rubro'].",'".$_POST['subrubro_id']."','".$_POST['proveedor']."','".$_POST['descripcion']."','".$_POST['remito_nro']."','".$_POST['recibo_nro']."','".$_POST['factura_nro']."','".$_POST['factura_tipo']."','".$_POST['factura_orden']."','".$_POST['monto']."',".$_SESSION['userid'].",$estado,$iva_27,$iva_21,$iva_10_5,$otra_alicuota,'".$_POST['origen']."','".$_POST['razon']."','".$_POST['cuit']."','".$_POST['factura_punto_venta']."',".$perc_iva.",".$perc_iibb_bsas.",".$perc_iibb_caba.",".$exento.",".$quitar_egresos.")";
		mysql_query($sql);
		_log($sql);
		$result = mysql_error();

		if($result == ''){ $result = 1; }

		$operacion_id 	= mysql_insert_id();
		$operacion_tipo = "gasto";
	}
	else{
		$errorFechas=1;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="styles/form.css" rel="stylesheet" type="text/css" />
<script src="library/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>

<!--JQuery UI-->
<script src="library/jquery/ui/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<link href="library/jquery/ui/css/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
<script src="js/combobox-autosuggest.js" type="text/javascript"></script>
<!--/JQuery UI-->

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
<script>
$(function()
{
	//$('.date-pick').datePicker().val(new Date().asString()).trigger('change');
	$('.date-pick').datePicker().trigger('change');
	$("#proveedores").combobox();
	$(".ui-autocomplete-input").blur(function () {
		var datos = ({
			'id' : $("#proveedores").val()

		});

		$.ajax({
			beforeSend: function(){

			},
			data: datos,
			url: 'functions/dameProveedor.php',
			dataType:"json",
			success: function(data) {
				$("#razon").val(data["razon"]);

				if($("#razon").val()){
					$("#razon").attr('disabled','disabled');
				}
				else{
					$("#razon").removeAttr('disabled');
					}
				$("#cuit").val(data["cuit"]);
				if($("#cuit").val()){
					$("#cuit").attr('disabled','disabled');
				}
				else{
					$("#cuit").removeAttr('disabled');
					}

			}
		});
	});
	$( "#factura_orden" ).change(function() {


		$('#factura_tipo').empty();
		$('#factura_tipo').append('<option value="n">Tipo</option>');
		if($(this).val()=='N'){
			$('#factura_tipo').append('<option value="Ticket">Ticket</option>');
            $('#quitar_egresos').attr('disabled','disabled');
		}
		else{
            $("#quitar_egresos").removeAttr('disabled');
			$('#factura_tipo').append('<option value="A">A</option>');
			$('#factura_tipo').append('<option value="B">B</option>');
			$('#factura_tipo').append('<option value="C">C</option>');
			$('#factura_tipo').append('<option value="E">E</option>');
			$('#factura_tipo').append('<option value="M">M</option>');
			}


			/*<option value="A">A</option>
			<option value="B">B</option>
			<option value="C">C</option>
			<option value="E">E</option>
			<option value="M">M</option>*/


    });
	$( "#monto" ).change(function() {
		calcularControl();
    });
	$( "#neto" ).change(function() {
		calcularControl();
    });
	$( "#iva_27" ).change(function() {
		calcularControl();
    });
	$( "#iva_21" ).change(function() {
		calcularControl();
    });
	$( "#iva_10_5" ).change(function() {
		calcularControl();
    });
	$( "#otra_alicuota" ).change(function() {
		calcularControl();
    });
	$( "#perc_iva" ).change(function() {
		calcularControl();
    });
	$( "#perc_iibb_bsas" ).change(function() {
		calcularControl();
    });
	$( "#perc_iibb_caba" ).change(function() {
		calcularControl();
    });
	$( "#exento" ).change(function() {
		calcularControl();
    });

});

function roundToTwo(num) {
    return +(Math.round(num + "e+2")  + "e-2");
}

function calcularControl(){

	if(($( "#factura_tipo" ).val() == 'A')||($( "#factura_tipo" ).val() == 'M')){
		if(isNaN($( "#monto" ).val())||$( "#monto" ).val()==""){
			$( "#monto" ).val(0);
		}
		if(isNaN($( "#neto" ).val())||$( "#neto" ).val()==""){
			$( "#neto" ).val(0);
		}
		if(isNaN($( "#iva_27" ).val())||$( "#iva_27" ).val()==""){
			$( "#iva_27" ).val(0);
		}
		if(isNaN($( "#iva_21" ).val())||$( "#iva_21" ).val()==""){
			$( "#iva_21" ).val(0);
		}
		if(isNaN($( "#iva_10_5" ).val())||$( "#iva_10_5" ).val()==""){
			$( "#iva_10_5" ).val(0);
		}
		if(isNaN($( "#otra_alicuota" ).val())||$( "#otra_alicuota" ).val()==""){
			$( "#otra_alicuota" ).val(0);
		}
		if(isNaN($( "#perc_iva" ).val())||$( "#perc_iva" ).val()==""){
			$( "#perc_iva" ).val(0);
		}
		if(isNaN($( "#perc_iibb_bsas" ).val())||$( "#perc_iibb_bsas" ).val()==""){
			$( "#perc_iibb_bsas" ).val(0);
		}
		if(isNaN($( "#perc_iibb_caba" ).val())||$( "#perc_iibb_caba" ).val()==""){
			$( "#perc_iibb_caba" ).val(0);
		}
		if(isNaN($( "#exento" ).val())||$( "#exento" ).val()==""){
			$( "#exento" ).val(0);
		}
		$( "#control" ).val((parseFloat($( "#monto" ).val())-(parseFloat($( "#neto" ).val())+parseFloat($( "#iva_27" ).val())+parseFloat($( "#iva_21" ).val())+parseFloat($( "#iva_10_5" ).val())+parseFloat($( "#otra_alicuota" ).val())+parseFloat($( "#perc_iva" ).val())+parseFloat($( "#perc_iibb_bsas" ).val())+parseFloat($( "#perc_iibb_caba" ).val())+parseFloat($( "#exento" ).val()))).toFixed(2));
	}
	else{
		$( "#control" ).val('0.00');

		}
}

function mostrarAlicuota(tipo){
	if((tipo == 'A')||(tipo == 'M')){
		$('#alicuota').show();
	}
	else{
		$('#alicuota').hide();
		$('#iva_27').val('');
		$('#iva_21').val('');
		$('#iva_10_5').val('');
		$('#otra_alicuota').val('');
		$('#perc_iva').val('');
		$('#perc_iibb_bsas').val('');
		$('#perc_iibb_caba').val('');
		$('#exento').val('');

	}
}
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


	if(F.rubro.value == 'null') {
	alert("Rubro es obligatorio")
	F.rubro.focus();
	return false
	}
	if(vacio(F.fecha.value) == false) {
	alert("Fecha es obligatorio")
	F.fecha.focus();
	return false
	}
	if(vacio(F.proveedor.value) == false) {
	alert("Complete con un proveedor")
	F.proveedor.focus();
	return false
	}

	if(isNaN(F.cuit.value)){
		alert("Ingrese solo numeros en el CUIT")
		F.cuit.focus();
		return false
		}
	if((F.cuit.value.length != 11)) {
		alert("Ingrese 11 digitos en el CUIT")
		F.cuit.focus();
		return false
	}
	if((F.remito_nro.value!="")&&(F.remito_nro.value.length != 6)) {
		alert("Ingrese 6 digitos en el nro de remito")
		F.remito_nro.focus();
		return false
		}
	if((F.recibo_nro.value!="")&&(F.recibo_nro.value.length != 6)) {
		alert("Ingrese 6 digitos en el nro de recibo")
		F.recibo_nro.focus();
		return false
		}
	if((F.factura_tipo.value!="n")&&(F.factura_punto_venta.value.length != 4)) {
		alert("Ingrese 4 digitos en el punto de venta")
		F.factura_punto_venta.focus();
		return false
	}
	if((F.factura_tipo.value!="n")&&(F.factura_nro.value.length != 8)) {
		alert("Ingrese 8 digitos en el nro de factura")
		F.factura_nro.focus();
		return false
	}

	if(vacio(F.monto.value) == false) {
	alert("El monto es obligatorio")
	F.monto.focus();
	return false
	}
	if(F.monto.value>0){
		if(((F.factura_tipo.value=="A")||(F.factura_tipo.value=="M"))&&(vacio(F.iva_27.value) == false)&&(vacio(F.iva_21.value) == false)&&(vacio(F.iva_10_5.value) == false)&&(vacio(F.otra_alicuota.value) == false)) {
			alert("Incluya los montos de IVA Segun la alicuota que corresponda")
			F.iva_27.focus();
			return false
			}
		var mitadMonto = parseFloat(F.monto.value)/2;
		var totalAlicuota = parseFloat(F.iva_27.value)+parseFloat(F.iva_21.value)+parseFloat(F.iva_10_5.value)+parseFloat(F.otra_alicuota.value);
		if((totalAlicuota>mitadMonto)||(F.iva_27.value>mitadMonto)||(F.iva_21.value>mitadMonto)||(F.iva_10_5.value>mitadMonto)||(F.otra_alicuota.value>mitadMonto)) {
			alert("El total de IVA no puede ser mayor al 50% del monto bruto")
			F.iva_27.focus();
			return false
			}
	}
	/*if(vacio(F.neto.value) == false) {
		alert("El neto es obligatorio")
		F.neto.focus();
		return false
		}*/
		if((F.control.value!='0.00')&&(F.control.value!='-0.00')){
		alert("El monto de control debe ser 0(cero)")
		F.monto.focus();
		return false
	}
	var datos = ({
		'monto' : F.monto.value,
		'fecha' : F.fecha.value,
		'fecha_vencimiento' : F.fecha_vencimiento.value,
		'factura_nro' : F.factura_nro.value,
		'tabla' : 'gasto'
	});
	$.ajax({
		beforeSend: function(){
			$('#loading').show();
		},
		data: datos,
		url: 'functions/checkFecha.php',
		dataType:"json",
		success: function(data) {

			if(data["fecha"] == 'no'){
				alert('Error en alguna de las fechas');
			}
			else{

				$.ajax({
					beforeSend: function(){
						$('#loading').show();
					},
					data: datos,
					url: 'functions/checkFactura.php',
					dataType:"json",
					success: function(data) {

						if(data["siMonto"] == 'si'){
							//if(confirm("Para la fecha de devengado "+F.fecha.value+", existe un gasto de id&eacute;ntico monto ($"+F.monto.value+"), para el proveedor ("+data["proveedor"]+")!  \n \n Continuar?")) {
							if(confirm("Para la fecha de devengado "+F.fecha.value+", existe un gasto de identico monto ($"+F.monto.value+"), para el proveedor ("+data["proveedor"]+") con nro. de factura "+data["factura_tipo"]+" "+data["factura_orden"]+"-"+data["factura_nro"]+". Controle que no este intentando cargar nuevamente la factura detallada en este mensaje!  \n \n Continuar?")) {
								$("#razon").removeAttr('disabled');
								$("#cuit").removeAttr('disabled');
								F.submit();
							}else{
								$('#loading').hide();
							}
						}else{
							if(data["siFactura"] == 'si'){
								if(confirm("Para la fecha de devengado "+data["fecha"]+", existe un gasto para el proveedor ("+data["proveedor"]+") con nro de factura "+data["factura_tipo"]+" "+data["factura_orden"]+"-"+data["factura_nro"]+". Controle que no este intentando cargar nuevamente la factura detallada en este mensaje!  \n \n Continuar?")) {
									$("#razon").removeAttr('disabled');
									$("#cuit").removeAttr('disabled');
									F.submit();
								}else{
									$('#loading').hide();
								}
							}else{
								$("#razon").removeAttr('disabled');
								$("#cuit").removeAttr('disabled');
								F.submit();
							}
						}

					}
				});
			}
		}
	});

	return false;
}
</script>
</head>

<body>

<?php  if(isset($nro_orden)){ ?>
	<script>
	var dhxWins = parent.dhxWins;
	dhxWins.window('w_gasto').attachURL('v2/gastos/index');
	</script>
<?php  } ?>
<?php  include_once("config/messages.php");
if($errorFechas){
		echo '<div id="mensaje" class="ok"><p><img src="images/error.gif" align="absmiddle" /> &nbsp; Verifique las fechas</p></div>';
	}
elseif(isset($nro_orden)){
	if($nro_orden==0){
		echo '<div id="mensaje" class="ok"><p><img src="images/error.gif" align="absmiddle" /> &nbsp; El gasto debe ser aprobado por administaci&oacute;n</p></div>';
	}else{
		echo '<div id="mensaje" class="ok"><p><img src="images/ok.gif" align="absmiddle" /> &nbsp; El gasto se aprob&oacute; con la orden de pago: '.$nro_orden.'</p></div>';?>

<?php
	}
}
?>

<div class="formContainer">
	<form method="POST" name="form" action="gastos.add.php" onSubmit="return valida(this);">
	<fieldset>
		<legend>Detalle de gasto</legend>
		<ul class="form">
			<li><label>Fecha devengado:</label><input class="date-pick dp-applied" name="fecha" /></li>
			<li><label>Fecha factura:</label><input class="date-pick dp-applied" name="fecha_vencimiento" /></li>
			<li><label>Origen:</label>
					<select name="origen" id="origen">
					<option value="">Seleccionar...</option>
					<option <?php if('Bienes'==$rs['origen']){ ?> selected="selected" <?php } ?> value="Bienes">Bienes</option>
					<option <?php if('Servicios'==$rs['origen']){ ?> selected="selected" <?php } ?> value="Servicios">Servicios</option>
					</select></li>
			<li><label>Rubro:</label>
			<select id="rubro" name="rubro" onchange="createCombo('subrubro','rubro_id','subrubro',form.rubro.options[form.rubro.selectedIndex].value);">
			<option value="null">Seleccionar...</option>
			<?php
			$sql = "SELECT id,rubro FROM rubro WHERE activo=1 and gastos=1 ORDER BY rubro";
			$rsTemp = mysql_query($sql);
			while($rs = mysql_fetch_array($rsTemp)){?>
			<option value="<?php echo $rs['id']?>"><?php echo $rs['rubro']?></option>
			<?php  } ?>
			</select> <img id="combo_loading" src="images/loading.gif" style="display:none" />
			<li id="subrubro" style="display:none;"><label>Subrubro:</label><div id="subrubro_combo"></div></li>
			<li><label>Proveedor:</label>
			<select id="proveedores" name="proveedor" size="1" >
			<option value="">Seleccione uno...</option>
			<?php
			$sql2 = "SELECT id,nombre FROM proveedor ORDER BY nombre ASC";
			$rsTemp2 = mysql_query($sql2);
			while($rs2 = mysql_fetch_array($rsTemp2)){?>
			<option <?php  if($rs2['id']==$rs['proveedor_id']){ ?> selected="selected" <?php  } ?> value="<?php echo $rs2['id']?>"><?php echo $rs2['nombre']?></option>
			<?php  } ?>
			</select>
			</li>
			<li><label>Raz&oacute;n Social:</label><input type="text" name="razon" id="razon"/></li>
			<li><label>CUIT:</label><input type="text" name="cuit" id="cuit"/></li>
			<li><label>Descripcion:</label><textarea name="descripcion" style="width:138px"></textarea></li>
			<li><label>Tipo de operacion:</label><select size="1" name="factura_orden" id="factura_orden">
					<option value="B">0001</option>
					<?php
					include_once("config/user.php");
					if(ACCION_137){
					?>
						<option value="N">0002</option>
					<?php }?>
				</select><input type="checkbox" id="quitar_egresos" name="quitar_egresos"></input> </li>



			<li><label>Numero de remito:</label><input type="text" name="remito_nro"  maxlength="6"/></li>
			<li><label>Numero de recibo:</label><input type="text" name="recibo_nro"  maxlength="6"/></li>
			<li><label>Numero de factura:</label>
				<select size="1" name="factura_tipo" id="factura_tipo" onchange="mostrarAlicuota($(this).val())" >
					<option value="n">Tipo</option>
					<option value="A">A</option>
					<option value="B">B</option>
					<option value="C">C</option>
					<option value="E">E</option>
					<option value="M">M</option>
				</select>
				<span style="float:left;padding: 3px 5px 3px 1px;"></span>
				<input type="text" name="factura_punto_venta"  maxlength="4" size="4"/>
				<span style="float:left;padding: 3px 5px 3px 1px;"></span>
				<input type="text" name="factura_nro"  maxlength="8" size="8"/></li>
			<li><label>Monto bruto:</label><input name="monto" id="monto" value="" size="3"/> <img id="loading" src="images/loading.gif" style="display:none" /></li>

			<span id="alicuota" style="display:none">
			<li><label>Monto neto:</label><input name="neto" id="neto" value="" size="3" /><span style="width:40px;float:left;padding: 3px 5px 3px 10px;"> Control:</span><input name="control" id="control" value="" size="3" disabled/></li>
			<li><label>IVA 27%:</label><input name="iva_27" id="iva_27" value="" size="3"/></li>
			<li><label>IVA 21%:</label><input name="iva_21" id="iva_21" value="" size="3"/></li>
			<li><label>IVA 10.5%:</label><input name="iva_10_5" id="iva_10_5" value="" size="3"/></li>
			<li><label>Otra al&iacute;cuota:</label><input name="otra_alicuota" id="otra_alicuota" value="" size="3"/></li>
			<li><label>Percepci&oacute;n IVA:</label><input name="perc_iva" id="perc_iva" value="" size="3"/></li>
			<li><label>Perc. IIBB Bs.As.:</label><input name="perc_iibb_bsas" id="perc_iibb_bsas" value="" size="3"/></li>
			<li><label>Perc. IIBB CABA:</label><input name="perc_iibb_caba" id="perc_iibb_caba" value="" size="3"/></li>
			<li><label>Exento:</label><input name="exento" id="exento" value="" size="3"/></li>
			</span>
		</ul>
   	</fieldset>
	<p align="center"><input type="submit" value="Agregar gasto" name="agregar" /></p>
	</form>
</div>
</body>
</html>
