<?php
include_once("model/form.class.php");
include_once("config/db.php");
include_once("functions/abm.php");

//indicar tabla a editar
$tabla = 'cuenta';

//indicar campos a editar
$campos['banco_id'] 		= array('combo','Banco',1,'','banco','id','banco');
$campos['cuenta_tipo_id'] 	= array('combo','Tipo de cuenta',1,'','cuenta_tipo','id','cuenta_tipo');
$campos['numero'] 		= array('text','Numero',1);
$campos['sucursal'] 		= array('text','Sucursal',1);
$campos['nombre'] 		= array('text','Nombre',1);
$campos['CUIT'] 		= array('text','CUIT');
$campos['visible_en_informe'] 	= array('checkbox','Mostrar en Informes');
$campos['controla_facturacion'] 	= array('checkbox','Controla Facturacion');
$campos['emite_cheques'] 	= array('checkbox','Emite Cheques');
$campos['id'] 		= array('text','',0,'','','hidden');

include_once("functions/common.php");

$form = new Form();
$form->setLegend('Cuentas de bancos'); //nombre del form
$form->setAction('cuentas.am.php'); //a donde hacer el post

if(isset($dataid)){

	$sql = "SELECT * FROM $tabla WHERE id=".$dataid; //traer datos
	$rsTemp = mysql_query($sql);
	$rs = mysql_fetch_array($rsTemp);
	
	foreach($campos as $clave=>$valores){
		$campos[$clave][3] = $rs[$clave];
	}
		
	$form->setBotonValue('Editar cuenta'); //leyendo del boton
	$form->setBotonName('editar'); 
	
}else{

	$form->setBotonValue('Agregar cuenta'); //leyenda del boton
	$form->setBotonName('agregar');
	
}

$form->setCampos($campos, $conn);



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
