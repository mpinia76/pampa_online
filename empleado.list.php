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

<?
include_once("config/db.php");

$sql = "SELECT id,nombre,apellido FROM empleado WHERE estado = 1 ORDER BY apellido ASC";
$rsTemp = mysql_query($sql);

while($rs = mysql_fetch_array($rsTemp)){

?>

<option value="<?=$rs['id']?>"><?=$rs['apellido']?> <?=$rs['nombre']?></option>

<? } ?>

</select> <img id="loading" src="images/loading.gif" style="display:none" />

</div>
<div style="clear:both;"></div>