<!--EFECTIVO-->
<table class="medios_pago">
<thead>
	<tr>
		<th width="100">Efectivo</th>
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
		<th width="50"><?php echo $ano?></th>
		<th width="50">Total</th>
	</tr>
</thead>
<tbody>
<?php

$tabla 		= "efectivo_consumo";
$sql_meses 	= sql_meses($tabla,$ano);
$sql 		= "SELECT $sql_meses,ROUND(SUM(IF(YEAR($tabla.fecha)=$ano,$tabla.monto,0)),2) as 'anual',ROUND(sum($tabla.monto),2) as total, caja.caja as tipo FROM $tabla INNER JOIN caja ON $tabla.caja_id = caja.id GROUP BY $tabla.caja_id";

$rsTemp =  mysqli_query($conn,$sql);echo mysql_error();
while($rs = mysqli_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
	<tr>
		<td><?php echo $rs['tipo']?></td>
		<?php for($i=0;$i<12;$i++){ ?>
		<td class="caja_fecha_<?php echo $i?>"><?php echo $rs[$i]?></td>
		<?php } ?>
		<td class="caja_total_anual"><?php echo $rs['anual']?></td>
		<td class="caja_total"><?php echo $rs['total']?></td>
	</tr>
	<?php } ?>
<?php } ?>
	<tr style="background:#FFFF00;">
		<td>Total</td>
		<?php for($i=0;$i<12;$i++){ ?>
		<td class="total_caja_fecha_<?php echo $i?>"></td>
		<?php } ?>
		<td class="total_caja_anual"></td>
		<td class="total_caja_total"></td>
	</tr>
</tbody>
</table>

<script>
$('.medios_pago').flexigrid({height:'auto',striped:false,singleSelect:true});

for (i=0; i<12; i++){
	var sum = 0;
	$(".caja_fecha_"+i).each(function() {
		if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
		sum += parseFloat(this.innerHTML);
		}
	});
	$(".total_caja_fecha_"+i).html(sum);
}

var sum = 0;
$(".caja_total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_caja_total").html(sum);
var efectivo = sum;
var sum = 0;
$(".caja_total_anual").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_caja_anual").html(sum);
</script>
