<!--CHEQUES-->
<table class="medios_pago">
<thead>
	<tr>
		<th width="100">Cheque</th>
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
<?php 

$tabla 		= "cheque_consumo";
$sql_meses 	= sql_meses($tabla,$ano);
$sql 		= "SELECT $sql_meses,ROUND(sum($tabla.monto),2) as total, concat(banco.banco,' ',cuenta.sucursal,' ',cuenta.nombre) as tipo FROM $tabla INNER JOIN cuenta ON $tabla.cuenta_id = cuenta.id INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id WHERE $tabla.debitado = 1 GROUP BY $tabla.cuenta_id";

$rsTemp =  mysqli_query($conn,$sql);echo mysql_error();
while($rs = mysqli_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
	<tr>
		<td><?php echo $rs['tipo']?></td>
		<?php for($i=0;$i<12;$i++){ ?>
		<td class="cheque_fecha_<?php echo $i?>"><?php echo $rs[$i]?></td>
		<?php } ?>
		<td class="cheque_total"><?php echo $rs['total']?></td>
	</tr>
	<?php } ?>
<?php } ?>
	<tr style="background:#FFFF00;">
		<td>Total</td>
		<?php for($i=0;$i<12;$i++){ ?>
		<td class="total_cheque_fecha_<?php echo $i?>"></td>
		<?php } ?>
		<td class="total_cheque_total"></td>
	</tr>
</tbody>
</table>

<script>
$('.medios_pago').flexigrid({height:'auto',striped:false,singleSelect:true});

for (i=0; i<12; i++){
	var sum = 0;
	$(".cheque_fecha_"+i).each(function() {
		if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
		sum += parseFloat(this.innerHTML);
		}
	});
	$(".total_cheque_fecha_"+i).html(sum);
}

var sum = 0;
$(".cheque_total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_cheque_total").html(sum);

</script>
