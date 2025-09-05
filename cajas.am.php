<?php
include_once("model/form.class.php");
include_once("config/db.php");
include_once("functions/abm.php");

//indicar tabla a editar
$tabla = 'caja';

//indicar campos a editar
$campos['caja'] 			= array('text','Nombre de la caja');
$campos['visible_en_informe'] 	= array('checkbox','Mostrar en Informes');
$campos['descubierto'] 	= array('checkbox','Movimientos Descubierto');
$campos['sincronizacion'] 	= array('checkbox','Pedir sincronizacion');
$campos['dias_sincronizacion'] 			= array('text','Dias sincronizacion');
$campos['id'] 				= array('text','',0,'','','hidden');

include_once("functions/common.php");

$form = new Form();
$form->setLegend('Cajas de efectivo'); //nombre del form
$form->setAction('cajas.am.php'); //a donde hacer el post

if(isset($dataid)){

	$sql = "SELECT * FROM $tabla WHERE id=".$dataid; //traer datos
	$rsTemp = mysqli_query($conn,$sql);
	$rs = mysqli_fetch_array($rsTemp);
	
	foreach($campos as $clave=>$valores){
		$campos[$clave][3] = $rs[$clave];
	}
		
	$form->setBotonValue('Editar caja'); //leyendo del boton
	$form->setBotonName('editar'); 
	
}else{

	$form->setBotonValue('Agregar caja'); //leyenda del boton
	$form->setBotonName('agregar');
	
}

$form->setCampos($campos);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<?php echo $form->printJS()?>
<link href="styles/form.css" rel="stylesheet" type="text/css" />
</head>

<body>

<?php include_once("config/messages.php"); ?>

<?php echo $form->printHTML()?>

</body>
</html>
