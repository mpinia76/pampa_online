<?php
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
?>
<script>
function findEmpleado (){
	$.ajax({
		beforeSend: function(){
			$('#loading').show();
		},
		data: { 'empleado_id' : $('#empleado_id').val() },
		url: 'empleado.get.php',
		success: function(data) {
			$('#loading').hide();
			$('#result').html(data);
		}
	});

}
</script>
<div id="result"></div>
<div class="label">Es empleado?</div>
<div class="content">
<select id="empleado_id" name="empleado_id" onChange="findEmpleado();">
<option value="null" selected="selected">Seleccionar...</option>

<?php


$sql = "SELECT id,nombre,apellido FROM empleado WHERE estado = 1 ORDER BY apellido ASC";

$rsTemp = mysqli_query($conn,$sql);

while($rs = mysqli_fetch_array($rsTemp)){

?>

<option value="<?php echo $rs['id']?>"><?php echo $rs['apellido']?> <?php echo $rs['nombre']?></option>

<?php } ?>

</select> <img id="loading" src="images/loading.gif" style="display:none" />

</div>
<div style="clear:both;"></div>
