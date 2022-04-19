<!--TARJETA-->
<table class="medios_pago">
<thead>
	<tr>
		<th width="100">Tarjeta</th>
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

$tabla 		= "tarjeta_resumen";
$sql_meses 	= sql_meses($tabla,$ano,'fecha_pago');
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total, CONCAT(tarjeta_marca.marca,' ',tarjeta.titular) as tipo FROM $tabla INNER JOIN tarjeta ON $tabla.tarjeta_id = tarjeta.id INNER JOIN tarjeta_marca ON tarjeta.tarjeta_marca_id = tarjeta_marca.id GROUP BY $tabla.tarjeta_id";

$rsTemp =  mysql_query($sql);echo mysql_error();
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
	<tr>
		<td><?=$rs['tipo']?></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="tarjeta_fecha_<?=$i?>"><?=$rs[$i]?></td>
		<? } ?>
		<td class="tarjeta_total"><?=$rs['total']?></td>
	</tr>
	<? } ?>
<? } ?>
	<tr style="background:#FFFF00;">
		<td>Total</td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="total_tarjeta_fecha_<?=$i?>"></td>
		<? } ?>
		<td class="total_tarjeta_total"></td>
	</tr>
</tbody>
</table>

<script>
$('.medios_pago').flexigrid({height:'auto',striped:false,singleSelect:true});

for (i=0; i<12; i++){
	var sum = 0;
	$(".tarjeta_fecha_"+i).each(function() {
		if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
		sum += parseFloat(this.innerHTML);
		}
	});
	$(".total_tarjeta_fecha_"+i).html(sum);
}

var sum = 0;
$(".tarjeta_total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_tarjeta_total").html(sum);

</script>
