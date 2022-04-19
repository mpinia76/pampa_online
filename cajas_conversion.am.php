<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
session_start();
$user_id = $_SESSION['userid'];

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");
include_once("functions/util.php");

//indicar tabla a editar
$tabla = 'caja_movimiento';

//indicar campos a editar
$campos['id'] 				= array(
								'type'				=> 'text',
								'input_type'		=> 'hidden'
							);
$campos['fecha'] 			= array(
								'type'		=> 'date',
								'label'		=> 'Fecha',
								'value'		=> date('d/m/Y')
							);
$campos['origen']			= array(
								
								'type'				=> 'combo',
								'label'				=> 'Caja origen',
								'tabla'				=> 'caja',
								'campo_id'			=> 'id',
								'campo'				=> 'caja',
								'sql'				=> "SELECT caja.id as id, caja.caja as caja FROM caja INNER JOIN usuario_caja ON usuario_caja.caja_id=caja.id AND usuario_caja.usuario_id=$user_id",
								'requerid'			=> true
							);
$campos['caja_id']			= array(
								'type'				=> 'combo',
								'label'				=> 'Caja destino',
								'tabla'				=> 'caja',
								'campo_id'			=> 'id',
								'campo'				=> 'caja',
								'sql'				=> "SELECT caja.id as id, caja.caja as caja FROM caja INNER JOIN usuario_caja ON usuario_caja.caja_id=caja.id AND usuario_caja.usuario_id=$user_id",
								'requerid'			=> true
							);
$campos['moneda_id']			= array(
								'type'				=> 'combo',
								'label'				=> 'Moneda',
								'tabla'				=> 'moneda',
								'campo_id'			=> 'id',
								'campo'				=> 'moneda',
								'sql'				=> "SELECT moneda.id as id, moneda.moneda as moneda FROM moneda",
								'requerid'			=> true
							);
$campos['saldo']			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Monto $',
								'size'				=> '5',
								'requerid' 			=> true
							);
$campos['cambio']			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Cambio',
								'size'				=> '5',
								'requerid' 			=> true
							);
$campos['monto']			= array(
								'type' 				=> 'text',
								'input_type'		=> 'text',
								'label' 			=> 'Total',
								'size'				=> '5',
								'requerid' 			=> true
							);

							


if(($_POST['agregar'])){
	//proceso la entrada de plata a la caja
	$saldo1=$_POST['saldo'];
	if ($_POST['moneda_id']!=1) {
		
		$sql = "SELECT id,monto_moneda,cambio,usados FROM caja_movimiento WHERE monto_moneda>0 AND caja_id=".$_POST['origen']." AND moneda_id = ".$_POST['moneda_id']. " ORDER BY id ASC";
		//echo $sql;
		$rsTemp = mysql_query($sql);
		$saldo=0;
		$ok=1;
		while($rs = mysql_fetch_array($rsTemp) AND $ok){
			if ($_POST['saldo']<=($rs['monto_moneda']-$rs['usados'])) {
				$monto_moneda +=$_POST['saldo'];
				$cambio=$rs['cambio'];
				$saldo +=($_POST['saldo']*$cambio);
				//echo $saldo."<br>";
				//echo $monto_moneda."<br>";
				$usados = $rs['usados']+$_POST['saldo'];
				$sql_update = "UPDATE caja_movimiento SET usados = ".$usados." WHERE id = ".$rs['id'] ;
				//echo $sql_update."<br>";
				_log($sql_update);
				mysql_query($sql_update);
				$ok=0;
			}
			else{
				$monto_moneda +=($rs['monto_moneda']-$rs['usados']);
				$cambio=$rs['cambio'];
				$saldo +=(($rs['monto_moneda']-$rs['usados'])*$cambio);
				$usados_ant = $rs['usados'];//para anular la actualización en caso de no usarlos
				$usados = $rs['usados']+($rs['monto_moneda']-$rs['usados']);
				$sql_update = "UPDATE caja_movimiento SET usados = ".$usados." WHERE id = ".$rs['id'] ;
				_log($sql_update);
				//echo $saldo."<br>";
				//echo $monto_moneda."<br>";
				//echo $sql_update."<br>";
				mysql_query($sql_update);
				$_POST['saldo'] -=($rs['monto_moneda']-$rs['usados']);
				$ultimo_id=$rs['id'];
			}
			//$saldo=$_POST['saldo']*$_POST['cambio'];
		}
		
		$cambio = $saldo / $monto_moneda;
		
	}
	if ($ok) {
		$sql_update = "UPDATE caja_movimiento SET usados = ".$usados_ant." WHERE id = ".$ultimo_id ;
		_log($sql_update);
		mysql_query($sql_update);
		$saldo=0;
	}
	if ($saldo!=0) {
		$time = time();

		$hora = date("H:i:s", $time);
		
		$fecha =fechasql($_POST['fecha']).' '.$hora;	
		$monto = $saldo1*$_POST['cambio'];
		$sql_entra = "INSERT INTO $tabla (fecha,origen,caja_id,monto,usuario_id,moneda_id,monto_moneda,cambio) 
					VALUES 
					('".$fecha."','cajacambio_".$_POST['origen']."_".$_POST['moneda_id']."_".$_POST['cambio']."','".$_POST['caja_id']."','".$monto."',".$user_id.",'1','0','0')";
		mysql_query($sql_entra);
		_log($sql_entra);
		//echo mysql_error();
		//echo $sql_entra."<br>";
		//proceso la salida de plata
		$sql_sale = "INSERT INTO $tabla (fecha,origen,caja_id,monto,usuario_id,moneda_id,monto_moneda,cambio) 
					VALUES 
					('".$fecha."','haciacambio_".$_POST['caja_id']."','".$_POST['origen']."','-".$saldo."',".$user_id.",'".$_POST['moneda_id']."','-".$monto_moneda."','".$cambio."')";
		mysql_query($sql_sale);
		_log($sql_sale);
		//echo $sql_sale."<br>";
		$result = 1;
	}
	else{
		$result = "La caja de origen no dispone de saldo suficiente para la moneda seleccionada";
	}
}

$form = new Form();
$form->setLegend('Movimientos en efectivo'); //nombre del form
$form->setAction('cajas_conversion.am.php'); //a donde hacer el post

if(isset($dataid)){

	$sql = "SELECT * FROM $tabla WHERE id=".$dataid; //traer datos
	$rsTemp = mysql_query($sql);
	$rs = mysql_fetch_array($rsTemp);
	
	foreach($campos as $clave=>$valores){
		$campos[$clave][3] = $rs[$clave];
	}
		
	$form->setBotonValue('Editar caja'); //leyendo del boton
	$form->setBotonName('editar'); 
	
}else{

	$form->setBotonValue('Hacer cambio'); //leyenda del boton
	$form->setBotonName('agregarSubmit');
	
}

$form->setCampos($campos);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
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
<script>
function consultarDescubierto (caja){
	
	var datos = ({
		'caja_id' : caja,
		'monto':$( "#monto" ).val()
	});
	
	$.ajax({
		beforeSend: function(){
			$('#loading').show();
		},
		data: datos,
		url: 'functions/consultarDescubierto.php',
		success: function(data) {
			
			if(data == 'no'){		
				alert('La caja de origen no tiene fondos suficientes');
				$('#origen').val(0);
			}
			$('#loading').hide();
			
		}
	});

}

function consultarSincronismo (caja){
	
	var datos = ({
		'caja_id' : caja,
		'fecha' : $('#fecha').val()
	});
	
	$.ajax({
		beforeSend: function(){
			$('#loading').show();
		},
		data: datos,
		url: 'functions/consultarSincronismo.php',
		success: function(data) {
			
			if(data == 'no'){		
				alert('Por favor, realice la conciliacion y sincronice la caja sobre la que desea generar la operacion para continuar');
				$('#origen').val(0);
			}
			$('#loading').hide();
			
		}
	});

}

function consultarSincronismoFecha (caja){
	
	var datos = ({
		'caja_id' : caja,
		'fecha' : $('#fecha').val()
	});
	
	$.ajax({
		beforeSend: function(){
			$('#loading').show();
		},
		data: datos,
		url: 'functions/consultarSincronismoFecha.php',
		success: function(data) {
			
			if(data == 'no'){		
				alert('Movimiento no permitido: La caja se encuentra conciliada y sincronizada para la fecha del movimiento que intenta realizar. Contactar administrador');
				$('#efectivo_caja_id').val(0);
			}
			$('#loading').hide();
			
		}
	});

}

$(document).ready(function() {
	
	$( "#origen" ).change(function() {
		consultarSincronismo ($( "#origen" ).val());
		consultarSincronismoFecha ($( "#origen" ).val());	
		consultarDescubierto ($( "#origen" ).val());
    });
	$( "#caja_id" ).change(function() {
		
		consultarSincronismoFecha ($( "#caja_id" ).val());	
    });
	$( "#moneda_id" ).change(function() {
		$("#monto").attr("disabled", true);
		$( "#monto" ).val(0);
		$( "#saldo" ).val(0);
		$( "#cambio" ).val(0);
		
    });

	$( "#saldo" ).change(function() {
		$( "#monto" ).val(parseFloat($( "#saldo" ).val()*$( "#cambio" ).val()));
		$("#monto").attr("disabled", true);
		consultarDescubierto ($( "#origen" ).val());
		
    });
	$( "#cambio" ).change(function() {
		$( "#monto" ).val(parseFloat($( "#saldo" ).val()*$( "#cambio" ).val()));
		
		$("#monto").attr("disabled", true);
		consultarDescubierto ($( "#origen" ).val());
    });
});


</script>
<?php echo $form->printJS()?>

<link href="styles/form2.css" rel="stylesheet" type="text/css" />
</head>

<body>

<?php include_once("config/messages.php"); ?>

<?php echo $form->printHTML()?>

</body>
</html>
