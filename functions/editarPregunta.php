<?php
$id 	= $_POST['id'];
$valor	= $_POST['valor'];

include_once("../config/db.php");

$sql = "UPDATE encuesta_preguntas SET activa = ".$valor." WHERE id = ".$id;
	            mysqli_query($conn,$sql);



?>