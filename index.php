<?php

session_start();
include_once("model/form.class.php");
include_once("config/db.php");
include_once("functions/abm.php");
include_once("functions/util.php");
// Al final de la página, actualiza la información en la base de datos
$date = date('Y-m-d');

if(isset($_GET['exit']) and $_GET['exit']=="on"){
	$sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','logout','".getRealIP()."')";
	mysql_query($sql);
    $date = date('Y-m-d');
    $sqlAuditoria ="SELECT * FROM usuario_auditoria WHERE usuario_id = ".$_SESSION['userid']." AND fecha='".$date."'";
    $rsTempAuditoria = mysql_query($sqlAuditoria);
    $totalAuditoria = mysql_num_rows($rsTempAuditoria);
    //_log($sqlAuditoria);
    if($totalAuditoria == 1) {
        $rsAuditoria = mysql_fetch_array($rsTempAuditoria);
        $last_interaction = strtotime($rsAuditoria['last']);
        _log(date('Y-m-d H:i:s'));
        // Calcula los segundos entre la última interacción y el tiempo actual
        $elapsed_time_seconds = time() - $last_interaction;
        //$elapsed_time_minutes = round($elapsed_time_seconds / 60);

            // Actualiza la hora de última interacción y segundos conectados
            $sql_update = "UPDATE usuario_auditoria SET last = now(), interaccion='logout', segundos = segundos + $elapsed_time_seconds WHERE usuario_id = " . $_SESSION['userid'] . " AND fecha = '$date'";
        //_log($sql_update);
        mysql_query($sql_update);

    }
    session_destroy();
    setcookie("userid","",time()-3600);
}else if($_SESSION['userid'] != ''){
    header('Location: desktop.php');
}


if(isset($_POST['ingresar'])){
	$user = $_POST['email'];
	$pass = $_POST['password'];
	
	$sql = "SELECT * FROM usuario WHERE email = '$user' AND password='$pass'";
	$rsTemp = mysql_query($sql);
	$total = mysql_num_rows($rsTemp);
	
	if($total == 1){
		$rs = mysql_fetch_array($rsTemp);
		$_SESSION['userid'] = $rs['id'];
		$_SESSION['usernombre'] = $rs['nombre']." ".$rs['apellido'];
        $_SESSION['login_time'] = time();
        // Actualiza el tiempo de la última interacción del usuario
        $_SESSION['last_interaction'] = time();
        setcookie('userid',$rs['id'],time()+60*60*24,'/');
        
        $sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','login','".getRealIP()."')";
	mysql_query($sql);




        $sqlAuditoria ="SELECT * FROM usuario_auditoria WHERE usuario_id = ".$rs['id']." AND fecha='".$date."'";
        $rsTempAuditoria = mysql_query($sqlAuditoria);
        $totalAuditoria = mysql_num_rows($rsTempAuditoria);
        //_log($sqlAuditoria);
        if($totalAuditoria == 1) {
            $rsAuditoria = mysql_fetch_array($rsTempAuditoria);
            $last_interaction = strtotime($rsAuditoria['last']);

            // Calcula los segundos entre la última interacción y el tiempo actual
            $elapsed_time_seconds = time() - $last_interaction;
            $elapsed_time_minutes = round($elapsed_time_seconds / 60);
            // Verifica si la sesión se ha perdido por inactividad
            $inactividad_limite = 24; // Establece el límite de inactividad en segundos (ajusta según tus necesidades)
            $elapsed_time_minutes=($elapsed_time_minutes > $inactividad_limite)?$inactividad_limite:$elapsed_time_minutes;
            _log(date('Y-m-d H:i:s'));
                // Actualiza la hora de última interacción y segundos conectados
                $sql_update = "UPDATE usuario_auditoria SET last = now(), interaccion='logout', segundos = segundos + $elapsed_time_minutes WHERE usuario_id = " . $rs['id'] . " AND fecha = '$date'";
            //_log($sql_update);
                mysql_query($sql_update);

        }
        else{
            $sqlInsertAuditoria = "INSERT INTO usuario_auditoria (usuario_id,fecha,logueo,last,segundos,interaccion,ip)
			VALUES ('".$_SESSION['userid']."','".date('Y-m-d')."','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."',0,'login','".getRealIP()."')";
            //_log($sqlInsertAuditoria);
            mysql_query($sqlInsertAuditoria);
        }


		if($rs['admin'] == 1){ $_SESSION['admin'] = true; }
		header("Location: desktop.php#");
	}else{
		$result = 2;
	}
}
$campos['email'] 	= array('text','E-mail',1);
$campos['password'] = array('text','Password',0,'','','password');

$form = new Form();
$form->setLegend('Ingresar al sistema'); //nombre del form
$form->setAction('index.php'); //a donde hacer el post
$form->setBotonValue('Ingresar'); //leyenda del boton
$form->setBotonName('ingresar');
$form->setCampos($campos);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pampa Online!</title>
<link href="styles/form.css" rel="stylesheet" type="text/css" />
<?=$form->printJS()?>
<style>
body{
margin:0;
}
</style>
</head>

<body>
<div id="wrapper" style="width:250px; margin-left:auto; margin-right:auto; margin-top:150px;">
<img src="images/logo.png" /><br /><br />
<? if(isset($result) and $result == 2){ ?>
	<div id="mensaje" class="ok"><p><img src="images/error.gif" align="absmiddle" /> Datos incorrectos</p></div>
<? } ?>
<?=$form->printHTML()?>
</div>

</body>
</html>
