<?php
session_start();

include_once("functions/fechasql.php");
include_once("config/db.php");

if(isset($_POST['monto'])){
	
	
	
		
	$sql = "SELECT valor FROM configuracion WHERE id='compra_aprobada'";
	$rs = mysql_fetch_array(mysql_query($sql));
	
	if($_POST['monto'] >= $rs['valor']){  //el monto del compra supera al valor permitido automatico
	
		$estado = 0; 
		$nro_orden = 0;
		
	}else{ 
		
		$estado = 0;
		$sql = "SELECT nro_orden FROM compra ORDER BY nro_orden DESC LIMIT 1";
		$rs = mysql_fetch_array(mysql_query($sql));
		$nro_orden = $rs['nro_orden'] + 1;
		
	}
	
	$sql = "INSERT INTO compra (nro_orden,fecha,fecha_vencimiento,rubro_id,subrubro_id,proveedor,descripcion,remito_nro,recibo_nro,factura_nro,factura_tipo,factura_orden,monto,user_id,estado)
			VALUES ($nro_orden,'".fechasql($_POST['fecha'])."','".fechasql($_POST['fecha_vencimiento'])."',".$_POST['rubro'].",'".$_POST['subrubro_id']."','".$_POST['proveedor']."','".$_POST['descripcion']."','".$_POST['remito_nro']."','".$_POST['recibo_nro']."','".$_POST['factura_nro']."','".$_POST['factura_tipo']."','".$_POST['factura_orden']."','".$_POST['monto']."',".$_SESSION['userid'].",$estado)";
	//echo $sql;
	mysql_query($sql);
	//$result = mysql_error();
	
	if($result == ''){ $result = 1; }

	$operacion_id 	= mysql_insert_id();
	$operacion_tipo = "compra";
		
	
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
	$('.date-pick').datePicker().val(new Date().asString()).trigger('change');
	$("#proveedores").combobox();
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
	if((F.remito_nro.value!="")&&(F.remito_nro.value.length != 6)) {
		alert("Ingrese 6 digitos en el nro de remito")
		F.factura_nro.focus();
		return false
		}
	if((F.recibo_nro.value!="")&&(F.recibo_nro.value.length != 6)) {
		alert("Ingrese 6 digitos en el nro de recibo")
		F.factura_nro.focus();
		return false
		}
	if((F.factura_tipo.value!="n")&&(F.factura_nro.value.length != 6)) {
		alert("Ingrese 6 digitos en el nro de comprobante")
		F.factura_nro.focus();
		return false
	}
	if(vacio(F.monto.value) == false) {
	alert("El monto es obligatorio")
	F.monto.focus();
	return false
	}
			
	var datos = ({
		'monto' : F.monto.value,
		'fecha' : F.fecha.value,
		'factura_nro' : F.factura_nro.value,
		'tabla' : 'compra'
	});
	
	$.ajax({
		beforeSend: function(){
			$('#loading').show();
		},
		data: datos,
		url: 'functions/checkFactura.php',
		dataType:"json",
		success: function(data) {
		
			if(data["siMonto"] == 'si'){		
				//if(confirm("Para la fecha de devengado "+F.fecha.value+", existe una compra de id&eacute;ntico monto ($"+F.monto.value+"), para el proveedor ("+data["proveedor"]+")!  \n \n Continuar?")) {
				if(confirm("Para la fecha de devengado "+F.fecha.value+", existe una compra de identico monto ($"+F.monto.value+"), a el proveedor ("+data["proveedor"]+") con nro. de factura "+data["factura_tipo"]+" "+data["factura_orden"]+"-"+data["factura_nro"]+". Controle que no este intentando cargar nuevamente la factura detallada en este mensaje!  \n \n Continuar?")) {
						F.submit();
				}else{
					$('#loading').hide();
				}
			}else{
				if(data["siFactura"] == 'si'){		
					if(confirm("Para la fecha de devengado "+data["fecha"]+", existe una compra a el proveedor ("+data["proveedor"]+") con nro de factura "+data["factura_tipo"]+" "+data["factura_orden"]+"-"+data["factura_nro"]+". Controle que no este intentando cargar nuevamente la factura detallada en este mensaje!  \n \n Continuar?")) {
						F.submit();
					}else{
						$('#loading').hide();
					}
				}else{
					F.submit();
				}
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
	dhxWins.window('w_compra').attachURL('v2/compras/index');
	</script>
<?php  } ?>
<?php  include_once("config/messages.php"); ?>
<?php  if(isset($nro_orden)){
	if($nro_orden==0){
		echo '<div id="mensaje" class="ok"><p><img src="images/error.gif" align="absmiddle" /> &nbsp; La compra debe ser aprobado por administaci&oacute;n</p></div>';
	}else{
		echo '<div id="mensaje" class="ok"><p><img src="images/ok.gif" align="absmiddle" /> &nbsp; La compra se aprob&oacute; con la orden de pago: '.$nro_orden.'</p></div>';?>

<?php 
	}
}

?>

<div class="formContainer">
	<form method="POST" name="form" action="compras.add.php" onSubmit="return valida(this);">
	<fieldset>
		<legend>Detalle de compra</legend> 
		<ul class="form">
			<li><label>Fecha devengado:</label><input class="date-pick dp-applied" name="fecha" /></li>
			<li><label>Fecha vencimiento:</label><input class="date-pick dp-applied" name="fecha_vencimiento" /></li>
			<li><label>Rubro:</label>
			<select name="rubro" onchange="createCombo('subrubro','rubro_id','subrubro',form.rubro.options[form.rubro.selectedIndex].value);">
			<option value="null">Seleccionar...</option>
			<?php 
			$sql = "SELECT id,rubro FROM rubro WHERE impuestos=1 AND activo=1  ORDER BY rubro";
			$rsTemp = mysql_query($sql);
			while($rs = mysql_fetch_array($rsTemp)){?>
			<option value="<?php echo $rs['id']?>"><?php echo $rs['rubro']?></option>
			<?php  } ?>
			</select> <img id="combo_loading" src="images/loading.gif" style="display:none" />
			<li id="subrubro" style="display:none;"><label>Subrubro:</label><div id="subrubro_combo"></div></li>
			<li><label>Proveedor:</label>
			<select id="proveedores" name="proveedor" size="1">
			<option value="">Seleccione uno...</option>
			<?php 
			$sql2 = "SELECT id,nombre FROM proveedor ORDER BY nombre ASC";
			$rsTemp2 = mysql_query($sql2);
			while($rs2 = mysql_fetch_array($rsTemp2)){?>
			<option <?php  if($rs2['id']==$rs['proveedor_id']){ ?> selected="selected" <?php  } ?> value="<?php echo $rs2['id']?>"><?php echo $rs2['nombre']?></option>
			<?php  } ?>
			</select>
			</li>
			<li><label>Descripcion:</label><textarea name="descripcion"></textarea></li>
			<!--  <li><label>Numero de remito:</label><input type="text" name="remito_nro" /></li>-->
			<li><label>Numero de recibo:</label><input type="text" name="recibo_nro" maxlength="6"/></li>
			<li><label>Numero de comprobante:</label>
				<select size="1" name="factura_tipo">
					<option value="n">Tipo</option>
					<option value="A">A</option>
					<option value="B">B</option>
					<option value="C">C</option>
				</select> 
				<select size="1" name="factura_orden">
					<option value="B">0001</option>
					<option value="N">0002</option>
				</select> 
				<input type="text" name="factura_nro" maxlength="6"/></li>
			<li><label>Monto neto:</label><input name="monto" value="" size="3"/> <img id="loading" src="images/loading.gif" style="display:none" /></li>
		</ul>
   	</fieldset> 
	<p align="center"><input type="submit" value="Agregar compra" name="agregar" /></p> 
	</form> 
</div> 
</body>
</html>
