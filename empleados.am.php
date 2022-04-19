<?php
session_start();
$user_id = $_SESSION['userid'];

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");

//indicar tabla a editar
$tabla = 'empleado';

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
$campos['nombre'] 			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Nombre',
								'requerid' 			=> true
							);
$campos['apellido'] 		= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Apellido',
								'requerid' 			=> true
							);
$campos['telefono_fijo'] 	= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Telefono fijo',
								'requerid' 			=> true
							);
$campos['telefono_cel'] 	= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Telefono Celular',
								'requerid' 			=> false
							);
$campos['domicilio_dni'] 	= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Domicilio del DNI',
								'requerid' 			=> true
							);
$campos['domicilio_reside'] = array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Domicilio de residencia',
								'requerid' 			=> false
							);
$campos['localidad'] = array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Localidad',
								'requerid' 			=> false
							);

$campos['provincia'] = array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Provincia',
								'requerid' 			=> false
							);


$campos['nacimiento'] 		= array(
								'type'		=> 'date',
								'label'		=> 'Fecha de nacimiento',
								'requerid'	=> true
							);
$campos['estudios'] 		= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Nivel de estudios',
								'requerid' 			=> true
							);
$campos['estado_civil'] 	= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Estado civil',
								'requerid' 			=> true
							);
$campos['cant_hijos'] 		= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Cantidad de hijos',
								'requerid' 			=> true
							);
$campos['dni'] 				= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'DNI',
								'maxlength' 			=> '8',	
								'requerid' 			=> true
							);
$campos['cuil'] 			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'CUIL',
								'requerid' 			=> true
							);
$campos['email'] 			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Email'
							);
$campos['foto'] 			= array(
								'type'			=> 'file',
								'label'			=> 'Imagen',
								'infotext'		=> 'Seleccione una imagen',
								'extensions' 	=> '*.jpg;*.jpeg;*.png;*.gif',
								'folder'		=> 'empleados'
							);
$campos['fecha_alta'] 		= array(
								'type'		=> 'date',
								'label'		=> 'Fecha de alta',
								'requerid' 	=> true
							);
$campos['inicio_actividades'] = array(
								'type'		=> 'date',
								'label'		=> 'Inicio de actividades'
							);
$campos['nro_legajo'] 		= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Nro. de legajo',
								'requerid' 			=> true
							);

include_once("functions/common.php");

$form = new Form();
$form->setLegend('Empleados'); //nombre del form
$form->setAction('empleados.am.php'); //a donde hacer el post

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
		
	$form->setBotonValue('Editar empleado'); //leyenda del boton editar
	$form->setBotonName('editar'); 
	
}else{

	$form->setBotonValue('Agregar empleado'); //leyenda del boton agregar
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
<script src="library/dhtml/js/dhtmlxcommon.js"></script>
<script src="js/createWindow.js"></script>
<script>
var dhxWins = parent.dhxWins;

var position = dhxWins.window('w_empleado').getPosition(); //id de la ventana

var xpos = position[0];
var ypos = position[1];
function documentos(){
	createWindow('w_documentos','Imprimir documentos','documentos.php?sinBarra=1','600','400'); //botones
}

</script>
<link href="library/datepicker/datePicker.css" rel="stylesheet" type="text/css" />
<link rel="STYLESHEET" type="text/css" href="styles/toolbar.css">
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

<?php $form->printJS()?>

<link href="styles/form2.css" rel="stylesheet" type="text/css" />
</head>

<body>

<?php include_once("config/messages.php"); ?>
<ul id="menu">
	
	<li onclick="documentos()" class="item"><img src="images/bt_print.png" align="absmiddle" />  Imprimir documentos</li>
	
</ul>
<?php 
$form->printHTML()

?>

</body>
</html>
