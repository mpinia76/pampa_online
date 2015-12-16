<?php
session_start();
$user_id = $_SESSION['userid'];

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");

$tabla = 'cheque_consumo';


//indicar campos a editar
$campos['id'] 				= array(
								'type'				=> 'text',
								'input_type'		=> 'hidden'
							);
$campos['creado_por'] 		= array(
								'type'				=> 'text',
								'input_type'		=> 'hidden',
								'value'				=> $user_id
							);
$campos['cuenta_id']		= array(
								'type'				=> 'combo',
								'label'				=> 'Cuenta',
								'campo_id'			=> 'cuenta.id',
								'column_id'			=> 'id',
								'campo'				=> 'concat(banco.banco," ",cuenta.sucursal," ",cuenta_tipo.cuenta_tipo," ",cuenta.nombre)',
								'tabla'				=> 'cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id',
								'requerid' 			=> true
							);
$campos['numero'] 			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Numero',
								'requerid' 			=> true
							);
$campos['fecha'] 			= array(
								'type' 				=> 'date',
								'label' 			=> 'Fecha',
								'requerid' 			=> true
							);
$campos['titular'] 		= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Titular',
								'requerid' 			=> true
							);
$campos['monto'] 			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'size'				=> '5',
								'label' 			=> 'Monto $',
								'requerid' 			=> true
							);
$campos['interes'] 			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'size'				=> '5',
								'label' 			=> 'Interes $',
								'value'				=> 0,
								'requerid' 			=> true
							);
$campos['descuento'] 		= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'size'				=> '5',
								'label' 			=> 'Descuento $',
								'value'				=> 0,
								'requerid' 			=> true
							);
$campos['concepto'] 		= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Concepto'
							);
$campos['vencido'] 		= array(
								'type' 			=> 'checkbox',
								'label' 			=> 'Vencido'
							);
																			

include_once("functions/common.php");

$form = new Form();
$form->setLegend('Cheque'); //nombre del form
$form->setAction('cheque_consumo.am.php'); //a donde hacer el post

if(isset($dataid)){

	$sql = "SELECT * FROM cheque_consumo WHERE id=".$dataid;
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
		
	$form->setBotonValue('Editar cheque'); //leyendo del boton
	$form->setBotonName('editar'); 
	$form->setId($dataid);
	
}else{

	$form->setBotonValue('Agregar cheque'); //leyenda del boton
	$form->setBotonName('agregar');
	
}

$form->setCampos($campos);



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

<?php echo $form->printJS()?>

<link href="styles/form2.css" rel="stylesheet" type="text/css" />
</head>

<body>

<?php include_once("config/messages.php"); ?>

<?php echo $form->printHTML()?>

</body>
</html>
