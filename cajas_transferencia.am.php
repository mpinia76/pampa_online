<?
session_start();
$user_id = $_SESSION['userid'];

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");

//indicar tabla a editar
$tabla = 'caja_movimiento';

//indicar campos a editar
$campos['id'] 				= array(
								'type'				=> 'text',
								'input_type'		=> 'hidden'
							);
$campos['fecha'] 			= array(
								'type'		=> 'date',
								'label'		=> 'Fecha',
								'value'		=> date('d/m/Y')
							);
$campos['origen']			= array(
								'type'				=> 'combo',
								'label'				=> 'Caja origen',
								'tabla'				=> 'caja',
								'campo_id'			=> 'id',
								'campo'				=> 'caja',
								'sql'				=> "SELECT caja.id as id, caja.caja as caja FROM caja INNER JOIN usuario_caja ON usuario_caja.caja_id=caja.id AND usuario_caja.usuario_id=$user_id",
								'requerid'			=> true
							);
$campos['caja_id']			= array(
								'type'				=> 'combo',
								'label'				=> 'Caja destino',
								'tabla'				=> 'caja',
								'campo_id'			=> 'id',
								'campo'				=> 'caja',
								'sql'				=> "SELECT caja.id as id, caja.caja as caja FROM caja INNER JOIN usuario_caja ON usuario_caja.caja_id=caja.id AND usuario_caja.usuario_id=$user_id",
								'requerid'			=> true
							);
$campos['monto']			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Monto',
								'size'				=> '5',
								'requerid' 			=> true
							);

if(isset($_POST['agregar'])){
	//proceso la entrada de plata a la caja
	$sql_entra = "INSERT INTO $tabla (fecha,origen,caja_id,monto,usuario_id) 
				VALUES 
				('".fechasql($_POST['fecha'])."','caja_".$_POST['origen']."','".$_POST['caja_id']."','".$_POST['monto']."',$user_id)";
	mysql_query($sql_entra);
	echo mysql_error();
	
	//proceso la salida de plata
	$sql_sale = "INSERT INTO $tabla (fecha,origen,caja_id,monto,usuario_id) 
				VALUES 
				('".fechasql($_POST['fecha'])."','hacia_".$_POST['caja_id']."','".$_POST['origen']."','-".$_POST['monto']."',$user_id)";
	mysql_query($sql_sale);
	
	$result = 1;
}

$form = new Form();
$form->setLegend('Movimientos en efectivo'); //nombre del form
$form->setAction('cajas_transferencia.am.php'); //a donde hacer el post

if(isset($dataid)){

	$sql = "SELECT * FROM $tabla WHERE id=".$dataid; //traer datos
	$rsTemp = mysql_query($sql);
	$rs = mysql_fetch_array($rsTemp);
	
	foreach($campos as $clave=>$valores){
		$campos[$clave][3] = $rs[$clave];
	}
		
	$form->setBotonValue('Editar caja'); //leyendo del boton
	$form->setBotonName('editar'); 
	
}else{

	$form->setBotonValue('Hacer transferencia'); //leyenda del boton
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

<?=$form->printJS()?>

<link href="styles/form2.css" rel="stylesheet" type="text/css" />
</head>

<body>

<? include_once("config/messages.php"); ?>

<?=$form->printHTML()?>

</body>
</html>
