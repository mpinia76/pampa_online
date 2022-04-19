<?php
include_once("../config/db.php");

$cuenta_id 		= $_GET['cuenta_id'];
$div_id 		= $_GET['div_id'];
?>

<select name="cheque_chequera_id[]" id="cheque_chequera_id_<?php echo  $div_id?>" onChange="seleccionarChequera()">
<option value="">Seleccionar...</option>
<?php
$sql = "SELECT * FROM chequeras WHERE estado = 1 AND cuenta_id=$cuenta_id";

$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){?>

	<option value="<?php echo $rs['id']?>"><?php echo $rs['numero'].' ('.$rs['inicio'].' - '.$rs['final'].')'?></option>

<?php } ?>

</select>