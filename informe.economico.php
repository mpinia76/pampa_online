<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="library/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="library/flexigrid/flexigrid.css">
<script type="text/javascript" src="library/flexigrid/flexigrid.js"></script> 
<title>Documento sin t&iacute;tulo</title>
<style>
a{
text-decoration:underline;
color:#0000FF;
cursor:pointer;
}
</style>
</head>

<body>
<?php 

session_start();
include_once("config/db.php"); 

include_once("functions/util.php");
$sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','Informes economico - Egresos','".getRealIP()."')";
mysql_query($sql);

?>
<?php if(isset($_POST['ano'])) { $ano = $_POST['ano']; }else{ $ano= date('Y'); } 

if(isset($_POST['espacio_trabajo'])) { $espacio_trabajo = $_POST['espacio_trabajo']; }?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<select size="1" name="ano">
	<option <?php if($ano == '2010'){?> selected="selected" <?php } ?> >2010</option>
	<option <?php if($ano == '2011'){?> selected="selected" <?php } ?> >2011</option>
	<option <?php if($ano == '2012'){?> selected="selected" <?php } ?> >2012</option>
	<option <?php if($ano == '2013'){?> selected="selected" <?php } ?> >2013</option>
	<option <?php if($ano == '2014'){?> selected="selected" <?php } ?> >2014</option>
	<option <?php if($ano == '2015'){?> selected="selected" <?php } ?> >2015</option>
    <option <?php if($ano == '2016'){?> selected="selected" <?php } ?> >2016</option>
    <option <?php if($ano == '2017'){?> selected="selected" <?php } ?> >2017</option>
    <option <?php if($ano == '2018'){?> selected="selected" <?php } ?> >2018</option>
    <option <?php if($ano == '2019'){?> selected="selected" <?php } ?> >2019</option>
    <option <?php if($ano == '2020'){?> selected="selected" <?php } ?> >2020</option>
    <option <?php if($ano == '2021'){?> selected="selected" <?php } ?> >2021</option>
    <option <?php if($ano == '2022'){?> selected="selected" <?php } ?> >2022</option>
    <option <?php if($ano == '2023'){?> selected="selected" <?php } ?> >2023</option>
</select> 
<select name="espacio_trabajo">
			
			<?php 
			if ($_SESSION['admin']) {
				echo '<option value="0">Seleccionar...</option>';
				$sql = "SELECT * FROM espacio_trabajo ORDER BY espacio ASC";
			}
			else{
				$sql = "SELECT espacio_trabajo.id, espacio_trabajo.espacio FROM espacio_trabajo 
				INNER JOIN usuario ON espacio_trabajo.id = usuario.espacio_trabajo_id  
				WHERE usuario.id = ".$_SESSION['userid']."  
				ORDER BY espacio ASC";
			}
			
			
			$rsTemp = mysql_query($sql);
			while($rs = mysql_fetch_array($rsTemp)){ 
			if (!$espacio_trabajo&&!$_SESSION['admin']) {
				$espacio_trabajo=$rs['id'];
			}	
			$selected = ($espacio_trabajo==$rs['id'])?'selected="selected"':'';
				?>
			<option value="<?php echo $rs['id']?>" <?php echo $selected?>><?php echo $rs['espacio']?></option>
			<?php } ?>
			</select>
<input type="submit" value=">" name="buscar" />
</form>

<?php 

include("informe.economico.gasto.php"); ?><br />

<?php //include("informe.economico.compra.php"); ?><br />

<!-- <strong>Total</strong>: $<span id="total_total"></span>
<script>
var total = parseFloat(gasto) + parseFloat(compra);
$('#total_total').html(total);
</script> -->

</body>
</html>
