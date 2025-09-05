<?php
session_start();
$user_id = $_SESSION['userid'];
if($user_id == '') { header("Location: index.php"); }

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("config/user.php");
include_once("functions/abm.php");
$ano = ($_GET['ano'])?$_GET['ano']:date('Y');
$mes = ($_GET['mes'])?$_GET['mes']:'0';
if(isset($_POST['agregar'])){
	$sql = "SELECT * FROM empleado_pago WHERE empleado_id = ".$_POST['empleado_id']." AND ano = ".$_POST['ano'];
	$rsTemp = mysqli_query($conn,$sql);
	while($rs = mysqli_fetch_array($rsTemp)){
		$pagado[$rs['ano']."_".$rs['mes']] = true;
	}
	
                  $today = new DateTime();
                  $today->modify('-1 month');
                  $mes_anterior = $today->format('n');
                  $ano_anterior = $today->format('Y'); 
                  
                  if(!$pagado[$_POST['ano']."_".$_POST['mes']] and ((ACCION_102) or (($_POST['ano'] == $ano_anterior and $_POST['mes'] == $mes_anterior) or ($_POST['ano'] ==  date('Y') and $_POST['mes'] == date('m')) ) )){
	
		$sql = "INSERT INTO empleado_hora_extra
					(empleado_id,hora_extra_id,cantidad_solicitada,mes,ano,creado_por,creado)
				VALUES
					(".$_POST['empleado_id'].",".$_POST['sector_id'].",'".$_POST['horas']."',".$_POST['mes'].",".$_POST['ano'].",$user_id,NOW())";
		mysqli_query($conn,$sql); 
		
		if(mysql_error() != ''){
			$result = mysql_error();
		}else{
			$result = 1;
		}
	}else{
                        $result = "No se pueden asignar las horas extras, controle la fecha";
                }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Administrador de empleados</title>
<script type="text/javascript" src="library/jquery/jquery-1.4.2.min.js"></script>

<!--JQuery Uploadify-->
<script type="text/javascript" src="library/uploadify/jquery.uploadify.v2.1.0.min.js"></script>
<script type="text/javascript" src="library/uploadify/swfobject.js"></script>
<link href="library/uploadify/uploadify.css" rel="stylesheet" type="text/css" />
<!--/JQuery Uploadify-->

<!--JQuery editor-->
<script type="text/javascript" src="library/jwysiwyg/jquery.wysiwyg.js"></script>
<link rel="stylesheet" href="library/jwysiwyg/jquery.wysiwyg.css" type="text/css" />
<!--/JQuery editor-->

<!--JQuery Date Picker-->
<script type="text/javascript" src="library/datepicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="library/datepicker/jquery.bgiframe.js"></script><![endif]-->
<script type="text/javascript" src="library/datepicker/jquery.datePicker.min-2.1.2.js"></script>
<link href="library/datepicker/datePicker.css" rel="stylesheet" type="text/css" />
<style>
a.dp-choose-date {
	float: left;
	width: 16px;
	height: 16px;
	padding: 0;
	margin: 5px 3px 0;
	display: block;
	text-indent: -2000px;
	overflow: hidden;
	background: url(images/calendar.png) no-repeat; 
}
a.dp-choose-date.dp-disabled {
	background-position: 0 -20px;
	cursor: default;
}
/* makes the input field shorter once the date picker code
 * has run (to allow space for the calendar icon
 */
input.dp-applied {
	width: 140px;
	float: left;
}
</style>
<!--/JQuery Date Picker-->

<link href="styles/form2.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript"> 
function vacio(q) {
	//funcion que chequea que los campos no sean espacios en blanco
	for ( i = 0; i < q.length; i++ ) {
			if ( q.charAt(i) != " " ) {
					return true
			}
	}
	return false
}

function valida(F) {
	if(F.sector_id.value == 'null') {
	alert("Debe seleccionar un sector");
	F.sector_id.focus();
	return false
	}
	if(vacio(F.horas.value) == false) {
	alert("Debe completar la cantidad de horas")
	F.horas.focus();
	return false
	}
	if(vacio(F.ano.value) == false) {
	alert("El ano es obligatorio")
	F.ano.focus();
	return false
	}
	if(F.mes.value == 'null') {
	alert("Debe seleccionar un mes");
	F.mes.focus();
	return false
	}
	
}

function cargarMeses(year, mes){
	$("#input_mes").empty();
	$("#input_mes").append("<option value=\"n\">Seleccione...</option>");
	var hoy = new Date();
	var select ='';
	var meses={
			  1:"Enero",
			  2:"Febrero",
			  3:"Marzo",
			  4:"Abril",
			  5:"Mayo",
			  6:"Junio",
			  7:"Julio",
			  8:"Agosto",
			  9:"Septiembre",
			  10:"Octubre",
			  11:"Noviembre",
			  12:"Diciembre",
			};
	$.each(meses, function(k,v){
		
		fecha2 = new Date(year.value,(k-1),1);
		if(hoy.getTime()>=fecha2.getTime()){
			
			select = (mes==k)?"selected=\"selected\"":"";
			
			$("#input_mes").append("<option value=\""+k+"\"  "+select+">"+v+"</option>");
		}
	});
	
}
</script> 
</head>

<body onLoad="$('#input_ano').change();">

<?php  if(isset($_POST['agregar'])){ ?>
	<script>
	var dhxWins = parent.dhxWins;
	dhxWins.window('w_empleado_view').attachURL('empleados.ficha.php?empleado_id=<?php echo $_POST['empleado_id']?>');
	</script>
<?php  } ?>

<?php  include_once("config/messages.php"); ?>

<div class="container"> 

<form method="POST" name="form" action="empleado.horas_extras.php?empleado_id=<?php echo $_GET['empleado_id']?>" onSubmit="return valida(this);">
	<input type="hidden" name="empleado_id" value="<?php echo $_GET['empleado_id']?>" />
    
    <div class="label">Sector</div>
        <div class="content">
        <select name="sector_id">
        <option value="null">Seleccionar...</option>
        <?php 
        $sql = "SELECT sector_1_id,sector_2_id FROM empleado_trabajo WHERE empleado_id = ".$_GET['empleado_id']." ORDER BY id DESC LIMIT 1";
        $rsector = mysqli_fetch_array(mysqli_query($conn,$sql));

		$sql = "SELECT sector_id FROM sector_horas_extras WHERE sector_id IN (".$rsector['sector_1_id'].",".$rsector['sector_2_id'].") GROUP BY sector_id";
        $rsTemp = mysqli_query($conn,$sql);
		while($rs = mysqli_fetch_array($rsTemp)){
			$sector[] = $rs['sector_id'];
		}
		if(is_array($sector) and count($sector)>0){
			$lista_sector = implode(",",$sector);
			$sql = "SELECT * FROM sector WHERE id IN (".$lista_sector.")";
			$rsTemp = mysqli_query($conn,$sql); 
			while($rs = mysqli_fetch_array($rsTemp)){ ?>
				<option value="<?php echo $rs['hora_extra_activa']?>"><?php echo $rs['sector']?></option>
			<?php  } ?>
		<?php  } ?>
        </select>
        </div>
        <div style="clear:both;"></div>
    
    <div class="label">Cantidad de horas</div>
        <div class="content">
        <input type="text" size="2" name="horas" />
        </div>
        <div style="clear:both;"></div>
     <div class="label">A&ntilde;o</div>
        <div class="content">
        
        <input type="text" size="2" id="input_ano" name="ano" value="<?php echo $ano?>" onChange="cargarMeses(this,<?php echo $mes?>)"/>
        </div>
        <div style="clear:both;"></div>   
    <div class="label">Mes</div>
        <div class="content">
        <select name="mes" id="input_mes">
            
        </select>
        </div>
        <div style="clear:both;"></div>
    
    
	
    <p align="center"><input type="submit" value="Guardar" name="agregar" /></p> 
</form> 

</div>
</body>
</html>
