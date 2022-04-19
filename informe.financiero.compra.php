<!--COMPRAS-->
<table class="medios_pago">
<thead>
	<tr>
		<th width="100">Compra</th>
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

$tabla 		= "compra";
$sql_meses 	= sql_meses($tabla,$ano);
$sql 		= "SELECT $sql_meses,ROUND(SUM(IF(YEAR($tabla.fecha)=$ano,$tabla.monto,0)),2) as 'anual',ROUND(sum($tabla.monto),2) as total, rubro.rubro as tipo, rubro.id as rubro_id FROM $tabla INNER JOIN rubro ON $tabla.rubro_id = rubro.id GROUP BY $tabla.rubro_id";

$rsTemp =  mysql_query($sql);echo mysql_error();
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
		<tr>
			<td><a style="color:blue;" onclick="$('.<?=$tabla?>_rubro_<?=$rs['rubro_id']?>').toggle();"><?=$rs['tipo']?></a></td>
			<? for($i=0;$i<12;$i++){ ?>
			<td class="compra_fecha_<?=$i?>"><?=$rs[$i]?></td>
			<? } ?>
			<td class="compra_total_ano"><?=$rs['anual']?></td>
			<td class="compra_total"><?=$rs['total']?></td>
		</tr>
		<?
		$rubro_id = $rs['rubro_id'];
		$sql_subrubro = "SELECT $sql_meses,ROUND(SUM(IF(YEAR($tabla.fecha)=$ano,$tabla.monto,0)),2) as 'anual',ROUND(sum($tabla.monto),2) as total, subrubro.subrubro as tipo FROM $tabla INNER JOIN subrubro ON $tabla.subrubro_id = subrubro.id WHERE $tabla.rubro_id = '$rubro_id' GROUP BY $tabla.subrubro_id";
		$rsTemp2 =  mysql_query($sql_subrubro);echo mysql_error();
		while($rs2 = mysql_fetch_array($rsTemp2)){ ?>
			<tr class="<?=$tabla?>_rubro_<?=$rubro_id?>" style="display:none;">
				<td><?=$rs2['tipo']?></td>
				<? for($i=0;$i<12;$i++){ ?>
				<td><?=$rs2[$i]?></td>
				<? } ?>
				<td><?=$rs2['anual']?></td>
				<td><?=$rs2['total']?></td>
			</tr>
		<? } ?>
	<? } ?>
<? } ?>
	<tr style="background:#FFFF00;">
		<td>Total</td>
		<? for($i=0;$i<12;$i++){ ?>
		<td class="total_compra_fecha_<?=$i?>"></td>
		<? } ?>
		<td class="total_compra_ano"></td>
		<td class="total_compra_total"></td>
	</tr>
</tbody>
</table>

<script>
$('.medios_pago').flexigrid({height:'auto',striped:false,singleSelect:true});

for (i=0; i<12; i++){
	var sum = 0;
	$(".compra_fecha_"+i).each(function() {
		if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
		sum += parseFloat(this.innerHTML);
		}
	});
	$(".total_compra_fecha_"+i).html(sum);
}

var sum = 0;
$(".compra_total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_compra_total").html(sum);
var compra = sum;

var sum = 0;
$(".compra_total_ano").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_compra_ano").html(sum);
</script>
