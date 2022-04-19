<li><label>Forma de pago:</label></li>

<!--CHEQUE-->
<?php
$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cheque_consumo.* FROM cheque_consumo INNER JOIN cuenta ON cheque_consumo.cuenta_id=cuenta.id INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN rel_pago_operacion ON cheque_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago='cheque' WHERE rel_pago_operacion.operacion_tipo='$operacion_tipo' AND rel_pago_operacion.operacion_id=$operacion_id";

$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
	$numero = str_pad($rs['numero'], 8,'0',STR_PAD_LEFT);
	$sql = "SELECT chequeras.numero FROM chequeras INNER JOIN chequera_cheques ON chequeras.id=chequera_cheques.chequera_id WHERE chequeras.cuenta_id='".$rs['cuenta_id']."' AND chequera_cheques.numero='".$numero."'";
	//echo $sql;
	$rsTempChequera = mysql_query($sql);
	if(mysql_num_rows($rsTempChequera)>0){
		if($rsChequera = mysql_fetch_array($rsTempChequera)){
			$numero = $rsChequera['numero'].' - '.$numero;
		}
	}
?>
<li><h3>Cheque</h3></li>
<li><label>Cuenta:</label><?php echo $rs['banco']?> <?php echo $rs['cuenta_tipo']?></li>
<li><label>Numero:</label><?php echo $numero;?></li>
<li><label>Titular:</label><?php echo $rs['titular']?></li>
<!--  <li><label>A la fecha:</label><input class="date-pick dp-applied" name="fecha_cheque[]" value="<?php echo fechavista($rs['fecha'])?>" /><input type="hidden" name="fecha_cheque_id[]" value="<?php echo ($rs['id'])?>" /></li>-->
<li><label>A la fecha:</label><?=fechavista($rs['fecha'])?></li>
<li><label>Monto:</label>$<?php echo $rs['monto']?></li>
<li><label>Interes:</label>$<?php echo $rs['interes']?></li>
<li><label>Descuento:</label>$<?php echo $rs['descuento']?></li>
<?php  }} ?>

<!--TRANSFERENCIA-->
<?php 
$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,transferencia_consumo.* FROM transferencia_consumo INNER JOIN cuenta ON transferencia_consumo.cuenta_id=cuenta.id INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN rel_pago_operacion ON transferencia_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago='transferencia' WHERE rel_pago_operacion.operacion_tipo='$operacion_tipo' AND rel_pago_operacion.operacion_id=$operacion_id";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<li><h3>Transferencia</h3></li>
<li><label>Cuenta origen:</label><?php echo $rs['banco']?> <?php echo $rs['cuenta_tipo']?></li>
<li><label>Cuenta destino:</label><?php echo $rs['cuenta_destino']?></li>
<!--  <li><label>A la fecha:</label><input class="date-pick dp-applied" name="fecha_transferencia[]" value="<?php echo fechavista($rs['fecha'])?>" /><input type="hidden" name="fecha_transferencia_id[]" value="<?php echo ($rs['id'])?>" /></li>-->
<li><label>A la fecha:</label><?=fechavista($rs['fecha'])?></li>
<li><label>Monto:</label>$<?php echo $rs['monto']?></li>
<li><label>Interes:</label>$<?php echo $rs['interes']?></li>
<li><label>Descuento:</label>$<?php echo $rs['descuento']?></li>
<?php  }} ?>

<!--CHEQUE DE TERCERO-->
<?php 
$sql = "SELECT cobro_cheques.* FROM cobro_cheques INNER JOIN rel_pago_operacion ON cobro_cheques.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago='cheque_tercero' WHERE rel_pago_operacion.operacion_tipo='$operacion_tipo' AND rel_pago_operacion.operacion_id=$operacion_id";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<li><h3>Cheque de tercero</h3><li>
<li><label>Banco:</label><?php echo $rs['banco']?></li>
<li><label>Numero:</label><?php echo $rs['numero']?></li>
<li><label>Monto:</label>$<?php echo $rs['monto_neto'] + $rs['interes']?></li>
<?php  }} ?>

<!--DEBITO-->
<?php 
$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta_movimiento.* FROM cuenta_movimiento INNER JOIN cuenta ON cuenta_movimiento.cuenta_id=cuenta.id INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN rel_pago_operacion ON cuenta_movimiento.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago='debito' WHERE rel_pago_operacion.operacion_tipo='$operacion_tipo' AND rel_pago_operacion.operacion_id=$operacion_id";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<li><h3>Debito</h3></li>
<li><label>Cuenta:</label><?php echo $rs['banco']?> <?php echo $rs['cuenta_tipo']?></li>
<li><label>A la fecha:</label><input class="date-pick dp-applied" name="fecha_debito[]" value="<?php echo fechavista($rs['fecha'])?>" /><input type="hidden" name="fecha_debito_id[]" value="<?php echo ($rs['id'])?>" /></li>
<li><label>Monto:</label>$<?php echo $rs['monto']?></li>
<li><label>Interes:</label>$<?php echo $rs['interes']?></li>
<li><label>Descuento:</label>$<?php echo $rs['descuento']?></li>
<?php  }} ?>

<!--EFECIVO-->
<?php 
$sql = "SELECT caja.caja,efectivo_consumo.* FROM efectivo_consumo INNER JOIN caja ON efectivo_consumo.caja_id = caja.id INNER JOIN rel_pago_operacion ON efectivo_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago='efectivo' WHERE rel_pago_operacion.operacion_tipo='$operacion_tipo' AND rel_pago_operacion.operacion_id=$operacion_id";
//echo $sql;
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<li><h3>Efectivo</h3></li>
<li><label>Caja origen:</label><?php echo $rs['caja']?></li>
<!--  <li><label>A la fecha:</label><?php echo fechavista($rs['fecha'])?></li>-->
<li><label>A la fecha:</label><input class="date-pick dp-applied" name="fecha_efectivo[]" value="<?php echo fechavista($rs['fecha'])?>" /><input type="hidden" name="fecha_efectivo_id[]" value="<?php echo ($rs['id'])?>" /></li>
<li><label>Monto:</label>$<?php echo $rs['monto']?></li>
<li><label>Interes:</label>$<?php echo $rs['interes']?></li>
<li><label>Descuento:</label>$<?php echo $rs['descuento']?></li>
<?php  }} ?>

<!--TARJETA-->
<?php 
$sql = "SELECT banco.banco,tarjeta_marca.marca,tarjeta.titular,tarjeta_consumo.* FROM tarjeta_consumo INNER JOIN tarjeta ON tarjeta_consumo.tarjeta_id=tarjeta.id INNER JOIN tarjeta_marca ON tarjeta.tarjeta_marca_id=tarjeta_marca.id INNER JOIN banco ON tarjeta.banco_id=banco.id INNER JOIN rel_pago_operacion ON tarjeta_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago='tarjeta' WHERE rel_pago_operacion.operacion_tipo='$operacion_tipo' AND rel_pago_operacion.operacion_id=$operacion_id";
//echo $sql;
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<li><h3>Tarjeta</h3></li>
<li><label>Tarjeta:</label><?php echo $rs['banco']?> <?php echo $rs['marca']?> <?php echo $rs['titular']?></li>
<!--  <li><label>A la fecha:</label><input class="date-pick dp-applied" name="fecha_tarjeta[]" value="<?php echo fechavista($rs['fecha'])?>" /><input type="hidden" name="fecha_tarjeta_id[]" value="<?php echo ($rs['id'])?>" /></li>-->
<li><label>Comprobante:</label><?php echo $rs['comprobante_nro']?></li>
<li><label>Monto:</label>$<?php echo $rs['monto']?></li>
<li><label>Interes:</label>$<?php echo $rs['interes']?></li>
<li><label>Descuento:</label>$<?php echo $rs['descuento']?></li>
<li><label>Cuotas:</label><?php echo $rs['cuotas']?></li>

<?php  }} ?>

<!--CUENTAS A PAGAR-->
<?php 
$sql = "SELECT * FROM cuenta_a_pagar WHERE operacion_tipo='$operacion_tipo' AND operacion_id=$operacion_id AND estado=0";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<li><h3>Cuentas a pagar</h3></li>
<li><label>Monto:</label>$<?php echo $rs['monto']?></li>
<?php  }} ?>
