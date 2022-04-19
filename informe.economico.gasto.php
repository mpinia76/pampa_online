<!--GASTOS-->
<table class="medios_pago">
<thead>
	<tr>
		<th width="100">Gasto</th>
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
		<!--  <th width="50">Total</th>-->
	</tr>
</thead>
<tbody>
<?php
session_start();
if ($espacio_trabajo) {
	$arrayRubros = array();
	if ($_SESSION['admin']) {
				$sqlRubros = "SELECT DISTINCT usuario_rubro.rubro_id
FROM usuario INNER JOIN usuario_rubro ON usuario.id = usuario_rubro.usuario_id
INNER JOIN rubro ON rubro.id = usuario_rubro.rubro_id
INNER JOIN espacio_trabajo ON usuario.espacio_trabajo_id = espacio_trabajo.id
WHERE rubro.gastos=1 AND espacio_trabajo.id = ".$espacio_trabajo;
			}
			else{
				$sqlRubros = "SELECT DISTINCT usuario_rubro.rubro_id
FROM usuario INNER JOIN usuario_rubro ON usuario.id = usuario_rubro.usuario_id
INNER JOIN rubro ON rubro.id = usuario_rubro.rubro_id
WHERE rubro.gastos=1 AND usuario.id = ".$_SESSION['userid'];
			}

	$rsTempRubros =  mysql_query($sqlRubros);
	while($rsRubros = mysql_fetch_array($rsTempRubros)){
		$arrayRubros[]=$rsRubros['rubro_id'];
	}
}
//echo $sqlRubros;

$tabla 		= "gasto";
$sql_meses 	= sql_meses($tabla,$ano);
$sql 		= "SELECT $sql_meses,ROUND(SUM(IF(YEAR($tabla.fecha)=$ano,$tabla.monto,0)),2) as 'anual',ROUND(sum($tabla.monto),2) as total, rubro.rubro as tipo, rubro.id as rubro_id FROM $tabla INNER JOIN rubro ON $tabla.rubro_id = rubro.id WHERE rubro.gastos=1 AND quitar_egresos != 1";
if ($espacio_trabajo) {
	$rubros = join("','",$arrayRubros);
	$sql .= " AND $tabla.rubro_id IN ('$rubros')";
}
$sql .=" GROUP BY $tabla.rubro_id";
//echo $sql;
$rsTemp =  mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
		<tr>
			<td><a style="color:blue;" onclick="$('.<?php echo $tabla?>_rubro_<?php echo $rs['rubro_id']?>').toggle();"><?php echo $rs['tipo']?></a></td>
			<?php for($i=0;$i<12;$i++){ ?>
			<td class="gasto_fecha_<?php echo $i?>"><?php echo $rs[$i]?></td>
			<?php } ?>
			<td class="gasto_total_ano"><?php echo $rs['anual']?></td>
			<!--  <td class="gasto_total"><?php echo $rs['total']?></td> -->
		</tr>

		<?php
		$rubro_id = $rs['rubro_id'];
		$sql_subrubro = "SELECT $sql_meses,ROUND(SUM(IF(YEAR($tabla.fecha)=$ano,$tabla.monto,0)),2) as 'anual',ROUND(sum($tabla.monto),2) as total, subrubro.subrubro as tipo FROM $tabla INNER JOIN subrubro ON $tabla.subrubro_id = subrubro.id WHERE $tabla.rubro_id = '$rubro_id' AND quitar_egresos != 1 GROUP BY $tabla.subrubro_id";
		$rsTemp2 =  mysql_query($sql_subrubro);
		while($rs2 = mysql_fetch_array($rsTemp2)){ ?>
			<tr class="<?php echo $tabla?>_rubro_<?php echo $rubro_id?>" style="display:none;">
				<td><?php echo $rs2['tipo']?></td>
				<?php for($i=0;$i<12;$i++){ ?>
				<td><?php echo $rs2[$i]?></td>
				<?php } ?>
				<td><?php echo $rs2['anual']?></td>
				<!--<td><?php echo $rs2['total']?></td>-->
			</tr>
		<?php } ?>
	<?php } ?>
<?php } ?>
	<tr style="background:#FFFF00;">
		<td>Total</td>
		<?php for($i=0;$i<12;$i++){ ?>
		<td class="total_gasto_fecha_<?php echo $i?>"></td>
		<?php } ?>
		<td class="total_gasto_ano"></td>
		<!--  <td class="total_gasto_total"></td>-->
	</tr>
</tbody>
</table>

<script>
$('.medios_pago').flexigrid({height:'auto',striped:false,singleSelect:true});

for (i=0; i<12; i++){
	var sum = 0;
	$(".gasto_fecha_"+i).each(function() {
		if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
		sum += parseFloat(this.innerHTML);
		}
	});
	$(".total_gasto_fecha_"+i).html(sum);
}

var sum = 0;
$(".gasto_total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_gasto_total").html(sum);
var gasto = sum;

var sum = 0;
$(".gasto_total_ano").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_gasto_ano").html(sum);

</script>
