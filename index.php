<?php

session_start();
include_once("model/form.class.php");
include_once("config/db.php");
include_once("functions/abm.php");
include_once("functions/util.php");
// Al final de la página, actualiza la información en la base de datos
$date = date('Y-m-d');
_log('Usuario: '.$_SESSION['userid']);
if(isset($_GET['exit']) and $_GET['exit']=="on"){
    auditarUsuarios('logout');
    session_destroy();
    setcookie("userid","",time()-3600);
}else if($_SESSION['userid'] != ''){
    auditarUsuarios('login');
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

        auditarUsuarios('login');


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
