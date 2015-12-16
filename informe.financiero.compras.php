<table class="compras_pago">
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
//listamos los compras con tarjeta
$tabla 		= "tarjeta_consumo_cuota";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total FROM $tabla INNER JOIN tarjeta_consumo ON $tabla.tarjeta_consumo_id=tarjeta_consumo.id INNER JOIN compra ON tarjeta_consumo.operacion_id=compra.id WHERE tarjeta_consumo.operacion_tipo='compra' AND compra.estado=1 AND YEAR($tabla.fecha)=$ano";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
	<tr>
		<td><a style="cursor:pointer;" onclick="$('.compra_tarjeta_por_rubro').toggle();">Tarjeta</a></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="compra_fecha_<?=$i?>"><?=$rs[$i]?></td>
		<? } ?>
		<td class="compra_total"><?=$rs['total']?></td>
	</tr>
	<? } ?>
<? } ?>

<?
//listamos los compras con tarjeta agrupado por rubro
$tabla 		= "tarjeta_consumo_cuota";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total,rubro.rubro FROM $tabla INNER JOIN tarjeta_consumo ON $tabla.tarjeta_consumo_id=tarjeta_consumo.id INNER JOIN compra ON tarjeta_consumo.operacion_id=compra.id INNER JOIN rubro ON compra.rubro_id=rubro.id WHERE tarjeta_consumo.operacion_tipo='compra' AND compra.estado=1 AND YEAR($tabla.fecha)=$ano GROUP BY compra.rubro_id";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
?>
	<tr class="compra_tarjeta_por_rubro" style="display:none;">
		<td><?=$rs['rubro']?></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td><?=$rs[$i]?></td>
		<? } ?>
		<td><?=$rs['total']?></td>
	</tr>
<? } ?>


<?
//listamos los compras con cheque
$tabla 		= "cheque_consumo";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total FROM $tabla INNER JOIN compra ON $tabla.operacion_id=compra.id WHERE $tabla.operacion_tipo='compra' AND compra.estado=1 AND YEAR($tabla.fecha)=$ano";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
	<tr>
		<td><a style="cursor:pointer;"  onclick="$('.compra_cheque_por_rubro').toggle();">Cheque</a></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="compra_fecha_<?=$i?>"><?=$rs[$i]?></td>
		<? } ?>
		<td class="compra_total"><?=$rs['total']?></td>
	</tr>
	<? } ?>
<? } ?>

<?
//listamos los compras con cheque agrupado por rubro
$tabla 		= "cheque_consumo";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total,rubro.rubro FROM $tabla INNER JOIN compra ON $tabla.operacion_id=compra.id INNER JOIN rubro ON compra.rubro_id=rubro.id WHERE $tabla.operacion_tipo='compra' AND compra.estado=1 AND YEAR($tabla.fecha)=$ano GROUP BY compra.rubro_id";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
?>
	<tr class="compra_cheque_por_rubro" style="display:none;">
		<td><?=$rs['rubro']?></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td><?=$rs[$i]?></td>
		<? } ?>
		<td><?=$rs['total']?></td>
	</tr>
<? } ?>

<?
//listamos los compras con transferencia
$tabla 		= "transferencia_consumo";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total FROM $tabla INNER JOIN compra ON $tabla.operacion_id=compra.id WHERE $tabla.operacion_tipo='compra' AND compra.estado=1 AND YEAR($tabla.fecha)=$ano";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL) { ?>
	<tr>
		<td><a style="cursor:pointer;"  onclick="$('.compra_transferencia_por_rubro').toggle();">Transferencia</a></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="compra_fecha_<?=$i?>"><?=$rs[$i]?></td>
		<? } ?>
		<td class="compra_total"><?=$rs['total']?></td>
	</tr>
	<? } ?>
<? } ?>

<?
//listamos los compras con transferencia agrupado por rubro
$tabla 		= "transferencia_consumo";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total,rubro.rubro FROM $tabla INNER JOIN compra ON $tabla.operacion_id=compra.id INNER JOIN rubro ON compra.rubro_id=rubro.id WHERE $tabla.operacion_tipo='compra' AND compra.estado=1 AND YEAR($tabla.fecha)=$ano GROUP BY compra.rubro_id";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
?>
	<tr class="compra_transferencia_por_rubro" style="display:none;">
		<td><?=$rs['rubro']?></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td><?=$rs[$i]?></td>
		<? } ?>
		<td><?=$rs['total']?></td>
	</tr>
<? } ?>

<?
//listamos los compras con efectivo
$tabla 		= "efectivo_consumo";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total FROM $tabla INNER JOIN compra ON $tabla.operacion_id=compra.id WHERE $tabla.operacion_tipo='compra' AND compra.estado=1 AND YEAR($tabla.fecha)=$ano";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL) { ?>
	<tr>
		<td><a style="cursor:pointer;"  onclick="$('.compra_efectivo_por_rubro').toggle();">Efectivo</a></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="compra_fecha_<?=$i?>"><?=$rs[$i]?></td>
		<? } ?>
		<td class="compra_total"><?=$rs['total']?></td>
	</tr>
	<? } ?>
<? } ?>

<?
//listamos los compras con efectivo agrupado por rubro
$tabla 		= "efectivo_consumo";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total,rubro.rubro FROM $tabla INNER JOIN compra ON $tabla.operacion_id=compra.id INNER JOIN rubro ON compra.rubro_id=rubro.id WHERE $tabla.operacion_tipo='compra' AND compra.estado=1 AND YEAR($tabla.fecha)=$ano GROUP BY compra.rubro_id";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
?>
	<tr class="compra_efectivo_por_rubro" style="display:none;">
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
		<td class="compra_total_fecha_<?=$i?>"></td>
		<? } ?>
		<td class="compra_total_total"></td>
	</tr>
</tbody>
</table>
<br />
<br />

<!--<table class="compras_rubros" style="display:none;">
<thead>
	<tr>
		<th width="100">Rubros</th>
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
//listamos los compras por rubro
$tabla 		= "compra";
$sql_meses 	= sql_meses($tabla);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total,rubro.rubro,rubro.id as rubro_id FROM $tabla INNER JOIN rubro ON $tabla.rubro_id=rubro.id WHERE compra.estado=1 AND YEAR($tabla.fecha)=$ano GROUP BY $tabla.rubro_id";

$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
	<tr>
		<td><a onclick="$('.compra_rubro_<?=$rs['rubro_id']?>').toggle();"><?=$rs['rubro']?></a></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="compra_rubro_fecha_<?=$i?>"><?=$rs[$i]?></td>
		<? } ?>
		<td class="compra_rubro_total"><?=$rs['total']?></td>
	</tr>
	<? } ?>

<?
	$tabla 		= "compra";
	$sql_meses 	= sql_meses($tabla);
	$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total,subrubro.subrubro FROM $tabla INNER JOIN subrubro ON $tabla.subrubro_id=subrubro.id WHERE compra.estado=1 AND subrubro.rubro_id=".$rs['rubro_id']." AND YEAR($tabla.fecha)=$ano GROUP BY $tabla.subrubro_id";
	
	$rs2Temp = mysql_query($sql);
	while($rs2 = mysql_fetch_array($rs2Temp)){
		if($rs2['total'] != NULL){ ?>

		<tr class="compra_rubro_<?=$rs['rubro_id']?>" style="display:none;">
			<td><?=$rs2['subrubro']?></td>
			<? for($i=0;$i<12;$i++){ ?>
			<td><?=$rs2[$i]?></td>
			<? } ?>
			<td><?=$rs2['total']?></td>
		</tr>
		<? } ?>
	<? } ?>
<? } ?>

	<tr style="background:#FFFF00;">
		<td>Total</td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="compra_rubro_total_fecha_<?=$i?>"></td>
		<? } ?>
		<td class="compra_rubro_total_total"></td>
	</tr>
</tbody>
</table>
-->
<script>
$('.compras_pago').flexigrid({height:'auto',striped:false,singleSelect:true});
$('.compras_rubros').flexigrid({height:'auto',striped:false,singleSelect:true});
</script>
<div id="total"></div>
<script>
for (i=0; i<12; i++){
	var sum = 0;
	$(".compra_fecha_"+i).each(function() {
		if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
		sum += parseFloat(this.innerHTML);
		}
	});
	$(".compra_total_fecha_"+i).html(sum);
}

var sum = 0;
$(".compra_total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".compra_total_total").html(sum);

for (i=0; i<12; i++){
	var sum = 0;
	$(".compra_rubro_fecha_"+i).each(function() {
		if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
		sum += parseFloat(this.innerHTML);
		}
	});
	$(".compra_rubro_total_fecha_"+i).html(sum);
}

var sum = 0;
$(".compra_rubro_total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".compra_rubro_total_total").html(sum);
</script>