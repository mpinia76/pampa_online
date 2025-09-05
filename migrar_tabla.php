<?php
//DATOS PARA LA BASE DE DATOS MYSQL
ini_set('display_errors', 1);

$dbhostVillaged = "163.10.35.37";
$dbnameVillaged = "villaged_web";
$dbuserVillaged = "root";
$dbpasswordVillaged = "secyt";

$dbhostTest = "163.10.35.37";
$dbnameTest = "pampa";
$dbuserTest = "root";
$dbpasswordTest = "secyt";

/*$dbhostProduccion = "localhost";
$dbnameProduccion = "produccion";
$dbuserProduccion = "produccion";
$dbpasswordProduccion = "PKVJ6HQVwE96yBNj";*/




//CONEXION A LA BASE DE DATOS

$connectionVillaged = mysql_connect($dbhostVillaged, $dbuserVillaged, $dbpasswordVillaged, true);
$connectionTest = mysql_connect($dbhostTest, $dbuserTest, $dbpasswordTest, true);
//$connectionProduccion = mysql_connect($dbhostProduccion, $dbuserProduccion, $dbpasswordProduccion, true);

mysql_select_db($dbnameVillaged, $connectionVillaged);





$sql = "SELECT clientes.email, encuesta.*
	FROM encuesta INNER JOIN clientes ON encuesta.cliente_id = clientes.id";
	echo $sql;
	$rsTemp = mysqli_query($conn,$sql, $connectionVillaged);
	//print_r($rsTemp);
	while ($rs = mysqli_fetch_array($rsTemp)){

		$sql = "SELECT reservas.id
			FROM reservas INNER JOIN clientes ON reservas.cliente_id = clientes.id
			WHERE reservas.check_in = '".$rs['checkin']."' AND reservas.check_out = '".$rs['checkout']."' AND clientes.email = '".$rs['email']."'";
		echo $sql."<br>";
		mysql_select_db($dbnameTest, $connectionTest);
		$rsTemp1 = mysqli_query($conn,$sql, $connectionTest) ;
		//print_r($rsTemp1);
		$reserva_id=0;
		if ($rs1 = mysqli_fetch_array($rsTemp1)){
			$reserva_id = $rs1['id'];
		}

		$sql = "INSERT INTO encuesta (id, reserva_id, comentarios, enviada, respondida) VALUES ('".$rs['id']."','".$reserva_id."','".$rs['comentarios']."',1,1)";
		mysqli_query($conn,$sql, $connectionTest) ;
		$sql = "SELECT *
			FROM encuesta_respuestas WHERE encuesta_id = ".$rs['id'];
			mysql_select_db($dbnameVillaged, $connectionVillaged);
			echo $sql."<br>";
			$rsTemp2 = mysqli_query($conn,$sql,$connectionVillaged);
			//print_r($rsTemp);
			while ($rs2 = mysqli_fetch_array($rsTemp2)){
				$sql = "INSERT INTO encuesta_respuestas (encuesta_id, pregunta_id, valor, extra)
				VALUES ('".$rs2['encuesta_id']."','".$rs2['pregunta_id']."','".$rs2['valor']."','".$rs2['extra']."')";
				echo $sql."<br>";
				mysql_select_db($dbnameTest, $connectionTest);
				mysqli_query($conn,$sql, $connectionTest) ;
			}

	}

?>
