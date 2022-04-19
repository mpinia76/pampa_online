<?php



include("preguntas.inc.php");




echo '<?xml version="1.0" encoding="UTF-8"?>';



?>

<?php


$dbhost = "163.10.35.37";
$dbname = "pampa";
$dbuser = "root";
$dbpassword = "secyt";



$conn=mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);


//funciones utiles

function fechasql($fecha){

	$part=explode("/",$fecha);

	$mysql=$part[2]."-".$part[1]."-".$part[0];

	return $mysql;

}

function fechavista($fecha){
	$part=explode("-",$fecha);
	$mysql=$part[2]."/".$part[1]."/".$part[0];
	return $mysql;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Page-Enter" content="revealTrans(Duration=1, Transition=5)" >

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Village de las Pampas - Encuesta de satisfacci&oacute;n</title>

<?php //$xajax->printJavascript(); ?>







<style type="text/css">
#container {
    height: 200px;
    line-height: 200px;
    text-align:center;
}

#container img {
    vertical-align: middle;

}

#container img:hover {
  filter: blur(6px);
}

.titulo_formulario {
	font-family: Arial, Helvetica, sans-serif;
	font-size: x-large;
	color: #AAA;
	}
.titulo_principal {
	font-family: Arial, Helvetica, sans-serif;
	border: 1px solid #CCC;
	font-size:0.8em;
	font-weight:bold;
	color:#30896C;
	background-color:#CAECE1;
	line-height: 1.8em;
	}
.titulo_secundario {
	font-family: Arial, Helvetica, sans-serif;
	font-size:small;
	}
.comentario {
	font-family: Arial, Helvetica, sans-serif;
	font-size:small;
	font-style:italic;
	color:#666666;
	}
table {
	border: 1px solid #CCCCCC;
	}
.itemdetabla-1{
	font-family: Arial, Helvetica, sans-serif;
	font-size:small;
	line-height: 1.8em;
	padding-left: .5em;
	background-color:#FFF;
	color:#333333;
	font-weight:normal;
	}
.itemdetabla-2{
	font-family: Arial, Helvetica, sans-serif;
	font-size:small;
	line-height: 1.8em;
	padding-left: .5em;
	background-color:#EEE;
	color:#333333;
	font-weight:normal;
	}
.controldetabla-1{
	line-height: 1.8em;
	background-color:#FFF;
	}
.controldetabla-2{
	line-height: 1.8em;
	background-color:#EEE;
	}
#contenedor{
	width:70%;
	margin-left:auto;
	margin-right:auto;
	padding:15px;
	border: 2px solid #CCCCCC;
	background-color:#FFF;
	overflow-y:scroll;
	overflow-x:hidden;
	height: 50%;
	/*position:relative;*/
	position:absolute;
	top:25%;
	right:15%;
	z-index:10;
	}
.mensaje {
	position:absolute;
	top:100px;
	left:90px;
	padding:15px;
	width:320px;
	background-color:#009091;
	border:solid 4px #FFFFFF;
	color:#FFFFFF;
	z-index:10;
	font:normal 12px arial;
	text-align:center;
}
.mensaje .cerrar {
	font:bold 13px arial;
	color: #FFFF33;
	cursor:pointer;
	text-decoration:none;
}
</style>

<!--JQuery Date Picker-->

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>



<!--[if IE]><script type="text/javascript" src="../js/jquery.bgiframe.js"></script><![endif]-->





<script language="javascript" type="text/javascript">



function valida(F) {







}

</script>

<style type="text/css">



a.dp-choose-date {

	float: left;

	width: 16px;

	height: 16px;

	padding: 0;

	margin: 5px 3px 0;

	display: block;

	text-indent: -2000px;

	overflow: hidden;

	background: url(../js/calendar.png) no-repeat;

}

a.dp-choose-date.dp-disabled {

	background-position: 0 -20px;

	cursor: default;

}

input.dp-applied {

	width: 70px;

	float: left;

}





.page-heading {

              background-color: #2a9ed9;

			  background-image: url(https://www.discoverushuaia.com.ar/tienda/wp-content/uploads/2016/09/bg-home-v3-b-1.jpg);

			  background-size: cover;

			  background-repeat: no-repeat;

			  color:#fff;

			  }

</style>

<!--/JQuery Date Picker-->

</head>



<body>





<?php if(isset($_POST['enviar'])){

	$sql="SELECT id FROM encuesta WHERE id='".$_POST['id']."'";


	$rsTemp = mysql_query($sql);

	if(mysql_affected_rows()==1){


		//cargo el cliente en la bd

		/*$sql="SELECT id FROM clientes WHERE email='".$_POST['email']."'";

		$rsTemp = mysql_query($sql);

		if(mysql_affected_rows()==1){

			$rs = mysql_fetch_array($rsTemp);

			$cliente_id = $rs['id'];

			$sql="UPDATE clientes SET

					nombre = '".$_POST['nombre']."',

					apellido = '".$_POST['apellido']."',

					email = '".$_POST['email']."',

					telefono = '".$_POST['cod_area']." ".$_POST['telefono']."',

					tipodoc = '".$_POST['tipodoc']."',

					nrodoc = '".$_POST['nrodoc']."',

					pais = '".$_POST['pais']."',

					provincia = '".$_POST['provincia']."',

					ciudad = '".$_POST['ciudad']."',

					calle = '".$_POST['calle']."',

					nropuerta = '".$_POST['nropuerta']."'

				WHERE email = '".$_POST['email']."'";

			mysql_query($sql);







		}*/
		//$persona = $_POST['nombre_apellido']." ha completado una encuesta de satisfaccion.  \r\n \r\n";


		//guardo la encuesta

		$sql="UPDATE encuesta SET
					respondida = '1',
					comentarios = '".$_POST['comentarios']."'



				WHERE id = '".$_POST['id']."'";

			mysql_query($sql);


		//echo $sql;

		//$estadia = "Los datos ingresados son para su estadia desde el".$_POST['check_in']." hasta el ".$_POST['check_out']." en el apartamento ".$_POST['apartamento']."\r\n\r\n";







		$encuesta_id=$_POST['id'];



		$letras="abcdefghijklmnopqrst";
		/*$contestada=0;
		$total=0;*/
		for($i=1; $i<=5; $i++){

			if($i==3){

				for($j=0; $j<strlen($letras); $j++){

					if(isset($_POST[$i.$letras[$j]])){
						$sql = "DELETE  FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$encuesta_id."' AND `pregunta_id` = '".$i.$letras[$j]."'";


						//echo $sql."<br>";

						mysql_query($sql);
						/*$contestada++;
						$total += $_POST[$i.$letras[$j]];*/
						$sql = "INSERT INTO `encuesta_respuestas`

							(`encuesta_id`,`pregunta_id`,`valor`,`extra`)

							VALUES

							('".$encuesta_id."', '".$i.$letras[$j]."', '".$_POST[$i.$letras[$j]]."','')";

						//$resultado.=$pregunta[$i.$letras[$j]].": ".$_POST[$i.$letras[$j]]." \r\n";



						mysql_query($sql);

					}

				}

			}else{

				if(isset($_POST[$i])){
					$sql = "DELETE  FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$encuesta_id."' AND `pregunta_id` = '".$i."'";


						//echo $sql."<br>";

						mysql_query($sql);

					$sql = "INSERT INTO `encuesta_respuestas`

							(`encuesta_id`,`pregunta_id`,`valor`,`extra`)

							VALUES

							('".$encuesta_id."', '".$i."', '".$_POST[$i]."','".$_POST[$i.$_POST[$i]."_extra"]."')";

					//$resultado.=$pregunta[$i].": ".$respuesta[$i][$_POST[$i]]." ".$_POST[$i.$_POST[$i]."_extra"]."  \r\n";



					mysql_query($sql);

				}

			}

		}



		/*$mail.=$persona;

		$mail.=$estadia;

		$mail.="Los resultados de la encuesta son: \r\n";

		$mail.=$resultado;

		$mail.="Comentarios: ".$_POST['comentarios']." \r\n";
                  $mail.="Destacado: ".$_POST['destacado'];











		$to="satisfaccion@villagedelaspampas.com.ar,minervinim@villagedelaspampas.com.ar,pacec@villagedelaspampas.com.ar,gallinalj@villagedelaspampas.com.ar";
		mail($to,"Nueva encuesta de satisfaccion",$mail,"From: Satisfaccion al huesped  <satisfaccion@villagedelaspampas.com.ar>");

		*/



		//echo 'error: '.$exito;




	}



	?>

	<div id="gracias">

	<p align="center" class="titulo_principal"><?php echo "Encuesta modificada con &Eacute;xito"?></p>

	</div>
	<?php //echo $googleTripadvisor?>


<?php }else{
	$sql = "SELECT encuesta.id AS idEncuesta, reservas.id, clientes.nombre_apellido, clientes.dni, clientes.email, reservas.check_in, reservas.check_out,
	apartamentos.apartamento, apartamentos.id as id_apartamento, encuesta.comentarios
	FROM encuesta INNER JOIN reservas ON encuesta.reserva_id = reservas.id
	INNER JOIN clientes ON reservas.cliente_id = clientes.id
	INNER JOIN apartamentos ON reservas.apartamento_id = apartamentos.id
	WHERE encuesta.reserva_id = ".$_GET['id'];
	//echo $sql;
	$rsTemp1 = mysql_query($sql);
	//print_r($rsTemp1);
	if ($rs = mysql_fetch_array($rsTemp1)){
			//echo $_POST['username']." == ".$rs['email'] ." and ". md5($_POST['password'])." == ".$rs['password'];
			//if ($_POST['username']==$rs['email'] and md5($_POST['password'])==$rs['password']){


				?>

	<div id="formulario">

	<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" name="form" onSubmit="return valida(this);">

	<input type="hidden" id="id" name="id" value="<?php echo $rs['idEncuesta']?>"/>
	<input type="hidden" id="check_in" name="check_in" value="<?php echo fechavista($rs['check_in']);?>"/>
	<input type="hidden" id="check_out" name="check_out" value="<?php echo fechavista($rs['check_out']);?>"/>
	<input type="hidden" id="apartamento" name="apartamento" value="<?php echo trim($rs['apartamento']);?>"/>
	<input type="hidden" id="email" name="email" value="<?php echo trim($rs['email']);?>"/>
	<input type="hidden" id="nombre_apellido" name="nombre_apellido" value="<?php echo trim($rs['nombre_apellido']);?>"/>

	<div id="datos_personales">



		<p class="titulo_secundario">

		<div class="titulo_secundario" style="float:left; margin-top:3px;">Estadia desde: &nbsp;<strong><?php echo fechavista($rs['check_in'])?></strong></div>

		<div class="titulo_secundario" style="float:left; margin-top:3px;"> &nbsp; hasta: &nbsp; <strong><?php echo fechavista($rs['check_out'])?></strong></div>

		<div class="titulo_secundario" style="float:left; margin-top:3px;"> &nbsp; en el apartamento/habitaci&oacute;n: <strong><?php echo trim($rs['apartamento']);?></strong></div>




		</p>
		<br>
		<p class="titulo_secundario">

		Nombre y Apellido: <strong><?php echo trim($rs['nombre_apellido']);?></strong>


		</p>
	</div>


	<div id="encuesta">
	 <p class="titulo_secundario"><strong>1. &iquest;C&oacute;mo conoci&oacute; Village de las Pampas?</strong><br>
		<br>
		<?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id=1";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		<input name="1" type="radio" value="a" <?php echo ($rsRespuestas['valor']=='a')?' checked="checked"':''?> /> Buscadores (Google, yahoo, otros)<br />
		<input name="1" type="radio" value="b" <?php echo ($rsRespuestas['valor']=='b')?' checked="checked"':''?>/> Portales de reservas (despegar.com, booking.com, expedia, etc.)<br />
		<input name="1" type="radio" value="c" <?php echo ($rsRespuestas['valor']=='c')?' checked="checked"':''?>/> Trip advisor<br />
		<input name="1" type="radio" value="d" <?php echo ($rsRespuestas['valor']=='d')?' checked="checked"':''?>/> Recomendaci&oacute;n<br />
		<input name="1" type="radio" value="e" <?php echo ($rsRespuestas['valor']=='e')?' checked="checked"':''?>/> Estad&iacute;as Anteriores<br />

	  </p>
	  <p class="titulo_secundario"><strong>2. &iquest;Por qu&eacute; eligi&oacute; Village de las Pampas para su estad&iacute;a?</strong><br>
		<br>
		<?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id=2";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		<input name="2" type="radio" value="a" <?php echo ($rsRespuestas['valor']=='a')?' checked="checked"':''?>/>Cercan&iacute;a al mar/Ubicaci&oacute;n<br />
		<input name="2" type="radio" value="b" <?php echo ($rsRespuestas['valor']=='b')?' checked="checked"':''?>/>Recomendaci&oacute;n<br />
		<input name="2" type="radio" value="c" <?php echo ($rsRespuestas['valor']=='c')?' checked="checked"':''?>/>Reputaci&oacute;n en redes sociales e internet<br />
		<input name="2" type="radio" value="d" <?php echo ($rsRespuestas['valor']=='d')?' checked="checked"':''?>/>Otras Razones, Cu&aacute;les? <input type="text" name="2d_extra" size="50" value="<?php echo $rsRespuestas['extra'];?>" />
	  </p>
	  <p class="titulo_secundario"><strong>3. Califica como (Excelente, Muy Bueno, Bueno, Razonable, Pobre)</strong></p>
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titulo_principal">
		<tr class="encabezado">
		  <th scope="col">Ventas</th>
		  <th width="40" scope="col">E</th>
		  <th width="40" scope="col">MB</th>
		  <th width="40" scope="col">B</th>
		  <th width="40" scope="col">R</th>
		  <th width="40" scope="col">P</th>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Amabilidad </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3a'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-1"><input name="3a" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3a" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3a" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3a" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3a" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Velocidad de respuesta a llamados y correos electr&oacute;nicos </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3b'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-2"><input name="3b" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3b" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3b" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3b" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3b" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Detalle en la informaci&oacute;n que usted necesitaba </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3c'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-1"><input name="3c" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3c" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3c" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3c" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3c" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>

	  </table><br />

	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titulo_principal">
		<tr>
		  <th class="encabezado" scope="col">Recepci&oacute;n</th>
		  <th width="40" class="encabezado" scope="col">E</th>
		  <th width="40" class="encabezado" scope="col">MB</th>
		  <th width="40" class="encabezado" scope="col">B</th>
		  <th width="40" class="encabezado" scope="col">R</th>
		  <th width="40" class="encabezado" scope="col">P</th>
		</tr>
		<tr>
          <td class="itemdetabla-1">Atenci&oacute;n en el Check In</td>
          <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3d'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-1"><input name="3d" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3d" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3d" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3d" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3d" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Amabilidad del personal</td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3e'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-2"><input name="3e" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3e" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3e" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3e" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3e" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Resoluci&oacute;n de conflictos</td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3f'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-1"><input name="3f" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3f" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3f" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3f" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3f" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
	  </table>
	  <br />
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titulo_principal">
		<tr>
		  <th class="encabezado" scope="col">Desayuno</th>
		  <th width="40" class="encabezado" scope="col">E</th>
		  <th width="40" class="encabezado" scope="col">MB</th>
		  <th width="40" class="encabezado" scope="col">B</th>
		  <th width="40" class="encabezado" scope="col">R</th>
		  <th width="40" class="encabezado" scope="col">P</th>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Ambientaci&oacute;n/M&uacute;sica </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3g'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-1"><input name="3g" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3g" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3g" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3g" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3g" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Variedad </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3h'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-2"><input name="3h" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3h" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3h" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3h" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3h" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Calidad </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3i'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-1"><input name="3i" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3i" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3i" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3i" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3i" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Amabilidad y profesionalismo del personal </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3j'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-2"><input name="3j" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3j" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3j" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3j" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3j" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
	  </table>
	  <br />
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titulo_principal">
		<tr>
		  <th class="encabezado" scope="col">Servicio de Mucamas y Limpieza</th>
		  <th width="40" class="encabezado" scope="col">E</th>
		  <th width="40" class="encabezado" scope="col">MB</th>
		  <th width="40" class="encabezado" scope="col">B</th>
		  <th width="40" class="encabezado" scope="col">R</th>
		  <th width="40" class="encabezado" scope="col">P</th>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Limpieza del apartamento </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3k'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-1"><input name="3k" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3k" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3k" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3k" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3k" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Limpieza de &aacute;reas comunes </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3l'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-2"><input name="3l" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3l" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3l" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3l" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3l" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Predisposici&oacute;n del personal </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3m'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-1"><input name="3m" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3m" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3m" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3m" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3m" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>

	  </table>
	  <br />
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titulo_principal">
		<tr>
		  <th class="encabezado" scope="col">Mantenimiento</th>
		  <th width="40" class="encabezado" scope="col">E</th>
		  <th width="40" class="encabezado" scope="col">MB</th>
		  <th width="40" class="encabezado" scope="col">B</th>
		  <th width="40" class="encabezado" scope="col">R</th>
		  <th width="40" class="encabezado" scope="col">P</th>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Resoluci&oacute;n de problemas </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3n'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-1"><input name="3n" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3n" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3n" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3n" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3n" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Estado general del apartamento </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3o'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-2"><input name="3o" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3o" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3o" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3o" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3o" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>

		<tr>
		  <td class="itemdetabla-2">Estado General de &Aacute;reas comunes (Parque, Internas y externas) </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3t'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-2"><input name="3t" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3t" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3t" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3t" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3t" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
	  </table>
	  <br />
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titulo_principal">
		<tr>
		  <th class="encabezado" scope="col">Sala de Relax</th>
		  <th width="40" class="encabezado" scope="col">E</th>
		  <th width="40" class="encabezado" scope="col">MB</th>
		  <th width="40" class="encabezado" scope="col">B</th>
		  <th width="40" class="encabezado" scope="col">R</th>
		  <th width="40" class="encabezado" scope="col">P</th>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Variedad y calidad de los tratamientos </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3p'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-1"><input name="3p" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3p" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3p" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3p" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3p" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Atenci&oacute;n del personal </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3q'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-2"><input name="3q" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3q" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3q" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3q" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3q" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>


	  </table>
	  <br />
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titulo_principal">
		<tr>
		  <th class="encabezado" scope="col">Gesti&oacute;n de sustentabilidad</th>
		  <th width="40" class="encabezado" scope="col">E</th>
		  <th width="40" class="encabezado" scope="col">MB</th>
		  <th width="40" class="encabezado" scope="col">B</th>
		  <th width="40" class="encabezado" scope="col">R</th>
		  <th width="40" class="encabezado" scope="col">P</th>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Respeto por el medio ambiente percibido </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3r'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-1"><input name="3r" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3r" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3r" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3r" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-1"><input name="3r" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Comunicaci&oacute;n de pautas de Gesti&oacute;n </td>
		  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='3r'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		  <td align="center" class="controldetabla-2"><input name="3s" type="radio" value="5" <?php echo ($rsRespuestas['valor']=='5')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3s" type="radio" value="4" <?php echo ($rsRespuestas['valor']=='4')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3s" type="radio" value="3" <?php echo ($rsRespuestas['valor']=='3')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3s" type="radio" value="2" <?php echo ($rsRespuestas['valor']=='2')?' checked="checked"':''?>/></td>
		  <td align="center" class="controldetabla-2"><input name="3s" type="radio" value="1" <?php echo ($rsRespuestas['valor']=='1')?' checked="checked"':''?>/></td>
		</tr>


	  </table>
	  <p class="titulo_secundario"><strong>4. El Village ha: </strong><br />
		<br />
		<?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='4'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
		<input name="4" type="radio" value="a" <?php echo ($rsRespuestas['valor']=='a')?' checked="checked"':''?>/> Superado mis expectativas <br />
		<input name="4" type="radio" value="b" <?php echo ($rsRespuestas['valor']=='b')?' checked="checked"':''?>/> Colmado mis expectativas<br />
		<input name="4" type="radio" value="c" <?php echo ($rsRespuestas['valor']=='c')?' checked="checked"':''?>/> Estado por debajo de mis expectativas  </p>

	<p class="titulo_secundario"><strong>5. Recomendaria Village de las Pampas:</strong><br />
	  <br />
	  <?php
		$sql = "SELECT * FROM `encuesta_respuestas`

							WHERE `encuesta_id` = '".$rs['idEncuesta']."' AND pregunta_id='5'";




					$rsTemp2 =	mysql_query($sql);
					$rsRespuestas = mysql_fetch_array($rsTemp2);


		?>
	  <input name="5" type="radio" value="a" <?php echo ($rsRespuestas['valor']=='a')?' checked="checked"':''?>/> Lo har&iacute;a con gusto</span><br />
	  <input name="5" type="radio" value="b" <?php echo ($rsRespuestas['valor']=='b')?' checked="checked"':''?>/> Solo si mejoran algunos aspectos<br />
	  <input name="5" type="radio" value="c" <?php echo ($rsRespuestas['valor']=='c')?' checked="checked"':''?>/> No lo har&iacute;a</p>
	<p class="titulo_secundario"><strong>6. &iquest;Desea hacer alguna sugerencia o comentario sobre nuestro Apart Hotel?:</strong><br /><br />
	  <textarea name="comentarios" cols="50" rows="5"><?php echo $rs['comentarios'];?></textarea>
	</p>
          <!-- <p class="titulo_secundario"><strong>7. &iquest;Desea destacar a alg&uacute;n miembro del staff y contarnos por qu&eacute;?:</strong><br /><br />
	  <textarea name="destacado" cols="50" rows="5"></textarea>
	</p>  -->

	<p align="center"><input type="submit" class="cat-agregar" value="Enviar" name="enviar"></p>





	</form>

	</div>

	<br/>

	</div>

<?php /*}
		else{?>
			<div id="gracias">

		<p align="center" class="titulo_principal">Los datos ingresados son erroneos.</p>
		<p align="center" class="titulo_principal"><a href="javascript:history.back(-1);" title="Ir la pÃ¡gina anterior">Volver</a></p>
		</div>
		<?php }
	}*/
	}else{

		?>
		<div id="gracias">

		<p align="center" class="titulo_principal">La encuesta ya ha sido completada.</p>

		</div>
	<?php }
}
?>
