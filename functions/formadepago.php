<?php
//DATOS DEL USUARIO
session_start();
$user_id = $_SESSION['userid'];

include_once("../config/db.php");

$forma_pago = $_GET['forma_pago'];
$pago = ($_GET['pago'])?1:0;
$div_id 	= rand(1000,9999);
?>
<script type="text/javascript">

$('.fecha').datePicker({startDate:'01/01/2010'});



function consultarDescubierto (){
	var esPago = <?php echo $pago; ?>;
	if(esPago){
		var datos = ({
			'caja_id' : $('#efectivo_caja_id').val(),
			'monto' : $('#monto').val()
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
					$('#efectivo_caja_id').val(0);
				}
				$('#loading').hide();

			}
		});
	}
}

function consultarSincronismo (){
	var esPago = <?php echo $pago; ?>;
	if(esPago){
		var datos = ({
			'caja_id' : $('#efectivo_caja_id').val(),
			'fecha' : $('#efectivo_fecha').val()
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
					$('#efectivo_caja_id').val(0);
				}
				$('#loading').hide();

			}
		});
	}
}

function consultarSincronismoFecha (){

	var datos = ({
		'caja_id' : $('#efectivo_caja_id').val(),
		'fecha' : $('#efectivo_fecha').val()
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






</script>

<div id="<?php echo $div_id?>">
<?php
switch($forma_pago){

	case 1:
?>
	<h3>Efectivo</h3>
	<input type="hidden" name="efectivo[]" value="1">
	<li><label>Caja origen:</label><select name="efectivo_caja_id[]" id="efectivo_caja_id" onChange="consultarDescubierto();consultarSincronismo();consultarSincronismoFecha()">
	<option value="0">Seleccionar...</option>
<?php
	$sql = "SELECT caja.* FROM caja INNER JOIN usuario_caja ON usuario_caja.caja_id=caja.id AND usuario_caja.usuario_id=".$user_id;

	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){
		$descubierto=1;
		if ($pago) {
			$saldo_sql = "SELECT SUM(monto) as saldo FROM caja_movimiento WHERE caja_id=".$rs['id'];
			$saldo_rs = mysql_fetch_array(mysql_query($saldo_sql));
			$saldo = $saldo_rs['saldo'];
			if ($saldo<0) {
				$sql = "SELECT * FROM caja WHERE id = ".$rs['id'];
				$rsTemp1 = mysql_query($sql);
				if($rs1 = mysql_fetch_array($rsTemp1)){
					$descubierto = $rs1['descubierto'];
				}
			}

		}
		if ($descubierto) {

	?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['caja']?></option>
	<?php }} ?>
	</select></li>
	<li><label>Fecha:</label><input type="text" name="efectivo_fecha[]" id="efectivo_fecha" class="date-pick dp-applied" value="<?php echo date("d/m/Y")?>" onChange="consultarSincronismo();consultarSincronismoFecha()"/></li>
	<li><label>Monto:</label><input type="text" name="efectivo_monto[]" id="monto" size="3" onChange="consultarDescubierto();"/><span class="leftNote">$</span></li>
	<li><label>Interes:</label><input type="text" name="efectivo_interes[]" size="3" onblur="if(this.value==''){this.value='0';};" value="0" /><span class="leftNote">$</span></li>
	<li><label>Descuento:</label><input type="text" name="efectivo_descuento[]" size="3" onblur="if(this.value==''){this.value='0';};" value="0" /><span class="leftNote">$</span></li>
<?php
	break;

	case 2:
?>
	<h3>Tarjeta</h3>
	<input type="hidden" name="tarjeta[]" value="1">
	<li><label>Tarjeta:</label><select name="tarjeta_tarjeta_id[]">
<?php
	$sql = "SELECT banco.banco,tarjeta_marca.marca,tarjeta.titular,tarjeta.id FROM tarjeta INNER JOIN tarjeta_marca ON tarjeta.tarjeta_marca_id=tarjeta_marca.id INNER JOIN banco ON tarjeta.banco_id=banco.id WHERE tarjeta.activa=1 ORDER BY banco.banco";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['banco']?> <?php echo $rs['marca']?> <?php echo $rs['titular']?></option>
	<?php } ?>
	</select></li>
	<li><label>Fecha de la operacion:</label><input type="text" name="tarjeta_fecha[]" class="date-pick dp-applied" value="<?php echo date("d/m/Y")?>" /></li>
	<li><label>Comprobante:</label><input type="text" name="tarjeta_comprobante[]" size="10" /></li>
	<li><label>Monto:</label><input type="text" name="tarjeta_monto[]" size="3" /><span class="leftNote">$</span></li>
	<li><label>Interes:</label><input type="text" name="tarjeta_interes[]" size="3" onblur="if(this.value==''){this.value='0';};" value="0" /><span class="leftNote">$</span></li>
	<li><label>Descuento:</label><input type="text" name="tarjeta_descuento[]" size="3" onblur="if(this.value==''){this.value='0';};" value="0" /><span class="leftNote">$</span></li>
	<li><label>Cantidad de cuotas:</label><input type="text" name="tarjeta_cuotas[]" size="3" onblur="if(this.value==''){this.value='1';};" value="1" /></li>
<?php
	break;

	case 3:
?>
	<h3>Cheque</h3>
	<script type="text/javascript">



$('#cheque_cuenta_id_<?php echo  $div_id?>').change(function(){

    if($(this).val()!=''){
    	$('#divCheques_<?php echo  $div_id?>').html('');
         $('#divCheques_<?php echo  $div_id?>').hide();
         var datos = ({
             'cuenta_id' : $(this).val(),
             'div_id' : <?php echo  $div_id?>
          });
         $.ajax({
             beforeSend: function(){

             },
             data: datos,
             url: 'functions/dameChequeras.php',
             success: function(data) {

                 $('#chequera_combo_<?php echo  $div_id?>').html(data);

             }
         });
    }else{
         $('#chequera_combo_<?php echo  $div_id?>').html('');
    }
})



function seleccionarChequera (){

	if($('#cheque_chequera_id_<?php echo  $div_id?>').val()!=''){
        $.ajax({
            url : 'v2/chequera_cheques/getCheques/'+$('#cheque_chequera_id_<?php echo  $div_id?>').val()+'/1',
            dataType: 'html',

            success: function(data){
                $('#divCheques_<?php echo  $div_id?>').html(data);
                $('#divCheques_<?php echo  $div_id?>').show();
            }
        });
    }else{
         $('#divCheques_<?php echo  $div_id?>').html('');
         $('#divCheques_<?php echo  $div_id?>').hide();
    }
}
	$('#alPortador_<?php echo  $div_id?>').change(function(){
	if ($(this).is(':checked')) {

      	$('#cheque_titular_<?php echo  $div_id?>').val('Al portador');

    }
    else {
    	$('#cheque_titular_<?php echo  $div_id?>').val('');
    	}
})

function seleccionarCheque (cheque){
	if ($('#'+cheque.id).is(':checked')) {



	 	var valorTexto = $('#'+cheque.id).attr('numero');
        var cuenta_id = $('#cheque_cuenta_id_<?php echo  $div_id?>').val();
        var chequera_id = $('#cheque_chequera_id_<?php echo  $div_id?>').val();
        if ($('#cheque_titular_<?php echo  $div_id?>').val()!=''){
        	var titular = $('#cheque_titular_<?php echo  $div_id?>').val();
        	$("#ul-cheques_<?php echo  $div_id?>").append( '<li id="li-'+cheque.id+'_<?php echo  $div_id?>"><span class="verticalAlign"><strong>' +valorTexto+'</strong></span><span class="verticalAlign">-</span><input type="hidden" name="cheque[]" value="1"><input type="hidden" name="cheque_cuenta_id[]" value="'+cuenta_id+'"><input type="hidden" name="chequera_id[]" value="'+chequera_id+'"><input type="hidden" name="cheque_titular[]" value="'+titular+'"><input type="hidden" id="chequera_cheque_id" name="chequera_cheque_id[]" value="' +cheque.value+'" /><input type="hidden" id="cheque_numero" name="cheque_numero[]" value="' +valorTexto+'" /><span class="verticalAlign">Monto:</span><input type="text" name="cheque_monto[]" size="3" /><span class="leftNote">$</span><span class="verticalAlign">Int.:</span><input type="text" name="cheque_interes[]" size="3" onblur="if(this.value==\'\'){this.value=\'0\';};" value="0" /><span class="leftNote">$</span><span class="verticalAlign">Desc.:</span><input type="text" name="cheque_descuento[]" size="3" onblur="if(this.value==\'\'){this.value=\'0\';};" value="0" /><span class="leftNote">$</span><span class="verticalAlign">Fecha:</span><input type="text" name="cheque_fecha[]" style="width:80px;" class="fecha date-pick" value="<?php echo date("d/m/Y")?>" /><a href="#" onClick="quitarCheque(\''+cheque.id+'\');return false;"><span class="verticalAlign"><img src="images/bt_delete.png" align="absmiddle" /></span></a>');

            $('.fecha').datePicker({startDate:'01/01/2010'});
            }
        else{
			alert('Antes de seleccionar un cheque debe cargar el titular');
			$('#'+cheque.id ).removeAttr("checked");
            }




	}
	else{
		$('#li-'+cheque.id+'_<?php echo  $div_id?>').remove();
	}


}
function quitarCheque (cheque_id){
	$('#li-'+cheque_id+'_<?php echo  $div_id?>').remove();
}
</script>
<style><!--
.verticalAlign{
            float:left;
            padding: 5px 0 0 0;
        }
--></style>
	<li><label>Cuenta:</label><select name="cheque_cuenta_aux" id="cheque_cuenta_id_<?php echo  $div_id?>">
	<option value="">Seleccionar...</option>
<?php
	$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.saldo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id WHERE emite_cheques=1 ORDER BY banco.banco";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['banco']?> <?php echo $rs['sucursal']?> <?php echo $rs['cuenta_tipo']?> <?php echo $rs['nombre']?></option>
	<?php } ?>
	</select></li>
	<li id="chequera_<?php echo  $div_id?>"><label>Chequera:</label>
		<div id="chequera_combo_<?php echo  $div_id?>">
			<select >
			 <option value="">Seleccionar...</option>
			</select>
		</div>
	</li>
	<li><label>Paguese a:</label><input type="text" name="cheque_titular_aux" id="cheque_titular_<?php echo  $div_id?>" /><input type="checkbox" id="alPortador_<?php echo  $div_id?>" name="alPortador"></input>Se emite al portador</li>
	<div style="padding:5px; display:none" id="divCheques_<?php echo  $div_id?>">

	</div>


	<ul id="ul-cheques_<?php echo  $div_id?>">

	</ul>

<?php
	break;

	case 4:
?>
	<h3>Transferencia</h3>
	<input type="hidden" name="transferencia[]" value="1">
	<li><label>Cuenta origen:</label><select name="transferencia_cuenta_id[]">
<?php
	$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.saldo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN usuario_cuenta ON usuario_cuenta.cuenta_id=cuenta.id AND usuario_cuenta.usuario_id=$user_id ORDER BY banco.banco";
	//echo $sql;
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['banco']?> <?php echo $rs['sucursal']?> <?php echo $rs['cuenta_tipo']?> <?php echo $rs['nombre']?></option>
	<?php } ?>
	</select></li>
	<li><label>Cuenta destino:</label><textarea name="transferencia_cuenta_destino[]"></textarea></li>
	<li><label>A la fecha:</label><input type="text" name="transferencia_fecha[]" class="date-pick dp-applied" value="<?php echo date("d/m/Y")?>" /></li>
	<li><label>Monto:</label><input type="text" name="transferencia_monto[]" size="3" /><span class="leftNote">$</span></li>
	<li><label>Interes:</label><input type="text" name="transferencia_interes[]" size="3" onblur="if(this.value==''){this.value='0';};" value="0" /><span class="leftNote">$</span></li>
	<li><label>Descuento:</label><input type="text" name="transferencia_descuento[]" size="3" onblur="if(this.value==''){this.value='0';};" value="0" /><span class="leftNote">$</span></li>
<?php
	break;

	case 5:
?>
	<h3>Cuentas a pagar</h3>
	<input type="hidden" name="cuenta[]" value="1">
	<li><label>Monto:</label><input type="text" name="cuenta_monto[]" size="3" /><span class="leftNote">$</span></li>
<?php
	break;

	case 6:
?>
	<h3>Debitar de cuenta</h3>
	<input type="hidden" name="debito[]" value="1">
	<li><label>Cuenta:</label><select name="debito_cuenta_id[]">
<?php
	$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.saldo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN usuario_cuenta ON usuario_cuenta.cuenta_id = cuenta.id AND usuario_cuenta.usuario_id = $user_id ORDER BY banco.banco";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['banco']?> <?php echo $rs['sucursal']?> <?php echo $rs['cuenta_tipo']?> <?php echo  $rs['nombre']?></option>
	<?php } ?>
	</select></li>
	<li><label>Fecha:</label><input type="text" name="debito_fecha[]" class="fecha date-pick dp-applied" value="<?php echo   date("d/m/Y")?>" /></li>
	<li><label>Monto:</label><input type="text" name="debito_monto[]" size="3" /><span class="leftNote">$</span></li>
	<li><label>Interes:</label><input type="text" name="debito_interes[]" size="3" onblur="if(this.value==''){this.value='0';};" value="0" /><span class="leftNote">$</span></li>
	<li><label>Descuento:</label><input type="text" name="debito_descuento[]" size="3" onblur="if(this.value==''){this.value='0';};" value="0" /><span class="leftNote">$</span></li>
<?php
	break;

                  case 7:
?>
                  <h3>Cheque pendiente de acreditar</h3>
                  <input type="hidden" name="cheque_acreditar[]" value="1" />
                  <li><label>Cheque:</label><select id="cheque_acreditar_<?php echo  $div_id?>" name="cheque_acreditar_id[]">
                          <option value="">Seleccionar...</option>
                  <?php
                  $sql = "SELECT * FROM cobro_cheques WHERE acreditado = 0 AND asociado_a_pagos = 0";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){
                        $cheque_monto[$rs['id']] = $rs['monto_neto'] + $rs['interes'];
                  ?>
                          <option value="<?php echo  $rs['id']?>"><?php echo  $rs['banco']?> <?php echo $rs['numero']?> $<?php echo $rs['monto_neto'] + $rs['interes'];?></option>
                  <?php } ?>
                  </select></li>
                  <input type="hidden" id="cheque_acreditar_monto_<?php echo $div_id?>" value="" name="cheque_acreditar_monto[]" />
                  <script>
                  $('#cheque_acreditar_<?php echo $div_id?>').change(function(){
                      var cheque_monto = <?php echo json_encode($cheque_monto)?>;
                      $('#cheque_acreditar_monto_<?php echo $div_id?>').val(cheque_monto[$(this).val()]);
                  });
                  </script>
<?php
                  break;
}
?>
	<a style="cursor:pointer; color:#0000FF; text-decoration:underline;" onClick="$('#<?php echo $div_id?>').remove()">eliminar</a>
</div>
