<!--TRANSFERENCIAS-->
<table class="medios_pago">
<thead>
	<tr>
		<th width="100">Transferencias</th>
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
		<th width="50"><?=$ano?></th>
		<th width="50">Total</th>
	</tr>
</thead>
<tbody>
<?

$tabla 		= "transferencia_consumo";
$sql_meses 	= sql_meses($tabla,$ano);
$sql 		= "SELECT $sql_meses,ROUND(SUM(IF(YEAR($tabla.fecha)=$ano,$tabla.monto,0)),2) as 'anual',ROUND(sum($tabla.monto),2) as total, concat(banco.banco,' ',cuenta.sucursal,' ',cuenta.nombre) as tipo FROM $tabla INNER JOIN cuenta ON $tabla.cuenta_id = cuenta.id INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id WHERE $tabla.debitado = 1 GROUP BY $tabla.cuenta_id";

$rsTemp =  mysqli_query($conn,$sql);echo mysql_error();
while($rs = mysqli_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
	<tr>
		<td><?=$rs['tipo']?></td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="transferencia_fecha_<?=$i?>"><?=$rs[$i]?></td>
		<? } ?>
		<td class="transferencia_total_anual"><?=$rs['anual']?></td>
		<td class="transferencia_total"><?=$rs['total']?></td>
	</tr>
	<? } ?>
<? } ?>
	<tr style="background:#FFFF00;">
		<td>Total</td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="total_transferencia_fecha_<?=$i?>"></td>
		<? } ?>
		<td class="total_transferencia_anual"></td>
		<td class="total_transferencia_total"></td>
	</tr>
</tbody>
</table>

<script>
$('.medios_pago').flexigrid({height:'auto',striped:false,singleSelect:true});

for (i=0; i<12; i++){
	var sum = 0;
	$(".transferencia_fecha_"+i).each(function() {
		if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
		sum += parseFloat(this.innerHTML);
		}
	});
	$(".total_transferencia_fecha_"+i).html(sum);
}

var sum = 0;
$(".transferencia_total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_transferencia_total").html(sum);

var sum = 0;
$(".transferencia_total_anual").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_transferencia_anual").html(sum);
</script>
