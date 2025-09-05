<?php
session_start();
$user_id = $_SESSION['userid'];
if($user_id == '') { header("Location: index.php"); }

//resta modificar los datos del grid

include_once("functions/delete.php");
include_once("config/db.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link rel="STYLESHEET" type="text/css" href="styles/toolbar.css">
<!--dhtmlGrid-->
<link rel="STYLESHEET" type="text/css" href="library/dhtml/styles/dhtmlxgrid.css">
<link rel="STYLESHEET" type="text/css" href="library/dhtml/styles/dhtmlxgrid_dhx_skyblue.css">
<script src="library/dhtml/js/dhtmlxcommon.js"></script>
<script src="library/dhtml/js/dhtmlxgrid.js"></script>
<script src="library/dhtml/js/dhtmlxgridcell.js"></script>
<script src="library/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="js/createWindow.js"></script>
<style>
body{
	background:#fff;
	font-family:arial;
	font-size:12px;
}
</style>
</head>

<?php
if(isset($_POST['id'])){
	$sql = "UPDATE empleado_hora_extra SET estado = 1, cantidad_aprobada = ".$_POST['horas_aprobadas'].", hora_extra_id = ".$_POST['hora_extra_activa'].", aprobado_por = $user_id, aprobado = NOW()  WHERE id = ".$_POST['id'];
	mysqli_query($conn,$sql);
	echo mysql_error();
?>
	<script>
	var dhxWins = parent.dhxWins;
	dhxWins.window('w_empleado_view').attachURL('empleados.ficha.php?empleado_id=<?php echo $_POST['empleado_id']?>');
	dhxWins.window('w_empleado_hora_extra').attachURL('horas_extras.php?empleado_id=<?php echo $_POST['empleado_id']?>');
	dhxWins.window('w_empleado_hora_extra_aprobar').close();
	</script>
<?php } ?>

<body>
<?php
$sql = "SELECT ehe.id, s.hora_extra_activa FROM empleado_hora_extra ehe INNER JOIN sector_horas_extras she ON ehe.hora_extra_id = she.id INNER JOIN sector s ON she.sector_id = s.id  WHERE ehe.id=".$_GET['id'];
$rs = mysqli_fetch_array(mysqli_query($conn,$sql));
?>

<form method="POST" action="horas_extras_aprobar.php">
<input type="hidden" value="<?php echo $rs['hora_extra_activa']?>" name="hora_extra_activa" />
<input type="hidden" value="<?php echo $rs['id']?>" name="id" />
<input type="hidden" value="<?php echo $rs['empleado_id']?>" name="empleado_id" />
<p><b>Horas extras aprobadas:</b> <input type="text" size="5" name="horas_aprobadas" value="<?php echo $rs['cantidad_solicitada']?>" /><p>
<p align="center"><input type="submit" value="Guardar"></p>
</form>

</body>
</html>
