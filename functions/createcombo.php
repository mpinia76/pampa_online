<?php
include_once("../config/db.php");

$tabla 		= $_GET['tabla'];
$campo_id 	= $_GET['campo_id'];
$campo 		= $_GET['campo'];
$value		= $_GET['value'];
?>

<select name="<?php echo $tabla?>_id">
<?php
$sql = "SELECT id,$campo FROM $tabla WHERE $campo_id=$value";
$sql .=($tabla=='subrubro')?" AND activo=1 ":"";
$rsTemp = mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){?>

	<option value="<?php echo $rs['id']?>"><?php echo $rs[$campo]?></option>

<?php } ?>

</select>