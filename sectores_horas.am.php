<?php
session_start();
$user_id = $_SESSION['userid'];
if($user_id == '') { header("Location: index.php"); }

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");

//indicar tabla a editar
$tabla = 'sector_horas_extras';

//indicar campos a editar
$campos['id'] 				= array(
								'type'				=> 'text',
								'input_type'		=> 'hidden'
							);
$campos['sector_id'] 		= array(
								'type'				=> 'text',
								'input_type'		=> 'hidden',
								'value'				=> $_GET['sector_id']
							);
$campos['creado'] 			= array(
								'type'				=> 'text',
								'input_type'		=> 'hidden',
								'value'				=> date("Y-m-d")
							);
$campos['creado_por'] 		= array(
								'type'				=> 'text',
								'input_type'		=> 'hidden',
								'value'				=> $user_id
							);
$campos['valor'] 			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Nuevo valor de hora extra',
								'requerid' 			=> true
							);

if(isset($_POST['agregar'])){
	$sql = "INSERT INTO sector_horas_extras 
				(sector_id,creado,creado_por,valor)
			VALUES
				(".$_POST['sector_id'].",'".$_POST['creado']."',".$_POST['creado_por'].",".$_POST['valor'].")";
	mysqli_query($conn,$sql); echo mysql_error();
	
	$sql = "UPDATE sector SET hora_extra_activa = ".mysql_insert_id()." WHERE id = ".$_POST['sector_id'];
	mysqli_query($conn,$sql); echo mysql_error();
}

$form = new Form();
$form->setLegend('Valor de horas extras'); //nombre del form
$form->setAction('sectores_horas.am.php'); //a donde hacer el post
$form->setBotonValue('Agregar sector'); //leyenda del boton agregar
$form->setBotonName('agregar');
$form->setCampos($campos);



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

<?php echo $form->printJS()?>

<link href="styles/form2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php  if(isset($_POST['agregar'])){?>
	<script>
	var dhxWins = parent.dhxWins;
	dhxWins.window('w_sector').attachURL('sectores.php');
	dhxWins.window('w_sector_horas').close();
	</script>
<?php  } ?>

<?php  include_once("config/messages.php"); ?>

<?php echo $form->printHTML()?>

</body>
</html>
