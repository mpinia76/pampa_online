<?
include_once("config/db.php");

$sql = "SELECT * FROM empleado WHERE id=".$_GET['empleado_id']." AND estado = 1";
$rs = mysql_fetch_array(mysql_query($sql));

?>
<script>

$('input[name=nombre]').val('<?=$rs['nombre']?>');
$('input[name=apellido]').val('<?=$rs['apellido']?>');
$('input[name=telefono]').val('<?=$rs['telefono_cel']?>');
$('input[name=email]').val('<?=$rs['email']?>');

</script>