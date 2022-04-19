<?php
include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");

//indicar tabla a editar
$tabla = 'proveedor';

/*
	id	int(11)			No	None	auto_increment	 	 	 	 	 	 	
	nombre	varchar(250)	utf8_unicode_ci		No	None		 	 	 	 	 	 	 
	telefono	varchar(250)	utf8_unicode_ci		No	None		 	 	 	 	 	 	 
	direccion	varchar(250)	utf8_unicode_ci		No	None		 	 	 	 	 	 	 
	rubro_id	int(11)			No	None		 	 	 	 	 	 	
	cliente_nro	varchar(250)	utf8_unicode_ci		No	None		 	 	 	 	 	 	 
	cuit	varchar(250)	utf8_unicode_ci		No	None		 	 	 	 	 	 	 
	email	varchar(250)	utf8_unicode_ci		No	None		 	 	 	 	 	 	 
	contacto	varchar(250)	utf8_unicode_ci		No	None		 	 	 	 	 	 	 
*/

//indicar campos a editar
$campos['id'] 				= array(
								'type'				=> 'text',
								'input_type'		=> 'hidden'
							);
$campos['nombre'] 			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Nombre del proveedor',
								'requerid' 			=> true
							);
$campos['telefono'] 	= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Telefono',
								'requerid' 			=> true
							);
$campos['direccion'] 	= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Domicilio',
								'requerid' 			=> true
							);
$campos['cliente_nro'] 	= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Numero de cliente',
								'requerid' 			=> true
							);
$campos['razon'] 	= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Razon Social'
							);
$campos['cuit'] 	= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Cuit',
								'requerid' 			=> true
							);
$campos['email'] 	= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Email',
								'requerid' 			=> true
							);
$campos['contacto'] 	= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Persona de contacto',
								'requerid' 			=> true
							);
$campos['rubro_id']			= array(
								'type'				=> 'combo',
								'label'				=> 'Rubro',
								'tabla'				=> 'rubro',
								'campo_id'			=> 'id',
								'campo'				=> 'rubro'
							);


include_once("functions/common.php");

$form = new Form();
$form->setLegend('Proveedores'); //nombre del form
$form->setAction('proveedores.am.php'); //a donde hacer el post

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
		
	$form->setBotonValue('Editar proveedor'); //leyenda del boton editar
	$form->setBotonName('editar'); 
	
}else{

	$form->setBotonValue('Agregar proveedor'); //leyenda del boton agregar
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
