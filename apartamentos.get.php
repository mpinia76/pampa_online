<?php
include_once("config/db.php");

$sql = "SELECT * FROM apartamentos WHERE categoria_id=".$_GET['categoria']." order by orden";

$rsTemp = mysql_query($sql);

?>
<select name="ApartamentoId[]" multiple="multiple" style="float:left;height:40px; width:200px; margin:2px 0px" id="ApartamentoId">
	<?php
	while($rs = mysql_fetch_array($rsTemp)){
	
	?>
	
	<option value="<?php echo $rs['id']?>"><?php echo $rs['apartamento']?> </option>
	
	<?php } ?>
	
</select>