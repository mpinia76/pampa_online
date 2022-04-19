<?php
session_start();
$user_id = $_SESSION['userid'];
if($user_id == '') { header("Location: index.php"); }

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");

//indicar tabla a editar
$tabla = 'empleado_trabajo';

if(isset($_POST['agregar'])){

	//proceso la entrada de plata a la cuenta
	$sql_entra = "INSERT INTO $tabla (fecha,empleado_id, horas_0001, horas_0002, duracion_jornada, espacio_trabajo_id, sector_1_id, sector_2_id, porcentaje_sector_1, porcentaje_sector_2, creado_por) 
				VALUES 
				(NOW(),'".$_POST['empleado_id']."','".$_POST['horas_0001']."','".$_POST['horas_0002']."','".$_POST['duracion_jornada']."','".$_POST['espacio_trabajo']."','".$_POST['sector_1_id']."','".$_POST['sector_2_id']."','".$_POST['porcentaje_sector_1']."','".$_POST['porcentaje_sector_2']."',$user_id)";
	mysql_query($sql_entra);
	
	if(mysql_error() == ''){
		$result = 1;
	}else{
		$result = mysql_error();
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

	if(vacio(F.horas_0001.value) == false) {

	alert("Complete la cantidad de horas 0001")

	F.horas_0001.focus();

	return false

	}

	if(vacio(F.horas_0002.value) == false) {

	alert("Complete la cantidad de horas 0002")

	F.horas_0002.focus();

	return false

	}
	
	if(vacio(F.duracion_jornada.value) == false) {

	alert("Complete la duracion de la jornada")

	F.duracion_jornada.focus();

	return false

	}
	
	if(F.espacio_trabajo.value == 'null' ) {

	alert("Seleccione el centro de costos")

	F.espacio_trabajo.focus();

	return false

	}
	
	if(vacio(F.porcentaje_sector_1.value) == false) {

	alert("Complete el porcentaje de sector 1 con un numero de 0 a 100")

	F.porcentaje_sector_1.focus();

	return false

	}
	
	if(F.porcentaje_sector_1.value > 0 && F.sector_1_id.value == 'null' ) {

	alert("Debe seleccionar un sector 1 de trabajo")

	F.sector_1_id.focus();

	return false

	}
	
	if(F.porcentaje_sector_2.value > 0 && F.sector_2_id.value == 'null' ) {

	alert("Debe seleccionar un sector 2 de trabajo")

	F.sector_2_id.focus();

	return false

	}
}
					
</script>



<link href="styles/form2.css" rel="stylesheet" type="text/css" />
</head>

<body>

<?php include_once("config/messages.php"); ?>

<?php if(isset($_POST['agregar'])){ ?>
	<script>
	var dhxWins = parent.dhxWins;
	dhxWins.window('w_empleado_view').attachURL('empleados.ficha.php?empleado_id=<?php echo $_POST['empleado_id']?>');
	dhxWins.window('w_emplado_add_jornada').close();
	</script>
<?php } 
$horas_0001=0;
$horas_0002=0;
$duracion_jornada=0;
$espacio_trabajo=0;
$sector_1_id=0;
$sector_2_id=0;
$porcentaje_sector_1='';
$porcentaje_sector_2='';
$sql = "SELECT * FROM $tabla 
		WHERE empleado_id = ".$_GET['empleado_id']."

	ORDER BY id DESC";

$rsTemp = mysql_query($sql);
if($rs = mysql_fetch_array($rsTemp)){
	$horas_0001=$rs['horas_0001'];
	$horas_0002=$rs['horas_0002'];
	$duracion_jornada=$rs['duracion_jornada'];
	$espacio_trabajo=$rs['espacio_trabajo_id'];
	$sector_1_id=$rs['sector_1_id'];
	$sector_2_id=$rs['sector_2_id'];
	$porcentaje_sector_1=$rs['porcentaje_sector_1'];
	$porcentaje_sector_2=$rs['porcentaje_sector_2'];
}

?>
<div class="container">

	<form method="POST" name="form" action="empleados.trabajo.php" onSubmit="return valida(this);">
	<input type="hidden" name="empleado_id" value="<?php echo $_GET['empleado_id']?>" />
	<input type="hidden" name="creado_por" value="<?php echo $user_id?>" />

	<div class="label">Horas 0001</div>
		<div class="content">
		<input type="text" name="horas_0001" value="<?php echo $horas_0001?>" size="5" />
		</div>
		<div style="clear:both;"></div>

	<div class="label">Horas 0002</div>
		<div class="content">
			<input type="text" name="horas_0002" value="<?php echo $horas_0002?>" size="5" />
		</div>
		<div style="clear:both;"></div>

	<div class="label">Duracion de la jornada</div>
		<div class="content">
			<input type="text" name="duracion_jornada" value="0" size="5" />
		</div>
		<div style="clear:both;"></div>


	<div class="label">Centro de costos</div>
		<div class="content">
			<select name="espacio_trabajo">
			<option value="null">Seleccionar...</option>
			<?php $sql = "SELECT * FROM espacio_trabajo ORDER BY espacio ASC";
			$rsTemp = mysql_query($sql);
			while($rs = mysql_fetch_array($rsTemp)){ 
				if($rs['id'] == $espacio_trabajo ){
					$selected = 'selected="selected"';
				}else{
					$selected = '';
				}
				?>
			<option <?php echo $selected?> value="<?php echo $rs['id']?>"><?php echo $rs['espacio']?></option>
			<?php } ?>
			</select>
		</div>
		<div style="clear:both;"></div>

	<div class="label">Sector 1</div>
		<div class="content">
			<select name="sector_1_id">
			<option value="null">Seleccionar...</option>
			<?php $sql = "SELECT * FROM sector ORDER BY sector ASC";
			$rsTemp = mysql_query($sql);
			while($rs = mysql_fetch_array($rsTemp)){ 
				if($rs['id'] == $sector_1_id ){
					$selected = 'selected="selected"';
				}else{
					$selected = '';
				}
				?>
			<option <?php echo $selected?> value="<?php echo $rs['id']?>"><?php echo $rs['sector']?></option>
			<?php } ?>
			</select>
		</div>
		<div style="clear:both;"></div>
		
	<div class="label">Sector 2</div>
		<div class="content">
			<select name="sector_2_id">
			<option value="null">Seleccionar...</option>
			<?php $sql = "SELECT * FROM sector ORDER BY sector ASC";
			$rsTemp = mysql_query($sql);
			while($rs = mysql_fetch_array($rsTemp)){ 
				if($rs['id'] == $sector_2_id ){
					$selected = 'selected="selected"';
				}else{
					$selected = '';
				}
				?>
			<option <?php echo $selected?> value="<?php echo $rs['id']?>"><?php echo $rs['sector']?></option>
			<?php } ?>
			</select>
		</div>
		<div style="clear:both;"></div>

	<div class="label">% Sector 1</div>
		<div class="content">
			<input type="text" name="porcentaje_sector_1" value="<?php echo $porcentaje_sector_1?>" size="5" onKeyUp="if(this.value > 100 || this.value < 0){ alert('El valor debe ser entre 0 y 100'); this.value=''; $('.por_2').val(''); }else{ $('.por_2').val(100 - this.value); }" />
		</div>
		<div style="clear:both;"></div>
		
	<div class="label">% Sector 2</div>
		<div class="content">
			<input class="por_2" type="text" disabled="disabled" name="porcentaje_sector_2_temp" value="<?php echo $porcentaje_sector_2?>" size="5" />
			<input class="por_2" type="hidden" name="porcentaje_sector_2" value="<?php echo $porcentaje_sector_2?>" />
		</div>
		<div style="clear:both;"></div>

		
</div>

 <p align="center"><input type="submit" value="Guardar" name="agregar" /></p>
</form>

</div>


</body>
</html>
