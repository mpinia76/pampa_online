<?
include_once("../config/db.php");

$tabla 		= $_GET['tabla'];
$campo_id 	= $_GET['campo_id'];
$campo 		= $_GET['campo'];
$value		= $_GET['value'];
?>

<select name="<?=$tabla?>_id">
<?
$sql = "SELECT id,$campo FROM $tabla WHERE $campo_id=$value";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){?>

	<option value="<?=$rs['id']?>"><?=$rs[$campo]?></option>

<? } ?>

</select>