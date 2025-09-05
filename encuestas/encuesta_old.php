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

<title>Village de las Pampas - Encuesta de satisfaccion</title>

<?php //$xajax->printJavascript(); ?>







<style type="text/css">
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

	$sql="SELECT id FROM encuesta WHERE id='".$_POST['id']."' AND respondida = 0";

	$rsTemp = mysqli_query($conn,$sql);

	if(mysql_affected_rows()==1){


		//cargo el cliente en la bd

		/*$sql="SELECT id FROM clientes WHERE email='".$_POST['email']."'";

		$rsTemp = mysqli_query($conn,$sql);

		if(mysql_affected_rows()==1){

			$rs = mysqli_fetch_array($rsTemp);

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

			mysqli_query($conn,$sql);







		}*/
		$persona = $_POST['nombre_apellido']." ha completado una encuesta de satisfaccion.  \r\n \r\n";


		//guardo la encuesta

		$sql="UPDATE encuesta SET
					respondida = '1',
					comentarios = '".$_POST['comentarios']."'



				WHERE id = '".$_POST['id']."'";

			mysqli_query($conn,$sql);


		//echo $sql;

		$estadia = "Los datos ingresados son para su estadia desde el".$_POST['checkin']." hasta el ".$_POST['checkout']." en el apartamento ".$_POST['apartamento']."\r\n\r\n";







		$encuesta_id=$_POST['id'];



		$letras="abcdefghijklmnopq";
		$total=0;
		for($i=1; $i<=10; $i++){

			if($i==7){

				for($j=0; $j<strlen($letras); $j++){

					if(isset($_POST[$i.$letras[$j]])){

						$sql = "INSERT INTO `encuesta_respuestas`

							(`encuesta_id`,`pregunta_id`,`valor`,`extra`)

							VALUES

							('".$encuesta_id."', '".$i.$letras[$j]."', '".$_POST[$i.$letras[$j]]."','')";

						$resultado.=$pregunta[$i.$letras[$j]].": ".$_POST[$i.$letras[$j]]." \r\n";



						mysqli_query($conn,$sql);

					}

				}

			}else{

				if(isset($_POST[$i])){

					$total += ($i>1&&$i<10)?$_POST[$i]:0;
					$sql = "INSERT INTO `encuesta_respuestas`

							(`encuesta_id`,`pregunta_id`,`valor`,`extra`)

							VALUES

							('".$encuesta_id."', '".$i."', '".$_POST[$i]."','".$_POST[$i.$_POST[$i]."_extra"]."')";

					$resultado.=$pregunta[$i].": ".$respuesta[$i][$_POST[$i]]." ".$_POST[$i.$_POST[$i]."_extra"]."  \r\n";



					mysqli_query($conn,$sql);

				}

			}

		}



		$mail.=$persona;

		$mail.=$estadia;

		$mail.="Los resultados de la encuesta son: \r\n";

		$mail.=$resultado;

		$mail.="Comentarios: ".$_POST['comentarios']." \r\n";
                  $mail.="Destacado: ".$_POST['destacado'];











		$to="satisfaccion@villagedelaspampas.com.ar,minervinim@villagedelaspampas.com.ar,pacec@villagedelaspampas.com.ar,gallinalj@villagedelaspampas.com.ar";
		mail($to,"Nueva encuesta de satisfaccion",$mail,"From: Satisfacci�n al hu�sped <satisfaccion@villagedelaspampas.com.ar>");





		//echo 'error: '.$exito;




	}


	/*$textoGracias = ($total>=38)?" Nos pone muy felices que hayas tenido una excelente experiencia.<br />
	Te invitamos a comprartirla en <a style=\"color:#0047F1;\">G</a><a style=\"color:#DD172C;\">o</a><a style=\"color:#F9A600; \">o</a><a style=\"color:#0047F1;\">g</a><a style=\"color:#00930E;\">l</a><a style=\"color:#E61B31;\">e</a> . Solo te tomará unos segundos y sería de gran ayuda para nosotros.
	Click <a href=\"https://search.google.com/local/writereview?placeid=ChIJ-w4zbCAjTLwRnvv9mFvBMCs\"  target=\"_blank\">aqu&iacute;</a>.":"Gracias por tomarse el tiempo de completar la encuesta<br />
	Su opini&oacute;n es muy importante para nosotros.";*/
	$textoGracias = "Gracias por tomarse el tiempo de completar la encuesta<br />
	Su opini&oacute;n es muy importante para nosotros.";


	?>

	<div id="gracias">

	<p align="center" class="titulo_principal"><?php echo $textoGracias?></p>

	</div>



<?php }else{
	$sql = "SELECT reservas.id, clientes.nombre_apellido, clientes.dni, clientes.email, reservas.check_in, reservas.check_out,
	apartamentos.apartamento, apartamentos.id as id_apartamento
	FROM encuesta INNER JOIN reservas ON encuesta.reserva_id = reservas.id
	INNER JOIN clientes ON reservas.cliente_id = clientes.id
	INNER JOIN apartamentos ON reservas.apartamento_id = apartamentos.id
	WHERE encuesta.id = ".$_GET['id']." AND respondida = 0";
	//echo $sql;
	$rsTemp1 = mysqli_query($conn,$sql);
	//print_r($rsTemp1);
	if ($rs = mysqli_fetch_array($rsTemp1)){
			//echo $_POST['username']." == ".$rs['email'] ." and ". md5($_POST['password'])." == ".$rs['password'];
			//if ($_POST['username']==$rs['email'] and md5($_POST['password'])==$rs['password']){


				?>

	<div id="formulario">

	<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" name="form" onSubmit="return valida(this);">

	<input type="hidden" id="id" name="id" value="<?php echo $_GET['id']?>"/>
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

	<p class="comentario">A continuacion debe seleccionar la/s opciones que desee</p>
	<div id="encuesta">
	  <p class="titulo_secundario"><strong>1. &iquest;Por que eligio Mar de las Pampas para sus vacaciones?</strong><br>
		<br>
		<input name="1" type="radio" value="a" />Tranquilidad<br />
		<input name="1" type="radio" value="b" />Curiosidad<br />
		<input name="1" type="radio" value="c" />Recomendacion<br />
		<input name="1" type="radio" value="d" />Experiencias anteriores<br />
		<input name="1" type="radio" value="e" />Otras? Cuales? <input type="text" name="1e_extra" size="50" />
	  </p>
	  <p class="titulo_secundario"><strong>2. &iquest;Como conocio Village de las Pampas?</strong><br>
		<br>
		<input name="2" type="radio" value="a" /> Pagina web<br />
		<input name="2" type="radio" value="b" /> Diarios? Cuales? <input type="text" name="2b_extra" size="20" /><br />
		<input name="2" type="radio" value="c" /> Revistas? Cuales? <input type="text" name="2c_extra" size="20" /><br />
		<input name="2" type="radio" value="d" /> Recomendacion<br />
		<input name="2" type="radio" value="e" /> Television<br />
		<input name="2" type="radio" value="f" /> Otros? Cuales?	<input type="text" name="2f_extra" size="20" />
	  </p>
	  <p class="titulo_secundario"><strong>3. Al realizar su consulta inicial (para obtener informacion y/o realizar una reserva) fue atendido: </strong><br />
		<br />
		<input name="3" type="radio" value="a" /> Rapidamente<br />
		<input name="3" type="radio" value="b" /> A desgano y fugazmente <br />
		<input name="3" type="radio" value="c" /> Brindando la informacion requerida <br />
		<input name="3" type="radio" value="d" /> Lentamente <br />
		<input name="3" type="radio" value="e" /> Con cordialidad y eficacia <br />
		<input name="3" type="radio" value="f" /> Con informacion pobre y poco precisa  </p>
	  <p class="titulo_secundario"><strong>4. Califique nuestro sector de Ventas, teniendo en cuenta sus percepciones y experiencia: </strong><br />
		<br />
		<input name="4" type="radio" value="a" /> Excelente<br />
		<input name="4" type="radio" value="b" /> Bueno<br />
		<input name="4" type="radio" value="c" /> Suficiente<br />
		<input name="4" type="radio" value="d" /> Insuficiente  </p>
	  <p class="titulo_secundario"><strong>5. &iquest;Por que eligio Village de las Pampas para su estadia? </strong><br />
		<br />
		<input name="5" type="radio" value="a" /> Cercania al mar, ubicacion<br />
		<input name="5" type="radio" value="b" /> Relacion producto/servicio<br />
		<input name="5" type="radio" value="c" /> Recomendacion<br />
		<input name="5" type="radio" value="d" /> Amplitud de espacio y equipamiento<br />
		<input name="5" type="radio" value="e" /> Otras razones? Cuales? <input type="text" name="5e_extra" size="20" />
	  </p>
	  <p class="titulo_secundario"><strong>6. El Village ha: </strong><br />
		<br />
		<input name="6" type="radio" value="a" /> Superado mis expectativas <br />
		<input name="6" type="radio" value="b" /> Colmado mis expectativas<br />
		<input name="6" type="radio" value="c" /> Estado por debajo de mis expectativas  </p>
	  <p class="titulo_secundario"><strong>7. Califique en: Excelente / Bueno / Razonable / Pobre</strong></p>
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titulo_principal">
		<tr class="encabezado">
		  <th scope="col">Servicios</th>
		  <th width="40" scope="col">E</th>
		  <th width="40" scope="col">B</th>
		  <th width="40" scope="col">R</th>
		  <th width="40" scope="col">P</th>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Camareros / Desayuno </td>
		  <td align="center" class="controldetabla-1"><input name="7a" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-1"><input name="7a" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-1"><input name="7a" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-1"><input name="7a" type="radio" value="P" /></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Camareros / Piscina </td>
		  <td align="center" class="controldetabla-2"><input name="7b" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-2"><input name="7b" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-2"><input name="7b" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-2"><input name="7b" type="radio" value="P" /></td>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Servicios de Masajes y relajaci&oacute;n </td>
		  <td align="center" class="controldetabla-1"><input name="7c" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-1"><input name="7c" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-1"><input name="7c" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-1"><input name="7c" type="radio" value="P" /></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Servicio de Recreaci&oacute;n para Ni&ntilde;os </td>
		  <td align="center" class="controldetabla-2"><input name="7d" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-2"><input name="7d" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-2"><input name="7d" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-2"><input name="7d" type="radio" value="P" /></td>    </tr>
		<tr>
		  <td class="itemdetabla-1">Servicio de Recreaci&oacute;n para Adultos </td>
		  <td align="center" class="controldetabla-1"><input name="7e" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-1"><input name="7e" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-1"><input name="7e" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-1"><input name="7e" type="radio" value="P" /></td>    </tr>
		</tr>
		<tr>
                                     <td class="itemdetabla-2">Horario abarcado por Recreaci&oacute;n</td>
		  <td align="center" class="controldetabla-2"><input name="7f" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-2"><input name="7f" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-2"><input name="7f" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-2"><input name="7f" type="radio" value="P" /></td>    </tr>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Gimnasio</td>
		  <td align="center" class="controldetabla-1"><input name="7g" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-1"><input name="7g" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-1"><input name="7g" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-1"><input name="7g" type="radio" value="P" /></td>    </tr>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Recepcionistas, Cordialidad y eficiencia </td>
		  <td align="center" class="controldetabla-2"><input name="7h" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-2"><input name="7h" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-2"><input name="7h" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-2"><input name="7h" type="radio" value="P" /></td>    </tr>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Resoluci&oacute;n de problemas </td>
		  <td align="center" class="controldetabla-1"><input name="7i" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-1"><input name="7i" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-1"><input name="7i" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-1"><input name="7i" type="radio" value="P" /></td>    </tr>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Servicio de Mucamas </td>
		  <td align="center" class="controldetabla-2"><input name="7j" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-2"><input name="7j" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-2"><input name="7j" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-2"><input name="7j" type="radio" value="P" /></td>    </tr>
		</tr>
	  </table><br />

	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titulo_principal">
		<tr>
		  <th class="encabezado" scope="col">Limpieza y mantenimiento</th>
		  <th width="40" class="encabezado" scope="col">E</th>
		  <th width="40" class="encabezado" scope="col">B</th>
		  <th width="40" class="encabezado" scope="col">R</th>
		  <th width="40" class="encabezado" scope="col">P</th>
		</tr>
		<tr>
                    <td class="itemdetabla-1">&Aacute;reas P&uacute;blicas del edificio</td>
		  <td align="center" class="controldetabla-1"><input name="7k" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-1"><input name="7k" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-1"><input name="7k" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-1"><input name="7k" type="radio" value="P" /></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Apartamento </td>
		  <td align="center" class="controldetabla-2"><input name="7l" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-2"><input name="7l" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-2"><input name="7l" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-2"><input name="7l" type="radio" value="P" /></td>
		</tr>
	  </table>
	  <br />
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="titulo_principal">
		<tr>
		  <th class="encabezado" scope="col">Restaurante</th>
		  <th width="40" class="encabezado" scope="col">E</th>
		  <th width="40" class="encabezado" scope="col">B</th>
		  <th width="40" class="encabezado" scope="col">R</th>
		  <th width="40" class="encabezado" scope="col">P</th>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Ambientaci&oacute;n / Orden </td>
		  <td align="center" class="controldetabla-1"><input name="7m" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-1"><input name="7m" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-1"><input name="7m" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-1"><input name="7m" type="radio" value="P" /></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Servicio</td>
		  <td align="center" class="controldetabla-2"><input name="7n" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-2"><input name="7n" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-2"><input name="7n" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-2"><input name="7n" type="radio" value="P" /></td>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Tama&ntilde;o de las porciones </td>
		  <td align="center" class="controldetabla-1"><input name="7o" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-1"><input name="7o" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-1"><input name="7o" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-1"><input name="7o" type="radio" value="P" /></td>
		</tr>
		<tr>
		  <td class="itemdetabla-2">Calidad de los ingredientes </td>
		  <td align="center" class="controldetabla-2"><input name="7p" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-2"><input name="7p" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-2"><input name="7p" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-2"><input name="7p" type="radio" value="P" /></td>
		</tr>
		<tr>
		  <td class="itemdetabla-1">Presentaci&oacute;n del plato </td>
		  <td align="center" class="controldetabla-1"><input name="7q" type="radio" value="E" /></td>
		  <td align="center" class="controldetabla-1"><input name="7q" type="radio" value="B" /></td>
		  <td align="center" class="controldetabla-1"><input name="7q" type="radio" value="R" /></td>
		  <td align="center" class="controldetabla-1"><input name="7q" type="radio" value="P" /></td>
		</tr>
	  </table>
	<p class="titulo_secundario"><strong>8. Recomendaria Village de las Pampas:</strong><br />
	  <br />
	  <input name="8" type="radio" value="a" /> Lo har&iacute;a con gusto</span><br />
	  <input name="8" type="radio" value="b" /> Solo si mejoran algunos aspectos<br />
	  <input name="8" type="radio" value="c" /> No lo har&iacute;a</p>
	<p class="titulo_secundario"><strong>9. &iquest;Desea hacer alguna sugerencia o comentario sobre nuestro Apart Hotel?:</strong><br /><br />
	  <textarea name="comentarios" cols="50" rows="5"></textarea>
	</p>
          <p class="titulo_secundario"><strong>10. &iquest;Desea destacar a alg&uacute;n miembro del staff y contarnos por qu&eacute;?:</strong><br /><br />
	  <textarea name="destacado" cols="50" rows="5"></textarea>
	</p>

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
