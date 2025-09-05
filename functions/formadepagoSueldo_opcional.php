<script type="text/javascript">
<!--
$('.fecha').datePicker({startDate:'01/01/2010'});
//-->
</script>
<?php
//DATOS DEL USUARIO
session_start();
$user_id = $_SESSION['userid'];

include_once("../config/db.php");

$forma_pago = $_GET['forma_pago'];
$monto_pendiente = $_GET['monto_pendiente'];
$div_id 	= rand(1000,9999);
?>
<div id="<?php echo $div_id?>">
<?php
switch($forma_pago){

	case 1:
?>
	<h3>Efectivo</h3>
	<input type="hidden" name="efectivo[]" value="1">
	<li><label>Caja origen:</label><select name="efectivo_caja_id[]">
<?php
	$sql = "SELECT caja.* FROM caja INNER JOIN usuario_caja ON usuario_caja.caja_id=caja.id AND usuario_caja.usuario_id=".$user_id;
	$rsTemp = mysqli_query($conn,$sql);
	while($rs = mysqli_fetch_array($rsTemp)){?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['caja']?></option>
	<?php } ?>
	</select></li>
	<li><label>Fecha:</label><input type="text" name="efectivo_fecha[]" class="date-pick dp-applied" value="<?php echo date("d/m/Y")?>" /></li>
	<li><label>Interes:</label><input type="text" name="efectivo_interes[]" size="3" onblur="if(this.value==''){this.value='0';};" value="0" /><span class="leftNote">$</span></li>
	<li><label>Descuento:</label><input type="text" id="efectivo_descuento" name="efectivo_descuento[]" size="3" onblur="if(this.value==''){this.value='0';};montoTotal(this.value, 'efectivo_monto')" value="0" /><span class="leftNote">$</span></li>
	<li><label>Motivo:</label><input type="text" name="motivo_descuentos" size="40" value="" /></li>
	<li><label>Monto:</label><input type="text" id="efectivo_monto" name="efectivo_monto[]" size="3" value="<?php echo $monto_pendiente?>"/><span class="leftNote">$</span></li>
<?php
	break;
	
	case 2:
?>
	<h3>Tarjeta</h3>
	<input type="hidden" name="tarjeta[]" value="1">
	<li><label>Tarjeta:</label><select name="tarjeta_tarjeta_id[]">
<?php
	$sql = "SELECT banco.banco,tarjeta_marca.marca,tarjeta.titular,tarjeta.id FROM tarjeta INNER JOIN tarjeta_marca ON tarjeta.tarjeta_marca_id=tarjeta_marca.id INNER JOIN banco ON tarjeta.banco_id=banco.id WHERE tarjet.activa=1 ORDER BY banco.banco";
	$rsTemp = mysqli_query($conn,$sql);
	while($rs = mysqli_fetch_array($rsTemp)){?>
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
	<input type="hidden" name="cheque[]" value="1">
	<li><label>Cuenta:</label><select name="cheque_cuenta_id[]">
<?php
	$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.saldo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id ORDER BY banco.banco";
	$rsTemp = mysqli_query($conn,$sql);
	while($rs = mysqli_fetch_array($rsTemp)){?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['banco']?> <?php echo $rs['sucursal']?> <?php echo $rs['cuenta_tipo']?> <?php echo $rs['nombre']?></option>
	<?php } ?>
	</select></li>
	<li><label>Numero:</label><input type="text" name="cheque_numero[]" /></li>
	<li><label>Paguese a:</label><input type="text" name="cheque_titular[]" /></li>
	<li><label>En la fecha:</label><input type="text" name="cheque_fecha[]" class="date-pick dp-applied" value="<?php echo date("d/m/Y")?>" /></li>
	<li><label>Interes:</label><input type="text" name="cheque_interes[]" size="3" onblur="if(this.value==''){this.value='0';};" value="0" /><span class="leftNote">$</span></li>
	<li><label>Descuento:</label><input type="text" id="cheque_descuento" name="cheque_descuento[]" size="3" onblur="if(this.value==''){this.value='0';};montoTotal(this.value, 'cheque_monto')" value="0" /><span class="leftNote">$</span></li>
	<li><label>Monto:</label><input type="text" id="cheque_monto" name="cheque_monto[]" size="3" value="<?php echo $monto_pendiente?>"/><span class="leftNote">$</span></li>
<?php
	break;

	case 4:
?>
	<h3>Transferencia</h3>
	<input type="hidden" name="transferencia[]" value="1">
	<li><label>Cuenta origen:</label><select name="transferencia_cuenta_id[]">
<?php
	$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.saldo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN usuario_cuenta ON usuario_cuenta.cuenta_id=cuenta.id AND usuario_cuenta.usuario_id=$user_id ORDER BY banco.banco";
	$rsTemp = mysqli_query($conn,$sql);
	while($rs = mysqli_fetch_array($rsTemp)){?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['banco']?> <?php echo $rs['sucursal']?> <?php echo $rs['cuenta_tipo']?> <?php echo $rs['nombre']?></option>
	<?php } ?>
	</select></li>
	<li><label>Cuenta destino:</label><textarea name="transferencia_cuenta_destino[]"></textarea></li>
	<li><label>A la fecha:</label><input type="text" name="transferencia_fecha[]" class="date-pick dp-applied" value="<?php echo date("d/m/Y")?>" /></li>
	<li><label>Interes:</label><input type="text" name="transferencia_interes[]" size="3" onblur="if(this.value==''){this.value='0';};" value="0" /><span class="leftNote">$</span></li>
	<li><label>Descuento:</label><input type="text" id="transferencia_descuento" name="transferencia_descuento[]" size="3" onblur="if(this.value==''){this.value='0';};montoTotal(this.value, 'transferencia_monto')" value="0" /><span class="leftNote">$</span></li>
	<li><label>Monto:</label><input type="text" id="transferencia_monto" name="transferencia_monto[]" size="3" value="<?php echo $monto_pendiente?>"/><span class="leftNote">$</span></li>
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
	$rsTemp = mysqli_query($conn,$sql);
	while($rs = mysqli_fetch_array($rsTemp)){?>
	<option value="<?php echo $rs['id']?>"><?php echo $rs['banco']?> <?php echo $rs['sucursal']?> <?php echo $rs['cuenta_tipo']?> <?php echo  $rs['nombre']?></option>
	<?php } ?>
	</select></li>
	<li><label>Fecha:</label><input type="text" name="debito_fecha[]" class="fecha date-pick dp-applied" value="<?php echo   date("d/m/Y")?>" /></li>
	<li><label>Interes:</label><input type="text" name="debito_interes[]" size="3" onblur="if(this.value==''){this.value='0';};" value="0" /><span class="leftNote">$</span></li>
	<li><label>Descuento:</label><input type="text" id="debito_descuento" name="debito_descuento[]" size="3" onblur="if(this.value==''){this.value='0';};montoTotal(this.value, 'debito_monto')" value="0" /><span class="leftNote">$</span></li>
	<li><label>Monto:</label><input type="text" id="debito_monto" name="debito_monto[]" size="3" value="<?php echo $monto_pendiente?>"/><span class="leftNote">$</span></li>
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
	$rsTemp = mysqli_query($conn,$sql);
	while($rs = mysqli_fetch_array($rsTemp)){
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