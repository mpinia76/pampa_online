<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$dbhost = "localhost";
$dbname = "produccion";
$dbuser = "produccion";
$dbpassword = "PKVJ6HQVwE96yBNj";

//CONEXION A LA BASE DE DATOS

//$conn = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
mysql_connect($dbhost, $dbuser, $dbpassword);
mysql_select_db($dbname);


$hoy = date("Y-m-d");
$ayer = date('Y-m-d', strtotime('-1 day'));
//$fecha = '2018-05-06';
$condicion = (isset($_GET['id']))?" reservas.id='".$_GET['id']."' ":" (reservas.check_out='".$hoy."' OR reservas.check_out='".$ayer."')";
$sql = "SELECT reservas.id, clientes.nombre_apellido, clientes.email FROM reservas INNER JOIN clientes ON reservas.cliente_id = clientes.id 
WHERE ".$condicion." AND ((reservas.estado != 2 AND reservas.estado != 3) OR reservas.estado is null)";
//echo $sql."<br>";
$rsTemp1 = mysqli_query($conn,$sql);
$id=0;
while ($rs = mysqli_fetch_array($rsTemp1)){

	//echo $newPassword."<br>";
	$sql = "SELECT id, enviada,respondida FROM encuesta WHERE reserva_id = ".$rs['id'];
	//echo $sql."<br>";
	$rsTemp = mysqli_query($conn,$sql);
	if(mysqli_num_rows($rsTemp)==0){
		$sql = "INSERT INTO encuesta (reserva_id, enviada) VALUES ('".$rs['id']."', 1)";
		//echo $sql."<br>";
		mysqli_query($conn,$sql);
		$id=mysql_insert_id();
	}
	else{
		$rsEncuesta = mysqli_fetch_array($rsTemp);
		if ($rsEncuesta['respondida']==0) {
			$enviada = $rsEncuesta['enviada'] + 1;
			$id = $rsEncuesta['id'];
			$sql = "UPDATE encuesta SET enviada = ".$enviada." WHERE id = ".$id;
			//echo $sql."<br>";
			mysqli_query($conn,$sql);
		}
	}
	if ($id) {


		$textMessage = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> New Document </TITLE>
<META NAME="Generator" CONTENT="EditPlus">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
</HEAD>

<BODY><div class=""><div class="aHl"></div><div id=":od" tabindex="-1"></div><div id=":o2" class="ii gt">
		<div id=":o1" class="a3s aXjCH "><div dir="ltr"><div class="gmail_default" style="font-family:verdana,sans-serif;color:#666666">
		<div dir="ltr"><div class="gmail_default" style="font-family:verdana,sans-serif;color:rgb(102,102,102)"><div dir="ltr">
		<div class="gmail_default" style="font-family:verdana,sans-serif;color:rgb(102,102,102)">
		<div dir="ltr"><div class="gmail_default" style="font-family:verdana,sans-serif">
		
		<div class="gmail_default" style="text-align:left;font-family:verdana,sans-serif">
		<i style="font-size:x-small">
		<img src="http://www.pampaonline.com/images/Header.jpg" style="margin-right:0px" data-image-whitelisted="" class="CToWUd" width="59px" hight="85px"></i>
		<br></div>
		<br>
		
		<span class="m_5146862768849835395m_-4273096359447565817gmail-m_2461132594155676157gmail-m_-1279388942409998069gmail-im" style="font-family:tahoma,sans-serif">
		<font color="#444444"><p class="MsoNormal" style="margin-bottom:0.0001pt;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif">Estimado/a '.$rs['nombre_apellido'];
		$textMessage .= '</span></font></p>
		<br>
		<p class="MsoNormal" style="margin-bottom:0.0001pt;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif"><font color="#444444">Deseamos que hayan tenido un buen regreso a casa.</font></span></p>
		<br>
		
		<p class="MsoNormal" style="margin-bottom:0.0001pt;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif"><font color="#444444">Nos comunicamos para agradecerles el habernos elegido y confiado en nosotros para su estad&iacute;a en Mar de las Pampas. Esperamos hayan disfrutado de nuestra propuesta, tanto como nosotros de su presencia.</font></span></p>
		<br>
		
		<p class="MsoNormal" style="margin-bottom:0.0001pt;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif"><font color="#444444">Aprovechamos la oportunidad para invitarlos a participar de nuestra encuesta de satisfacci&oacute;n. Haciendo click <a href="http://66.113.163.185/villagedelaspampas/encuesta/'.$id.'" target="_blank">aqu&iacute;</a>. No le llevar&aacute; m&aacute;s de 2 minutos y para nosotros es de gran ayuda e importancia contar con su opini&oacute;n.</font></span></p>
		<br>
		
		<p class="MsoNormal" style="margin-bottom:0.0001pt;font-family:tahoma,sans-serif;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif"><font color="#444444"><font color="#444444">Sin m&aacute;s, todo el equipo de Village de las Pampas Apart Hotel Boutique le deja un afectuoso saludo deseando contarlo nuevamente entre sus hu&eacute;spedes.</font></span></p>
		<br>
		
		<p class="MsoNormal" style="margin-bottom:0.0001pt;font-family:tahoma,sans-serif;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif"><font color="#444444">Un saludo Cordial,</font></span></p>
		<br>
		<p class="MsoNormal" style="margin-bottom:0.0001pt;font-family:tahoma,sans-serif;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif"><font color="#444444">Oficina de Reservas</font></span></p>
		<p class="MsoNormal" style="margin-bottom:0.0001pt;font-family:tahoma,sans-serif;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif"><font color="#444444">Village de las Pampas Apart Hotel Boutique</font></span><br>
			<b style="font-size:x-small"><a href="mailto:info@villagedelaspampas.com.ar" target="_blank">info@villagedelaspampas.com.ar</a>&nbsp;</b><br>
		<b style="font-size:x-small"><a href="http://www.villagedelaspampas.com.ar/" target="_blank">ww<wbr>w.villagedelaspampas.com.ar</a></b></p>
		<p class="MsoNormal" align="center" style="text-align:center;font-size:13px"><span style="color:rgb(31,73,125);font-family:Calibri,sans-serif;font-size:11pt">&nbsp;</span><br></p><p class="MsoNormal" style="margin-bottom:12pt;font-size:13px"><u></u><img width="87" height="87" src="http://www.pampaonline.com/images/facebook.jpg" align="left" hspace="12" alt="images.jpg" data-image-whitelisted="" class="CToWUd"><u></u><strong><span lang="ES" style="color:gray;font-weight:normal"><u></u><u></u></span></strong></p><p style="font-size:13px"><strong><span style="font-size:11pt;font-family:Calibri,sans-serif;color:gray;font-weight:normal">Los esperamos en&nbsp;</span></strong><strong><span style="font-size:11pt;font-family:Calibri,sans-serif;color:gray">Facebook&nbsp;</span></strong><strong><span style="font-size:11pt;font-family:Calibri,sans-serif;color:gray;font-weight:normal">con nuestras &uacute;ltimas novedade</span></strong><strong><span lang="ES" style="font-size:11pt;font-family:Calibri,sans-serif;color:gray;font-weight:normal">s...Hac&eacute; click</span></strong><strong><span lang="ES" style="font-size:11pt;font-family:Calibri,sans-serif;color:rgb(31,73,125);font-weight:normal">&nbsp;</span></strong><strong><span style="font-size:11pt;font-family:Calibri,sans-serif;color:rgb(31,73,125);font-weight:normal"><a href="http://goo.gl/4rrDa" target="_blank" >ac&aacute;</a></span></strong></p>
		</div></div></div>
<br></div></div>
<br></div></div>
<br></div></div>
<br></div></div>
<br><p></p><p class="MsoNormal" style="color:rgb(68,68,68);margin-bottom:0.0001pt;font-family:tahoma,sans-serif;font-size:12.8px;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
<span style="font-size:8.5pt;font-family:verdana,sans-serif"><br></span></p></div></div><div class="yj6qo"></div><div class="adL">
<br></div></div></div><div class="adL">
<br></div></div></div><div class="adL">
<br></div></div></div><div class="adL">
</div></div></div><div id=":og" class="ii gt" style="display:none"><div id=":oh" class="a3s aXjCH undefined"></div></div>
<div class="hi"></div></div></BODY>
</HTML>';

		/*$mailheaders = "MIME-Version: 1.0 \r\n";
	    $mailheaders .= "Content-type: text/html; charset=utf8 \r\n";
		$mailheaders .= "From: ".utf8_encode("Satisfacción al huésped")." <info@villagedelaspampas.com.ar> \r\n";
	    $mailheaders .= "Return-path: ".utf8_encode("Satisfacción al huésped")." <info@villagedelaspampas.com.ar> \r\n";
	    $mailheaders .= "Cc: pacec@villagedelaspampas.com.ar \r\n";
	    $mailheaders .= "X-Priority: 1 \r\n";
	    $mailheaders .= "X-MSMail-Priority: High \r\n";
	    $mailheaders .= "X-Mailer: PHP/".phpversion()." \n";










	    $success = mail($rs['email'], "Encuesta Village de las Pampas", $textMessage, $mailheaders, "-finfo@villagedelaspampas.com.ar");*/

		//echo $success;

		$errorMessage = '';

		//print_r(error_get_last());


		require("/home/produccion/library/mailer/class.phpmailer.php");

		$mail = new PHPMailer();
		//$mail->SMTPDebug = 4;

		// Configuración de Sendinblue SMTP
		$mail->isSMTP();
		$mail->Host = 'smtp-relay.brevo.com';
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;
		$mail->SMTPAuth = true;
		$mail->Username = 'marcospinero1976@gmail.com'; // Usar tu clave de API como nombre de usuario
		$mail->Password = 'KUgjx4rGQwYh5psW'; // Deja la contraseña en blanco

		$mail->setFrom('info@villagedelaspampas.com.ar', 'Satisfaccion al huesped');

		$mail->addAddress($rs['email']);

		$mail->Subject = 'Encuesta Village de las Pampas';
		$mail->isHTML(true);
		$mail->Body = $textMessage;

		// Envío del correo
		if (!$mail->send()) {
			//echo 'Error al enviar el mensaje: ' . $mail->ErrorInfo;
		} else {
			//echo '¡Mensaje enviado!';
		}

		$nombreFile = 'envio_encuesta_'.date('Ymd') . '_log';
		$dt = date('Y-m-d G:i:s');

		$_Log = fopen("./logs/" . $nombreFile . ".log", "a+");

		fputs($_Log, $dt . " --> Asunto: Envío encuesta a: " . $rs['email'] ."\n");

		//fputs($_Log, $dt . " --> Error mail: " . $errorMessage ."\n");

		fclose($_Log);


	}
}


?>