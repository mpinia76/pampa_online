<?php
header('Content-type: text/html; charset=UTF-8');

include_once("config/db.php");

$sql = "SELECT id,nombre FROM proveedor ORDER BY nombre LIMIT 5";
$r = mysql_query($sql);

echo '<ul>'."\n";
while( $l = mysql_fetch_array( $r ) )
{
echo "\t".'<li id="autocomplete_'.$l['id'].'" rel="'.$l['id'].'_' . $l['nombre'] . '">'. utf8_encode($l['nombre']) .'</li>'."\n";
}
echo '</ul>';

?>