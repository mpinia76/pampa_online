<?
include("admin/config.php");
$conn = mysql_connect($host, $db_user, $db_pass);
mysql_select_db($db);

$sql="SELECT d.id,d.nombre,c.titulo FROM departamentos d INNER JOIN categoria c ON d.id_categoria = c.id";
$rsTemp=mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){
    $data[] = array(
        'id' => $rs['id'],
        'nombre' => $rs['nombre'],
        'categoria' => $rs['titulo']
    );
}
echo json_encode($data);
?>
