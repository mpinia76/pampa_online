<?php
//DATOS DEL USUARIO

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");

if(($_POST['agregar'])){
	$sql	= "SELECT * FROM transferencia_consumo WHERE id=".$_POST['registro'];
	$rs		= mysql_fetch_array(mysql_query($sql));
	
	$cuenta_id	= $rs['cuenta_id'];
	$origen		= 'transferencia';
	$registro_id	= $_POST['registro'];
	
	
	$monto		= $rs['monto'] + $rs['interes'] - $rs['descuento'];

	$insert = "INSERT INTO cuenta_movimiento (cuenta_id,origen,registro_id,monto,fecha) VALUES ($cuenta_id,'$origen',$registro_id,-$monto,'".fechasql($_POST['fecha'])."')";
	//echo $insert."<br>";
	mysql_query($insert);
	
	$update = "UPDATE transferencia_consumo SET debitado=1, fecha_debitada='".fechasql($_POST['fecha'])."' WHERE id=$registro_id";
	mysql_query($update);
	//echo $update."<br>";
	
	$result = 1;
	echo "<script>
    	window.parent.dhxWins.window('w_transferencia_consumo_debitar').close();

		</script>";
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


function valida(F) {

	
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

<form method="POST" name="form" action="transferencias_debitar.php" onSubmit="return valida(this);">
<input name="agregar" id="agregar" type="hidden" value="0">
<input name="registro" id="registro" type="hidden" value="<?php echo $_GET['dataid']?>">
<div class="label">Fecha</div><div class="content"><input type="text" class="fecha dp-applied" name="fecha" value="<?php echo date("d/m/Y")?>" /></div><div style="clear:both;"></div>







</div>

<p align="center"><input type="submit" value="Confirmar debito" name="agregarSubmit" id="agregarSubmit" /></p>

</form>
 

</div>


</body>
</html>