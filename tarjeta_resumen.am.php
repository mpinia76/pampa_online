<?php
include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");

//indicar tabla a editar
$tabla = 'tarjeta_resumen';

//indicar campos a editar
$campos['id'] 				= array(
								'type'				=> 'text',
								'input_type'		=> 'hidden'
							);
$campos['tarjeta_id']		= array(
								'type'				=> 'combo',
								'label'				=> 'Tarjeta',
								'sql'				=> "SELECT CONCAT(banco.banco,' ',tarjeta_marca.marca,' ',tarjeta.titular) AS tarjeta,tarjeta.id FROM tarjeta INNER JOIN tarjeta_marca ON tarjeta.tarjeta_marca_id=tarjeta_marca.id INNER JOIN banco ON tarjeta.banco_id=banco.id WHERE tarjeta.activa=1",
								'campo_id'			=> 'id',
								'campo'				=> 'tarjeta'
							);
$campos['nombre'] 			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Nombre del periodo',
								'requerid' 			=> true
							);
$campos['inicio'] 			= array(
								'type' 				=> 'date',
								'label' 			=> 'Inicio',
								'requerid' 			=> true
							);
$campos['fin'] 				= array(
								'type' 				=> 'date',
								'label' 			=> 'Fin',
								'requerid' 			=> true
							);
$campos['vencimiento'] 		= array(
								'type' 				=> 'date',
								'label' 			=> 'Vencimiento',
								'requerid' 			=> true
							);
$campos['mes'] 				= array(
								'type'				=> 'combo',
								'label'				=> 'Mes donde aplica',
								'campo_id'			=> 'id',
								'tabla'				=> 'mes',
								'campo'				=> 'mes',
								'requerid' 			=> true
							);
$campos['ano'] 				= array(
								'type'				=> 'combo',
								'label'				=> 'A&ntilde;o donde aplica',
								'campo_id'			=> 'id',
								'tabla'				=> 'ano',
								'campo'				=> 'ano',
								'requerid' 			=> true
							);


include_once("functions/common.php");

$form = new Form();
$form->setLegend('Resumen de tarjeta'); //nombre del form
$form->setAction('tarjeta_resumen.am.php'); //a donde hacer el post

if(isset($dataid)){

	$sql = "SELECT * FROM $tabla WHERE id=".$dataid; //traer datos
	$rsTemp = mysql_query($sql);
	$rs = mysql_fetch_array($rsTemp);
	
	foreach($campos as $clave=>$atr){
		if($atr['type']=='date'){
			$campos[$clave]['value'] = fechavista($rs[$clave]);

		}elseif($atr['type']=='textarea'){
			$campos[$clave]['value'] = stripslashes($rs[$clave]);
		}else{
			$campos[$clave]['value'] = $rs[$clave];

		}

	}
		
	$form->setBotonValue('Editar resumen'); //leyenda del boton editar
	$form->setBotonName('editar'); 
	
}else{

	$form->setBotonValue('Agregar resumen'); //leyenda del boton agregar
	$form->setBotonName('agregar');
	
}

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

<?php include_once("config/messages.php"); ?>

<?php echo $form->printHTML()?>

</body>
</html>
