<?php
include_once("config/db.php");

$sql = "SELECT * FROM empleado WHERE id=".$_GET['empleado_id']." AND estado = 1";
$rs = mysql_fetch_array(mysql_query($sql));

?>
<script>

$('input[name=nombre]').val('<?php echo $rs['nombre']?>');
$('input[name=apellido]').val('<?php echo $rs['apellido']?>');
$('input[name=telefono]').val('<?php echo $rs['telefono_cel']?>');
$('input[name=email]').val('<?php echo $rs['email']?>');

</script>