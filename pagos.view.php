<li><label>Forma de pago:</label></li>

<!--CHEQUE-->
<?
$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cheque_consumo.* FROM cheque_consumo INNER JOIN cuenta ON cheque_consumo.cuenta_id=cuenta.id INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN rel_pago_operacion ON cheque_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago='cheque' WHERE rel_pago_operacion.operacion_tipo='$operacion_tipo' AND rel_pago_operacion.operacion_id=$operacion_id";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<li></li>
<h3>Cheque</h3>
<li><label>Cuenta:</label><?=$rs['banco']?> <?=$rs['cuenta_tipo']?></li>
<li><label>Numero:</label><?=$rs['numero']?></li>
<li><label>Titular:</label><?=$rs['titular']?></li>
<li><label>A la fecha:</label><?=fechavista($rs['fecha'])?></li>
<li><label>Monto:</label>$<?=$rs['monto']?></li>
<li><label>Interes:</label>$<?=$rs['interes']?></li>
<li><label>Descuento:</label>$<?=$rs['descuento']?></li>
<? }} ?>

<!--TRANSFERENCIA-->
<?
$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,transferencia_consumo.* FROM transferencia_consumo INNER JOIN cuenta ON transferencia_consumo.cuenta_id=cuenta.id INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN rel_pago_operacion ON transferencia_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago='transferencia' WHERE rel_pago_operacion.operacion_tipo='$operacion_tipo' AND rel_pago_operacion.operacion_id=$operacion_id";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<li></li>
<h3>Transferencia</h3>
<li><label>Cuenta origen:</label><?=$rs['banco']?> <?=$rs['cuenta_tipo']?></li>
<li><label>Cuenta destino:</label><?=$rs['cuenta_destino']?></li>
<li><label>A la fecha:</label><?=fechavista($rs['fecha'])?></li>
<li><label>Monto:</label>$<?=$rs['monto']?></li>
<li><label>Interes:</label>$<?=$rs['interes']?></li>
<li><label>Descuento:</label>$<?=$rs['descuento']?></li>
<? }} ?>

<!--CHEQUE DE TERCERO-->
<?
$sql = "SELECT cobro_cheques.* FROM cobro_cheques INNER JOIN rel_pago_operacion ON cobro_cheques.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago='cheque_tercero' WHERE rel_pago_operacion.operacion_tipo='$operacion_tipo' AND rel_pago_operacion.operacion_id=$operacion_id";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<h3>Cheque de tercero</h3>
<li><label>Banco:</label><?=$rs['banco']?></li>
<li><label>Numero:</label><?=$rs['numero']?></li>
<li><label>Monto:</label>$<?=$rs['monto_neto'] + $rs['interes']?></li>
<? }} ?>

<!--DEBITO-->
<?
$sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta_movimiento.* FROM cuenta_movimiento INNER JOIN cuenta ON cuenta_movimiento.cuenta_id=cuenta.id INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id INNER JOIN rel_pago_operacion ON cuenta_movimiento.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago='debito' WHERE rel_pago_operacion.operacion_tipo='$operacion_tipo' AND rel_pago_operacion.operacion_id=$operacion_id";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<li></li>
<h3>Debtio</h3>
<li><label>Cuenta:</label><?=$rs['banco']?> <?=$rs['cuenta_tipo']?></li>
<li><label>Monto:</label>$<?=$rs['monto']?></li>
<li><label>Interes:</label>$<?=$rs['interes']?></li>
<li><label>Descuento:</label>$<?=$rs['descuento']?></li>
<? }} ?>

<!--EFECIVO-->
<?
$sql = "SELECT caja.caja,efectivo_consumo.* FROM efectivo_consumo INNER JOIN caja ON efectivo_consumo.caja_id = caja.id INNER JOIN rel_pago_operacion ON efectivo_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago='efectivo' WHERE rel_pago_operacion.operacion_tipo='$operacion_tipo' AND rel_pago_operacion.operacion_id=$operacion_id";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<li></li>
<h3>Efectivo</h3>
<li><label>Caja origen:</label><?=$rs['caja']?></li>
<li><label>A la fecha:</label><?=fechavista($rs['fecha'])?></li>
<li><label>Monto:</label>$<?=$rs['monto']?></li>
<li><label>Interes:</label>$<?=$rs['interes']?></li>
<li><label>Descuento:</label>$<?=$rs['descuento']?></li>
<? }} ?>

<!--TARJETA-->
<?
$sql = "SELECT banco.banco,tarjeta_marca.marca,tarjeta.titular,tarjeta_consumo.* FROM tarjeta_consumo INNER JOIN tarjeta ON tarjeta_consumo.tarjeta_id=tarjeta.id INNER JOIN tarjeta_marca ON tarjeta.tarjeta_marca_id=tarjeta_marca.id INNER JOIN banco ON tarjeta.banco_id=banco.id INNER JOIN rel_pago_operacion ON tarjeta_consumo.id=rel_pago_operacion.forma_pago_id AND rel_pago_operacion.forma_pago='tarjeta' WHERE rel_pago_operacion.operacion_tipo='$operacion_tipo' AND rel_pago_operacion.operacion_id=$operacion_id";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<li></li>
<h3>Tarjeta</h3>
<li><label>Tarjeta:</label><?=$rs['banco']?> <?=$rs['marca']?> <?=$rs['titular']?></li>
<li><label>Comprobante:</label><?=$rs['comprobante_nro']?></li>
<li><label>Monto:</label>$<?=$rs['monto']?></li>
<li><label>Interes:</label>$<?=$rs['interes']?></li>
<li><label>Descuento:</label>$<?=$rs['descuento']?></li>
<li><label>Cuotas:</label><?=$rs['cuotas']?></li>

<? }} ?>

<!--CUENTAS A PAGAR-->
<?
$sql = "SELECT * FROM cuenta_a_pagar WHERE operacion_tipo='$operacion_tipo' AND operacion_id=$operacion_id AND estado=0";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp)>0){
while($rs = mysql_fetch_array($rsTemp)){
?>
<li></li>
<h3>Cuentas a pagar</h3>
<li><label>Monto:</label>$<?=$rs['monto']?></li>
<? }} ?>
