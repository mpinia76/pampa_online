<?php
session_start();
include_once("model/form.class.php");


include_once("config/db.php");
include_once("functions/util.php");
$sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','Gastos, Compras, Impuestos, Tasas y Cargas sociales: Montos Aprobacion','".getRealIP()."')";
mysql_query($sql);
$date = date('Y-m-d');
$sqlAuditoria ="SELECT * FROM usuario_auditoria WHERE usuario_id = ".$_SESSION['userid']." AND fecha='".$date."'";
$rsTempAuditoria = mysql_query($sqlAuditoria);
$totalAuditoria = mysql_num_rows($rsTempAuditoria);

if($totalAuditoria == 1) {
    $rsAuditoria = mysql_fetch_array($rsTempAuditoria);
    $last_interaction = strtotime($rsAuditoria['last']);

    // Calcula los segundos entre la última interacción y el tiempo actual
    $elapsed_time_seconds = time() - $last_interaction;
    //$elapsed_time_minutes = round($elapsed_time_seconds / 60);

    // Actualiza la hora de última interacción y segundos conectados
    $sql_update = "UPDATE usuario_auditoria SET last = now(), interaccion='Gastos, Compras, Impuestos, Tasas y Cargas sociales: Montos Aprobacion', segundos = segundos + $elapsed_time_seconds WHERE usuario_id = " . $_SESSION['userid'] . " AND fecha = '$date'";
    mysql_query($sql_update);

}
include_once("functions/abm.php");

//indicar tabla a editar
$tabla = 'configuracion';

//indicar campos a editar
$sql = "SELECT * FROM $tabla";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
$campos[$rs['id']] = array('text',$rs['descripcion'],1,$rs['valor']);
}

if(isset($_POST['editar'])){
	foreach($campos as $key=>$value){
		$sql = "UPDATE $tabla SET valor = '$_POST[$key]' WHERE id = '$key'";
		mysql_query($sql);
		$campos[$key][3] = $_POST[$key];
	}
	$result = 2;
}

$form = new Form();
$form->setLegend('Configuracion'); //nombre del form
$form->setAction('configuracion.am.php'); //a donde hacer el post
$form->setBotonValue('Guardar'); //leyendo del boton
$form->setBotonName('editar'); 
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

<?php  include_once("config/messages.php"); ?>

<?php echo $form->printHTML()?>

</body>
</html>
