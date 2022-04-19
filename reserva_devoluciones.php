<?php

include_once("config/db.php");
include_once("config/user.php");
include_once("functions/fechasql.php");

if(isset($_POST['guardar'])){
    $forma_pago_texto = array (
        1 => 'EFECTIVO',
        3 => 'CHEQUE',
        4 => 'TRANSFERENCIA'
    );
    
    switch($_POST['forma_pago']){
        case 1:
            $monto = $_POST['efectivo_monto'][0];
            break;
        case 3:
            $monto = $_POST['cheque_monto'][0];
            break;
        case 4:
            $monto = $_POST['transferencia_monto'][0];
            break;
    }
	//$result = $monto;
	if($monto){
		$sql = "SELECT sum(monto_cobrado) as 'total_cobrado' FROM reserva_cobros WHERE reserva_id = ".$_GET['reserva_id']." and tipo != 'DESCUENTO'";
		$rs = mysql_fetch_array(mysql_query($sql)); echo mysql_error();
		$cobrado = $rs['total_cobrado'];
		
		if($monto > $cobrado){
			$result = "El monto de devolucion ($monto) no puede ser mayor al monto cobrado ($cobrado)";
		}else{
		   $sql = sprintf("INSERT INTO reserva_devoluciones (reserva_id,usuario_id,forma_pago,fecha,monto,motivo) VALUES (%d,%d,'%s','%s',%d,'%s')",
					$_POST['reserva_id'],
					$_POST['usuario_id'],
					$forma_pago_texto[$_POST['forma_pago']],
					fechasql($_POST['fecha']),
					$monto,
					$_POST['motivo']);

				mysql_query($sql); //echo mysql_error();
			$operacion_tipo = 'reserva_devolucion';
			$operacion_id[] = mysql_insert_id();
			include("functions/procesa_pagos.php");
			
				 
			$result = 1;
			
			
		}
	}
	else {
		$result = "Debe cargar un monto";
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Devoluciones de reserva</title>
<link rel="stylesheet" type="text/css" href="v2/css/jquery-ui.css" />
<link href="styles/form.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="v2/js/jquery.js"></script>
<script type="text/javascript" src="v2/js/jquery-ui.js"></script>
<script type="text/javascript" src="v2/js/jquery.ui.datepicker-es.js"></script>
<style>
    .formContainer{
        font-size: 12px;
    }
    table tr{
        padding: 2px;
    }
    .ui-widget {
        font-size:12px;
    }
    .info{
        background: #fff;
        padding: 5px;
        font-size: 12px;
        font-family: arial;
        margin: 10px;
    }
</style>
</head>

<body>
<?php 
$sql = "SELECT reservas.*, clientes.*, apartamentos.* FROM reservas INNER JOIN clientes ON reservas.cliente_id = clientes.id INNER JOIN apartamentos ON reservas.apartamento_id = apartamentos.id WHERE reservas.id = ".$_GET['reserva_id'];
$reserva = mysql_fetch_array(mysql_query($sql));
?>
<div class="info">
    <strong>Titular: </strong> <?php echo $reserva['nombre_apellido']?> <br/>
    <strong>Apartamento: </strong> <?php echo $reserva['apartamento']?> <br/>
    <strong>Check In: </strong> <?php echo fechavista($reserva['check_in'])?> <?php echo $reserva['hora_check_in']?> hs. <strong>Check Out: </strong> <?php echo fechavista($reserva['check_out']);?> <?php echo $reserva['late_check_out']?> hs.
</div>
<div class="formContainer">
<form method="post" name="form" action="reserva_devoluciones.php?reserva_id=<?php echo $_GET['reserva_id']; ?>" onsubmit="return valida_form();">
    <input type="hidden" name="reserva_id" value="<?php echo $_GET['reserva_id']; ?>"/>
    <input type="hidden" name="usuario_id" value="<?php echo $user_id; ?>"/>
    <fieldset>
    <legend>Detalle de las devoluciones</legend> 
    <ul class="form">
    <?php
    if(isset($_POST['guardar']) and isset($result)){ 
        include_once("config/messages.php");
    }?>
    <?php
    $sql = "SELECT rd.forma_pago, rd.fecha, rd.monto, rd.motivo, caja.caja, ctc.nombre, cc.numero
        FROM reserva_devoluciones rd 
        INNER JOIN rel_pago_operacion rpo ON rd.id = rpo.operacion_id AND rpo.operacion_tipo = 'reserva_devolucion' 
        LEFT JOIN efectivo_consumo ec ON rpo.forma_pago_id = ec.id LEFT JOIN caja ON caja.id = ec.caja_id 
        LEFT JOIN transferencia_consumo tc ON rpo.forma_pago_id = tc.id LEFT JOIN cuenta ctc ON ctc.id = tc.cuenta_id 
        LEFT JOIN cheque_consumo cc ON rpo.forma_pago_id = cc.id
        WHERE rd.reserva_id = ".$_GET['reserva_id']." GROUP BY rd.id ORDER BY rd.fecha ASC"; 
    $rsTemp = mysql_query($sql);
    if(mysql_num_rows($rsTemp) > 0){ ?>
    <table width="100%">
        <tr>
            <td width="100"><strong>Fecha</strong></td>
            <td width="130"><strong>Forma de pago</strong></td>
            <td width="230"><strong>Detalle</strong></td>
            <td><strong>Motivo</strong></td>
            <td width="80" align="right"><strong>Monto</strong></td>
        </tr>
    <?php
    $total = 0;
    while($rs = mysql_fetch_array($rsTemp)){
        switch($rs['forma_pago']){
            case 'EFECTIVO':
                $detalle = 'desde caja '.$rs['caja'];
                break;
            case 'TRANSFERENCIA':
                $detalle =  'desde cuenta '.$rs['nombre'];
                break;
            case 'CHEQUE':
                $detalle = 'cheque numero '.$rs['numero'];
                break;
        } ?>
        <tr>
            <td><?php echo fechavista($rs['fecha'])?></td>
            <td><?php echo $rs['forma_pago']?></td>
            <td><?php echo $detalle?></td>
            <td><?php echo $rs['motivo']?></td>
            <td align="right">$<?php echo $rs['monto']?></td>
        </tr>
        <?php $total = $total + $rs['monto']; } ?>
        <tr>
            <td align="right" colspan="5"><strong>Total: $<?php echo   number_format($total,2)?></strong></td>
        </tr>
    </table>
   <?php }else{ ?>
            <p style="background: #ff;">No se ha registrado ninguna devolucion todavia</p>
    <?php } ?>
            <?php if($reserva['estado'] != 2){ ?>
            <li><label>Fecha:</label><input class="datepicker" name="fecha" type="text" value="<?php echo date('d/m/Y'); ?>"  /></li>
            <li><label>Motivo:</label><input name="motivo" type="text" size="60" /></li>
            <li><label>Forma de pago:</label><select id="forma_pago" name="forma_pago">
                <option value="n">Seleccionar...</option>
                <?php
                $sql = "SELECT id,forma_pago FROM forma_pago WHERE id IN (1,3,4) ORDER BY forma_pago ";
                $rsTemp = mysql_query($sql);
                while($rs = mysql_fetch_array($rsTemp)){?>
                    <option value="<?php echo $rs['id']?>"><?php echo $rs['forma_pago']?></option>
                <?php } ?>
            </select> &nbsp; <img id="forma_pago_loading" src="images/loading.gif" style="display:none" /></li>
            <div id="forma_de_pago"></div>
            </ul>
            </fieldset>
            <p align="center"><input type="submit" name="guardar" value="Guardar"/></p>
            <?php }else{?> 
            </ul>
            </fieldset>
            <?php } ?>
        </form>
     </div>
    <script>
    $.datepicker.regional[ "es" ]
    $(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });
    $('.datepicker').datepicker();
    $('#forma_pago').change(function(){
        if($('#forma_pago').val() != 'n'){
            $.ajax({
                beforeSend: function(){
                    $('#forma_pago_loading').show();
                },
                data: {'forma_pago' : $(this).val(),
            		'pago' : 1},
                url: 'functions/formadepago.php',
                success: function(data) {
                    $('#forma_pago_loading').hide();
                    $('#forma_de_pago').html(data);
                    $('.date-pick').datepicker();
                }
            });
        }else{
            $('#forma_de_pago').html('');
        }
    });
    function valida_form(){
        if(form.fecha.value == ''){
            alert('Debe seleccionar la fecha');
            form.fecha.focus();
            return false;
        }
        if(form.motivo.value == ''){
            alert('Debe completar con un motivo');
            form.motivo.focus();
            return false;
        }
        if($('#forma_pago').val() == 'n'){
            alert('Debe seleccionar una forma de pago');
            $('#forma_pago').focus();
            return false;
        }
    }
    </script>
</body>
</html>
