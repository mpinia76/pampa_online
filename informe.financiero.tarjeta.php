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
		<th width="50"><?php echo $ano?></th>
		<th width="50">Total</th>
	</tr>
</thead>
<tbody>
<?php
$sql1 = "SELECT id FROM tarjeta";
$rsTemp1 = mysql_query($sql1);
while($row = mysql_fetch_array($rsTemp1)){
	$tarjeta_id = $row['id'];
	$resumen = '';

	for($i = 1; $i<= 12; $i++){
		$resumen[$i] = "0 as '$i'";
	}

	$sql = "SELECT * FROM tarjeta_resumen WHERE tarjeta_id = $tarjeta_id AND ano = $ano";
	$rsTemp = mysql_query($sql); echo mysql_error();
	while($rs = mysql_fetch_array($rsTemp)){

		$inicio = $rs['inicio'];
		$fin 	= $rs['fin'];
		$mes 	= $rs['mes'];
		$tabla 	= "tarjeta_consumo_cuota";
		
		$sql = "SELECT SUM(monto) as monto FROM tarjeta_movimiento WHERE tarjeta_resumen_id=".$rs['id'];
		$rowTemp = mysql_query($sql); //echo $sql;
		$row = mysql_fetch_array($rowTemp);
		$row['monto'] ? $total = $row['monto'] : $total = 0;
	
		$resumen[$mes] = "SUM(IF($tabla.fecha >= '$inicio' AND $tabla.fecha <= '$fin',$tabla.monto,0)) + $total as '$mes'";
		
		if($mes == 1){ $anual_inicio = $inicio; }
		if($mes == 12){ $anual_fin = $fin; }
	}


	if($anual_inicio == ''){ $anual_inicio = "$ano-01-01"; }
	if($anual_fin == ''){ $anual_fin = "$ano-12-31"; }

	$resumen = implode(",",$resumen);

	$tabla 		= "tarjeta_consumo_cuota";
	$sql 		= "SELECT $resumen,ROUND(SUM(IF($tabla.fecha >= '$anual_inicio' AND $tabla.fecha <= '$anual_fin',$tabla.monto,0)),2) as 'anual',ROUND(sum($tabla.monto),2) as total, CONCAT(tarjeta_marca.marca,' ',tarjeta.titular) as tipo FROM $tabla INNER JOIN tarjeta_consumo ON $tabla.tarjeta_consumo_id=tarjeta_consumo.id INNER JOIN tarjeta ON tarjeta_consumo.tarjeta_id = tarjeta.id AND tarjeta.id = $tarjeta_id INNER JOIN tarjeta_marca ON tarjeta.tarjeta_marca_id = tarjeta_marca.id GROUP BY tarjeta.id";

	$rsTemp =  mysql_query($sql);echo mysql_error();
	while($rs = mysql_fetch_array($rsTemp)){
		if($rs['total'] != NULL){ ?>
		<tr>
			<td><?php echo $rs['tipo']?></td>
			<?php for($i=0;$i<12;$i++){ ?>
			<td class="tarjeta_fecha_<?php echo $i?>"><?php echo round($rs[$i],2)?></td>
			<?php } ?>
			<td class="tarjeta_total_anual"><?php echo round($rs['anual'],2)?></td>
			<td class="tarjeta_total"><?php echo round($rs['total'],2)?></td>
		</tr>
		<?php } ?>
	<?php } ?>
<?php } ?>

	<tr style="background:#FFFF00;">
		<td>Total</td>
		<?php for($i=0;$i<12;$i++){ ?>
		<td class="total_tarjeta_fecha_<?php echo $i?>"></td>
		<?php } ?>
		<td class="total_tarjeta_anual"></td>
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
	$(".total_tarjeta_fecha_"+i).html(roundVal(sum));
}

var sum = 0;
$(".tarjeta_total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_tarjeta_total").html(roundVal(sum));
var tarjeta = sum;
var sum = 0;
$(".tarjeta_total_anual").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_tarjeta_anual").html(roundVal(sum));



</script>
