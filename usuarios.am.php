<?php
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/
include_once("functions/form.class.php");
include_once("config/db.php");
include_once("functions/abm.php");
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

//indicar tabla a editar
$tabla = 'usuario';

//indicar campos a editar
$campos['id'] 				= array(
								'type'				=> 'text',
								'input_type'		=> 'hidden'
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
$campos['telefono'] 		= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Telefono',
								'requerid' 			=> true
							);
$campos['email'] 			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Email',
								'requerid' 			=> true
							);
$campos['password'] 		= array(
								'type' 				=> 'text',
								'input_type'		=> 'password',
								'label' 			=> 'Password',
								'requerid' 			=> true
							);
if(!$_GET['comun']){
	$campos['espacio_trabajo_id'] = array(
									'type'				=> 'combo',
									'label'				=> 'Centro de costos',
									'tabla'				=> 'espacio_trabajo',
									'campo_id'			=> 'id',
									'campo'				=> 'espacio',
									'requerid' 			=> true
								);
	
	
	$campos['admin']            = array(
									'type'				=> 'checkbox',
									'label'				=> 'Administrador',
									'requerid' 			=> true
								);
    $campos['activo']            = array(
        'type'				=> 'checkbox',
        'label'				=> 'Activo',
        'requerid' 			=> true
    );
}
			

include_once("functions/common.php");

$form = new Form();
$form->setLegend('Datos del usuario'); //nombre del form
if(!$_GET['comun']){
	$form->setAction('usuarios.am.php'); //a donde hacer el post
}
else{
	$form->setAction('usuarios.am.php?comun=1&extras=off'); //a donde hacer el post
}

if(isset($dataid)){

	$sql = "SELECT * FROM usuario WHERE id=".$dataid;
	$rsTemp = mysqli_query($conn,$sql);
	$rs = mysqli_fetch_array($rsTemp);
	
	foreach($campos as $clave=>$atr){
		if($atr['type']=='date'){
			$campos[$clave]['value'] = fechavista($rs[$clave]);
		}elseif($atr['type']=='textarea'){
			$campos[$clave]['value'] = stripslashes($rs[$clave]);
		}else{
			$campos[$clave]['value'] = $rs[$clave];
		}
	}
		
	$form->setBotonValue('Editar usuario'); //leyendo del boton
	$form->setBotonName('editar'); 
	$form->setId($dataid);
	
}else{

	$form->setBotonValue('Agregar usuario'); //leyenda del boton
	$form->setBotonName('agregar');
	
}

$form->setCampos($campos);
if($_GET['extras']!="off"){
	$form->setExtraFileTop("empleado.list.php");
	$form->setExtraFileEnd("permisos.php");
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

<?php echo $form->printJS()?>

<link href="styles/form2.css" rel="stylesheet" type="text/css" />
</head>

<body>

<?php include_once("config/messages.php"); ?>

<?php echo $form->printHTML()?>

</body>
</html>
