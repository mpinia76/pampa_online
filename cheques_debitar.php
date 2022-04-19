<?php
//DATOS DEL USUARIO
session_start();
$user_id = $_SESSION['userid'];

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");
include_once("config/user.php");
$error=0;
if(($_POST['agregar'])){
	
	if((date("Y-m-d")) >= fechasql($_POST['fecha'])){
		$sql	= "SELECT * FROM cheque_consumo WHERE id=".$_POST['registro'];
		$rs		= mysql_fetch_array(mysql_query($sql));
		//echo $sql;
		if(($rs['fecha']) <= fechasql($_POST['fecha'])){ //la fecha de pago es menor o igual a la fecha de hoy
			$cuenta_id	= $rs['cuenta_id'];
			$origen		= 'cheque';
			$registro_id	= $_POST['registro'];
			
			$monto		= $rs['monto'];
			if ($_POST['actualizar']) {
				mysql_query("DELETE FROM cuenta_movimiento WHERE cuenta_id = ".$cuenta_id." AND registro_id = ".$registro_id);
			}
		
			$insert = "INSERT INTO cuenta_movimiento (cuenta_id,origen,registro_id,monto,fecha) VALUES ($cuenta_id,'$origen',$registro_id,-$monto,'".fechasql($_POST['fecha'])."')";
			mysql_query($insert);
			//echo $insert."<br>";
			$update = "UPDATE cheque_consumo SET debitado=1, fecha_debitado='".fechasql($_POST['fecha'])."', debitado_por=$user_id  WHERE id=".$rs['id'];
			mysql_query($update);
			//echo $update."<br>";
			$result = 1;
			echo "<script>
		    	window.parent.dhxWins.window('w_cheque_consumo').attachURL('v2/cheque_consumos/index');
				window.parent.dhxWins.window('w_cheque_consumo_debitar').close();
		
				</script>";
		}else{
			$error = 1;
		}
		
		//echo $update."<br>";
	}else{
		$error = 2;
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


function valida(F) {

	
	$('#agregarSubmit').val('Procesando...');
	$('#agregarSubmit').attr('disabled','disabled');
	$('#agregar').val('1');

}

function inicializarCalendario() {

	var ToEndDate = new Date();	

	var fullDate = new Date()
	var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
	 
	var currentDate = fullDate.getDate() + "/" + twoDigitMonth + "/" + fullDate.getFullYear();

	$('.fecha').datePicker({
	
	    weekStart: 1,
	    startDate: '01/01/2010',
	    endDate: currentDate, 
	    autoclose: true
	})
}
					
</script>



<link href="styles/form2.css" rel="stylesheet" type="text/css" />
</head>
<body onload="inicializarCalendario()">


<?php 
switch ($error) {
	case 1:
		echo '<script>
	alert("La fecha de pago del cheque debe ser inferior o igual a la fecha de hoy");
	</script>';
	break;
	case 2:
		echo '<script>
	alert("La fecha de debito debe ser inferior o igual a la fecha de hoy");
	</script>';
	break;
	

}
if ($error) {$registro = $_POST['registro']; } else $registro = $_GET['dataid'];
if ($error) {$actualizar = $_POST['actualizar']; } else $actualizar = $_GET['actualizar'];?>

<?php  include_once("config/messages.php"); ?>

<div class="container">

<form method="POST" name="form" action="cheques_debitar.php" onSubmit="return valida(this);">
<input name="agregar" id="agregar" type="hidden" value="0">
<input name="registro" id="registro" type="hidden" value="<?php echo $registro?>">
<input name="actualizar" id="actualizar" type="hidden" value="<?php echo $actualizar?>">
<div class="label">Fecha</div><div class="content"><input type="text" class="fecha dp-applied" name="fecha" value="<?php echo date("d/m/Y")?>" /></div><div style="clear:both;"></div>







</div>

<p align="center"><input type="submit" value="Confirmar debito" name="agregarSubmit" id="agregarSubmit" /></p>

</form>
 

</div>


</body>
</html>