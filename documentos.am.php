<?php
include_once("functions/form.class.php");
include_once("config/db.php");
include_once("functions/abm.php");

//indicar tabla a editar
$tabla = 'documento';

//indicar campos a editar
$campos['id'] 				= array(
								'type'				=> 'text',
								'input_type'		=> 'hidden'
							);
$campos['path'] 			= array(
    'type'			=> 'file',
    'label' 		=> 'Archivo',
    'infotext'		=> 'Seleccione un archivo',
    'extensions' 	=> 'pdf|doc|docx|rtf',
    'folder'		=> 'documentos',
    'requerid' 			=> true
);

			

include_once("functions/common.php");

$form = new Form();
$form->setLegend('Documentos'); //nombre del form
$form->setAction('documentos.am.php'); //a donde hacer el post

if(isset($dataid)){

	$sql = "SELECT * FROM documento WHERE id=".$dataid;
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
		
	$form->setBotonValue('Editar documento'); //leyendo del boton
	$form->setBotonName('editar'); 
	$form->setId($dataid);
	
}else{

	$form->setBotonValue('Agregar documento'); //leyenda del boton
	$form->setBotonName('agregar');
	
}

$form->setCampos($campos);




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="library/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="library/jquery.fileupload/jquery.fileupload.css">
    <script src="library/jquery.fileupload/jquery.fileupload.js"></script>
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

<?php echo $form->printHTML(1)?>

</body>
</html>
