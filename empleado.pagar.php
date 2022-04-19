<?php
session_start();
$user_id = $_SESSION['userid'];
if($user_id == '') { header("Location: index.php"); }

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("config/user.php");
include_once("functions/abm.php");
include_once("functions/util.php");

$empleado_id = $_GET['empleado_id'];
$ano = $_GET['ano'];
$mes = $_GET['mes'];

if(isset($_POST['guardar'])){

	$sql = "SELECT * FROM empleado_pago WHERE empleado_id = ".$_POST['empleado_id']." AND ano = ".$_POST['ano'];
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){
		$pagado[$rs['ano']."_".$rs['mes']] = true;
	}
	
                  $today = new DateTime();
                  $today->modify('-1 month');
                  $mes_anterior = $today->format('m');
                  $ano_anterior = $today->format('Y');
                  
	if(!$pagado[$_POST['ano']."_".$_POST['mes']] and ((ACCION_102) or (($_POST['ano'] == $ano_anterior and $_POST['mes'] == $mes_anterior) or ($_POST['ano'] ==  date('Y') and $_POST['mes'] == date('m')) ) )){
	
                        $operacion_monto = $_POST['monto_pendiente']-$_POST['descuentos'];
                   
                        include("functions/comprueba_pagos.php");
		
		if($procesa){
			/*if ($_POST['efectivo_descuento']) {
				$descuentos = $_POST['efectivo_descuento'][0];
			}
			if ($_POST['efectivo_monto']) {
				$monto = $_POST['efectivo_monto'][0];
			}
			if ($_POST['cheque_descuento']) {
				$descuentos = $_POST['cheque_descuento'][0];
			}
			if ($_POST['cheque_monto']) {
				$monto = $_POST['cheque_monto'][0];
			}
			if ($_POST['transferencia_descuento']) {
				$descuentos = $_POST['transferencia_descuento'][0];
			}
			if ($_POST['transferencia_monto']) {
				$monto = $_POST['transferencia_monto'][0];
			}
			if ($_POST['debito_descuento']) {
				$descuentos = $_POST['debito_descuento'][0];
			}
			if ($_POST['debito_monto']) {
				$monto = $_POST['debito_monto'][0];
			}*/
			
			//_log($sql);
			$sql = "INSERT INTO empleado_pago
						(empleado_id,monto,mes,ano,abonado_por,abonado,descuentos, motivo_descuentos)
					VALUES
						(".$_POST['empleado_id'].",'".$operacion_monto."',".$_POST['mes'].",".$_POST['ano'].",$user_id,NOW(),'".$_POST['descuentos']."','".$_POST['motivo_descuentos']."')";
			mysql_query($sql); 
			$operacion_id[] = mysql_insert_id();
			$operacion_tipo = 'sueldo_pago';
			
			include("functions/procesa_pagos.php");
			
				
				$result = 1;
				echo "
				<script>window.open('reciboPDF.php?id=".$operacion_id[0]."', 'Recibo de sueldo');</script>";
			
			
			
			
		}else{
			
			if(($operacion_monto+$monto_interes-$monto_descuento) != $monto_pagado){
				$result = 'Verifique que el sueldo pendiente de pago ('.$operacion_monto.') coincida con el monto que intenta abonar ('.$monto_pagado.')';
			}elseif($fecha_error != 0){
				$result = 'La fecha ingresada no es correcta en alguna de las formas de pago';
			}elseif($error_cheque == true){
				$result = 'Debe completar el titular del cheque';
			}elseif($error_cheque_numero == true){
				$result = 'Ya existe un cheque del banco seleccionado y el numero ingresado';				
			}elseif($fecha_hoy == false){
				$result = 'Le fecha de pago no puede ser posterior a hoy';	
			}else{
				$result = 'No se pudo procesar la operacion';
			}
			
		}
	}else{
                        $result = "El pago no pudo ser realizado, verifique que la fecha no sea posterior al mes actual";
                }
	
	$empleado_id = $_POST['empleado_id'];
	$mes = $_POST['mes'];
	$ano = $_POST['ano'];
	
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
<link href="styles/form.css" rel="stylesheet" type="text/css" />
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

$(document).ready( function() {   // Esta parte del código se ejecutará automáticamente cuando la página esté lista.
    $("#agregarSubmit").click( function() {     // Con esto establecemos la acción por defecto de nuestro botón de enviar.
        if(validaForm()){      
	       
        }
    }); 
   
});
function validaForm() {
	/*if(F.forma_pago.value == 'null') {
		alert("Debe seleccionar una forma de pago");
		F.forma_pago.focus();
		return false;
	}
	
    	var descuento = $('#descuentos').val();
    	if((descuento != '0')&&(F.motivo_descuentos.value == '')) {
    		alert("Debe indicar el motivo del descuento");
    		F.motivo_descuentos.focus();
    		return false;
    	}*/
	$('#mensaje').html('');
	$('#mensaje').hide();
	$.ajax({
	
		type : 'POST',
		data: $("#idForm").serialize(),
		url: 'controlar_abono_empleado_pagar.php',
		success: function(data){
			
			if(data.logs){
				for(var x = 0; x < data.logs.length; x++){
					$('#mensaje').append(data.logs[x]+'<br />');
				}
				$('#mensaje').show();
				$('html,body').animate({
				    scrollTop: $("#mensaje").offset().top
				}, 200);
				return false;
			}else{
				$('#agregarSubmit').val('Procesando...');
				$('#agregarSubmit').attr('disabled','disabled');
				$('#guardar').val('1');
				$("#idForm").submit();
			}
			
		}
	});


    	
	       
}
function montoTotal(campo) {
	
	var total = $('#monto_pendiente').val();
	total = total - campo;
	$('#spanMonto').html('$'+total);
}
</script> 
<script type="text/javascript">
function addFormaDePago(forma_pago_id){

	var datos = ({
		'forma_pago' : forma_pago_id,
		'pago' : 1
	});
	
	$.ajax({
		beforeSend: function(){
			$('#forma_pago_loading').show();
		},
		data: datos,
		url: 'functions/formadepagoSueldo.php',
		success: function(data) {
			$('#forma_pago_loading').hide();
			$('#forma_de_pago').append(data);
			$('.date-pick').datePicker().trigger('change');
		}
	});
}
</script>
</head>

<body>

<?php  include_once("config/messages.php"); ?>
<div id="mensaje" class="error" style="display:none"></div> 
<div class="container" style="font-family:arial; font-size:12px;"> 

<?php 
$sql = "SELECT * FROM empleado_pago WHERE empleado_id = $empleado_id AND mes = $mes AND ano = $ano";
if(mysql_num_rows(mysql_query($sql)) == 0){
?>
    <form method="POST" id="idForm" name="form" action="empleado.pagar.php"">
        <input type="hidden" name="empleado_id" value="<?php echo $empleado_id?>" />
        <input type="hidden" name="ano" value="<?php echo $ano?>" />
        <input type="hidden" name="mes" value="<?php echo $mes?>" />
        <input name="guardar" id="guardar" type="hidden" value="0">
        <?php 
        $sql = "SELECT * FROM empleado WHERE id = $empleado_id";
        $rs = mysql_fetch_array(mysql_query($sql));
        ?>
        <p><strong><?php echo $rs['nombre']?> <?php echo $rs['apellido']?></strong> (<?php echo $mes?>/<?php echo $ano?>) </p>
        
        <p><strong>Sector de trabajo:</strong> 
        <?php 
        $sql = "SELECT empleado_trabajo.*,a.sector as 'sector1', b.sector as 'sector2', espacio_trabajo.espacio FROM empleado_trabajo LEFT JOIN sector as a ON empleado_trabajo.sector_1_id = a.id LEFT JOIN sector as b ON empleado_trabajo.sector_2_id = b.id INNER JOIN espacio_trabajo ON empleado_trabajo.espacio_trabajo_id = espacio_trabajo.id WHERE empleado_trabajo.empleado_id=$empleado_id ORDER BY empleado_trabajo.id DESC LIMIT 0,1";
        $rs = mysql_fetch_array(mysql_query($sql));
        ?>
        <?php echo $rs['sector1'] != '' ? $rs['sector1'] : ''?> <?php echo $rs['sector1'] != '' ? $rs['porcentaje_sector_1']."%" : ''?> 
        <?php echo $rs['sector2'] != '' ? " - ".$rs['sector2'] : ''?> <?php echo $rs['sector2'] != '' ? $rs['porcentaje_sector_2']."%" : ''?>
        </p>
        
        <p><strong>Salario acordado del mes:</strong></p>
        <?php 
        $sql = "SELECT * FROM empleado_sueldo WHERE empleado_id = $empleado_id AND mes = $mes AND ano = $ano ORDER BY sueldo_id DESC LIMIT 0,1";
        $rs = mysql_fetch_array(mysql_query($sql));
        ?>
        Sueldo: $<?php echo $rs['sueldo']?> <br>
        Viaticos: $<?php echo $rs['viaticos']?> <br>
        Asignaciones: $<?php echo $rs['asignaciones']?> <br>
        Presentismo: $<?php echo $rs['presentismo']?> <br>
        Aguinaldo: $<?php echo $rs['aguinaldo']?> <br>
        <?php  $salario = $rs['sueldo'] + $rs['viaticos'] + $rs['asignaciones'] + $rs['presentismo'] + $rs['aguinaldo']; ?>
        Total: $<?php echo $salario?>
        
        <p><strong>Horas extras aprobadas:</strong></p>
        <?php 
            $sql = "
            SELECT
                ehe.*,
                she.*,
                s.sector
            FROM 
                empleado_hora_extra ehe INNER JOIN sector_horas_extras she ON ehe.hora_extra_id = she.id INNER JOIN sector s ON she.sector_id = s.id 
            WHERE 
                ehe.empleado_id = $empleado_id 
                AND 
                ehe.estado = 1 
                AND
                ehe.mes = $mes 
                AND 
                ehe.ano = $ano
            ";
            $rsTemp = mysql_query($sql); echo mysql_error();
            if(mysql_num_rows($rsTemp) > 0) {
                while($rs = mysql_fetch_array($rsTemp)){ ?>
                <?php echo $rs['sector']?>: <?php echo $rs['cantidad_solicitada']?> hrs. solicitadas - <?php echo $rs['cantidad_aprobada']?> hrs. aprobadas = $<?php echo $rs['cantidad_aprobada']*$rs['valor']?> <br>
                <?php  $horas_extras = $horas_extras + ($rs['cantidad_aprobada']*$rs['valor']); ?>
                <?php  } ?>
            <?php  }else{ ?>
                No se han cargado horas extras
            <?php  } ?>
        </p>
        
        <p><strong>Adelantos otorgados:</strong></p>
        <?php 
            $sql = "SELECT * FROM empleado_adelanto WHERE empleado_id = $empleado_id AND mes = $mes AND ano = $ano";
            $rsTemp = mysql_query($sql);
            if(mysql_num_rows($rsTemp) > 0) {
                while($rs = mysql_fetch_array($rsTemp)){ ?>
                <?php echo fechavista($rs['creado'])?> $<?php echo $rs['monto']?> <?php echo $rs['comentarios']?> <br>
                <?php  $adelantos = $adelantos + $rs['monto']; ?>
                <?php  } ?>
            <?php  }else{ ?>
                No se han otorgado adelantos
            <?php  } ?>
        </p>
        <p><strong>Descuentos:</strong></p>
        <label>Monto:</label><input type="text" name="descuentos" id="descuentos" size="3" onblur="if(this.value==''){this.value='0';};montoTotal(this.value)"" value="0" />
        <br><br>
        <label>Motivos:</label><input type="text" name="motivo_descuentos" id="motivo_descuentos" size="40"/>
        <p><strong>Pendiente de pago:</strong><span id="spanMonto">$<?php echo $salario+$horas_extras-$adelantos?></span></p>
        <input type="hidden" id="monto_pendiente" name="monto_pendiente" value="<?php echo $salario+$horas_extras-$adelantos?>"  />
        
        <div class="label">Forma de pago</div>
            <div class="content">
            <select name="forma_pago">
            <option value="n">Seleccionar...</option>
            <?php 
            $sql = "SELECT id,forma_pago FROM forma_pago WHERE id IN (1,3,4,6) ORDER BY forma_pago ";
            $rsTemp = mysql_query($sql);
            while($rs = mysql_fetch_array($rsTemp)){?>
            <option value="<?php echo $rs['id']?>"><?php echo $rs['forma_pago']?></option>
            <?php  } ?>
            </select> &nbsp; <a style="cursor:pointer;" onclick="addFormaDePago(form.forma_pago.options[form.forma_pago.selectedIndex].value)">agregar</a> <img id="forma_pago_loading" src="images/loading.gif" style="display:none" /></li>
            </div>
            <div style="clear:both;"></div>
    
        <div class="form" id="forma_de_pago" style="font-family:arial; font-size:12px; margin-left:-10px; margin-top:-10px;"></div>
        
        <p align="center"><input type="button" value="Guardar" name="agregar" id="agregarSubmit" /></p> 
    </form> 
<?php  }else{ ?>
	<p>El salario para este periodo ya ha sido abonado</p>
<?php  } ?>
</div>
</body>
</html>
