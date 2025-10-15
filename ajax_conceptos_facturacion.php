<?php
include_once("config/db.php");
header('Content-Type: application/json');

$sql = "SELECT id, nombre FROM concepto_facturacions ORDER BY nombre ASC";
$rs = mysqli_query($conn, $sql);
$result = [];
while ($row = mysqli_fetch_assoc($rs)) {
    $result[] = $row;
}
echo json_encode($result);
