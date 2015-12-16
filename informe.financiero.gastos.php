<table class="gastos_pago">
<thead>
	<tr>
		<th width="100">Medio de pago</th>
		<th width="50">Enero</th>
		<th width="50">Febrero</th>
		<th width="50">Marzo</th>
		<th width="50">Abril</th>
		<th width="50">Mayo</th>
		<th width="50">Junio</th>
		<th width="50">Julio</th>
		<th width="50">Agosto</th>
		<th width="50">Septiembre</th>
		<th width="50">Octubre</th>
		<th width="50">Noviembre</th>
		<th width="50">Diciembre</th>
		<th width="50">Total</th>
	</tr>
</thead>
<tbody>
<?
//listamos los gastos con tarjeta
$tabla 		= "tarjeta_consumo_cuota";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total FROM $tabla INNER JOIN tarjeta_consumo ON $tabla.tarjeta_consumo_id=tarjeta_consumo.id INNER JOIN gasto ON tarjeta_consumo.operacion_id=gasto.id WHERE tarjeta_consumo.operacion_tipo='gasto' AND gasto.estado=1 AND YEAR($tabla.fecha)=$ano";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
	<tr>
		<td><a style="cursor:pointer;" onclick="$('.tarjeta_por_rubro').toggle();">Tarjeta</a></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="fecha_<?=$i?>"><?=$rs[$i]?></td>
		<? } ?>
		<td class="total"><?=$rs['total']?></td>
	</tr>
	<? } ?>
<? } ?>

<?
//listamos los gastos con tarjeta agrupado por rubro
$tabla 		= "tarjeta_consumo_cuota";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total,rubro.rubro FROM $tabla INNER JOIN tarjeta_consumo ON $tabla.tarjeta_consumo_id=tarjeta_consumo.id INNER JOIN gasto ON tarjeta_consumo.operacion_id=gasto.id INNER JOIN rubro ON gasto.rubro_id=rubro.id WHERE tarjeta_consumo.operacion_tipo='gasto' AND gasto.estado=1 AND YEAR($tabla.fecha)=$ano GROUP BY gasto.rubro_id";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
?>
	<tr class="tarjeta_por_rubro" style="display:none;">
		<td><?=$rs['rubro']?></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td><?=$rs[$i]?></td>
		<? } ?>
		<td><?=$rs['total']?></td>
	</tr>
<? } ?>


<?
//listamos los gastos con cheque
$tabla 		= "cheque_consumo";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total FROM $tabla INNER JOIN gasto ON $tabla.operacion_id=gasto.id WHERE $tabla.operacion_tipo='gasto' AND gasto.estado=1 AND YEAR($tabla.fecha)=$ano";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
	<tr>
		<td><a style="cursor:pointer;"  onclick="$('.cheque_por_rubro').toggle();">Cheque</a></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="fecha_<?=$i?>"><?=$rs[$i]?></td>
		<? } ?>
		<td class="total"><?=$rs['total']?></td>
	</tr>
	<? } ?>
<? } ?>

<?
//listamos los gastos con cheque agrupado por rubro
$tabla 		= "cheque_consumo";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total,rubro.rubro FROM $tabla INNER JOIN gasto ON $tabla.operacion_id=gasto.id INNER JOIN rubro ON gasto.rubro_id=rubro.id WHERE $tabla.operacion_tipo='gasto' AND gasto.estado=1 AND YEAR($tabla.fecha)=$ano GROUP BY gasto.rubro_id";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
?>
	<tr class="cheque_por_rubro" style="display:none;">
		<td><?=$rs['rubro']?></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td><?=$rs[$i]?></td>
		<? } ?>
		<td><?=$rs['total']?></td>
	</tr>
<? } ?>

<?
//listamos los gastos con transferencia
$tabla 		= "transferencia_consumo";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total FROM $tabla INNER JOIN gasto ON $tabla.operacion_id=gasto.id WHERE $tabla.operacion_tipo='gasto' AND gasto.estado=1 AND YEAR($tabla.fecha)=$ano";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL) { ?>
	<tr>
		<td><a style="cursor:pointer;"  onclick="$('.transferencia_por_rubro').toggle();">Transferencia</a></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="fecha_<?=$i?>"><?=$rs[$i]?></td>
		<? } ?>
		<td class="total"><?=$rs['total']?></td>
	</tr>
	<? } ?>
<? } ?>

<?
//listamos los gastos con transferencia agrupado por rubro
$tabla 		= "transferencia_consumo";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total,rubro.rubro FROM $tabla INNER JOIN gasto ON $tabla.operacion_id=gasto.id INNER JOIN rubro ON gasto.rubro_id=rubro.id WHERE $tabla.operacion_tipo='gasto' AND gasto.estado=1 AND YEAR($tabla.fecha)=$ano GROUP BY gasto.rubro_id";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
?>
	<tr class="transferencia_por_rubro" style="display:none;">
		<td><?=$rs['rubro']?></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td><?=$rs[$i]?></td>
		<? } ?>
		<td><?=$rs['total']?></td>
	</tr>
<? } ?>

<?
//listamos los gastos con efectivo
$tabla 		= "efectivo_consumo";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total FROM $tabla INNER JOIN gasto ON $tabla.operacion_id=gasto.id WHERE $tabla.operacion_tipo='gasto' AND gasto.estado=1 AND YEAR($tabla.fecha)=$ano";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL) { ?>
	<tr>
		<td><a style="cursor:pointer;"  onclick="$('.efectivo_por_rubro').toggle();">Efectivo</a></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="fecha_<?=$i?>"><?=$rs[$i]?></td>
		<? } ?>
		<td class="total"><?=$rs['total']?></td>
	</tr>
	<? } ?>
<? } ?>

<?
//listamos los gastos con efectivo agrupado por rubro
$tabla 		= "efectivo_consumo";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total,rubro.rubro FROM $tabla INNER JOIN gasto ON $tabla.operacion_id=gasto.id INNER JOIN rubro ON gasto.rubro_id=rubro.id WHERE $tabla.operacion_tipo='gasto' AND gasto.estado=1 AND YEAR($tabla.fecha)=$ano GROUP BY gasto.rubro_id";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
?>
	<tr class="efectivo_por_rubro" style="display:none;">
		<td><?=$rs['rubro']?></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td><?=$rs[$i]?></td>
		<? } ?>
		<td><?=$rs['total']?></td>
	</tr>
<? } ?>

	<tr style="background:#FFFF00;">
		<td>Total</td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="total_fecha_<?=$i?>"></td>
		<? } ?>
		<td class="total_total"></td>
	</tr>
</tbody>
</table>
<br />
<br />


<script>
$('.gastos_pago').flexigrid({height:'auto',striped:false,singleSelect:true});
$('.gastos_rubros').flexigrid({height:'auto',striped:false,singleSelect:true});
</script>
<div id="total"></div>
<script>
for (i=0; i<12; i++){
	var sum = 0;
	$(".fecha_"+i).each(function() {
		if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
		sum += parseFloat(this.innerHTML);
		}
	});
	$(".total_fecha_"+i).html(sum);
}

var sum = 0;
$(".total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_total").html(sum);

for (i=0; i<12; i++){
	var sum = 0;
	$(".rubro_fecha_"+i).each(function() {
		if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
		sum += parseFloat(this.innerHTML);
		}
	});
	$(".rubro_total_fecha_"+i).html(sum);
}

var sum = 0;
$(".rubro_total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".rubro_total_total").html(sum);
</script>