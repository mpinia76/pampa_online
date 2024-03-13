<?php 
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="library/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="library/flexigrid/flexigrid.css" />
<script type="text/javascript" src="library/flexigrid/flexigrid.js"></script> 
<script>
function roundVal(num){
	return Math.round(num*100)/100;
}
</script>
<title>Documento sin t&iacute;tulo</title>
<style>
a{
text-decoration:underline;
color:#0000FF;
cursor:pointer;
}
</style>
</head>

<body>
<?php include_once("config/db.php"); 
include_once("functions/util.php");
auditarUsuarios('Informe de cheques');

?>
<?php if(isset($_POST['ano'])) { $ano = $_POST['ano']; }else{ $ano= date('Y'); } ?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<select size="1" name="ano">
	<option <?php if($ano == '2010'){?> selected="selected" <?php } ?> >2010</option>
	<option <?php if($ano == '2011'){?> selected="selected" <?php } ?> >2011</option>
	<option <?php if($ano == '2012'){?> selected="selected" <?php } ?> >2012</option>
	<option <?php if($ano == '2013'){?> selected="selected" <?php } ?> >2013</option>
	<option <?php if($ano == '2014'){?> selected="selected" <?php } ?> >2014</option>
	<option <?php if($ano == '2015'){?> selected="selected" <?php } ?> >2015</option>
    <option <?php if($ano == '2016'){?> selected="selected" <?php } ?> >2016</option>
    <option <?php if($ano == '2017'){?> selected="selected" <?php } ?> >2017</option>
    <option <?php if($ano == '2018'){?> selected="selected" <?php } ?> >2018</option>
    <option <?php if($ano == '2019'){?> selected="selected" <?php } ?> >2019</option>
    <option <?php if($ano == '2020'){?> selected="selected" <?php } ?> >2020</option>
    <option <?php if($ano == '2021'){?> selected="selected" <?php } ?> >2021</option>
    <option <?php if($ano == '2022'){?> selected="selected" <?php } ?> >2022</option>
    <option <?php if($ano == '2023'){?> selected="selected" <?php } ?> >2023</option>
    <option <?php if($ano == '2024'){?> selected="selected" <?php } ?> >2024</option>
</select> 
<input type="submit" value=">" name="buscar" />
</form>


<!--CHEQUES-->
<table class="medios_pago">
<thead>
	<tr>
		<th width="100">Cheques librados</th>
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

$tabla 		= "cheque_consumo";
$sql_meses 	= sql_meses($tabla,$ano,'fecha');
$sql 		= "SELECT $sql_meses,ROUND(SUM(IF(YEAR($tabla.fecha)=$ano,$tabla.monto,0)),2) as 'anual',ROUND(sum($tabla.monto),2) as total, concat(banco.banco,' ',cuenta.sucursal,' ',cuenta.nombre) as tipo FROM $tabla INNER JOIN cuenta ON $tabla.cuenta_id = cuenta.id INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id WHERE $tabla.vencido = 0 GROUP BY $tabla.cuenta_id";

$rsTemp =  mysql_query($sql);echo mysql_error();
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
	<tr>
		<td><?php echo $rs['tipo']?></td>
		<?php for($i=0;$i<12;$i++){ ?>
		<td class="cheque_librado_fecha_<?php echo $i?>"><?php echo $rs[$i]?></td>
		<?php } ?>
		<td class="cheque_librado_total_anual"><?php echo $rs['anual']?></td>
		<td class="cheque_librado_total"><?php echo $rs['total']?></td>
	</tr>
	<?php } ?>
<?php } ?>
	<tr style="background:#FC6;">
		<td>Total</td>
		<?php for($i=0;$i<12;$i++){ ?>
		<td class="total_cheque_librado_fecha_<?php echo $i?>"></td>
		<?php } ?>
		<td class="total_cheque_librado_anual"></td>
		<td class="total_cheque_librado_total"></td>
	</tr>
</tbody>
</table>

<script>
$('.medios_pago').flexigrid({height:'auto',striped:false,singleSelect:true});

for (i=0; i<12; i++){
	var sum = 0;
	$(".cheque_librado_fecha_"+i).each(function() {
		if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
		sum += parseFloat(this.innerHTML);
		}
	});
	$(".total_cheque_librado_fecha_"+i).html(roundVal(sum));
}

var sum = 0;
$(".cheque_librado_total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_cheque_librado_total").html(roundVal(sum));
var cheque = sum;
var sum = 0;
$(".cheque_librado_total_anual").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_cheque_librado_anual").html(roundVal(sum));

</script>


<!--CHEQUES-->
<table class="medios_pago">
<thead>
	<tr>
		<th width="100">Cheques debitados</th>
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

$tabla 		= "cheque_consumo";
$sql_meses 	= sql_meses($tabla,$ano,'fecha_debitado');
$sql 		= "SELECT $sql_meses,ROUND(SUM(IF(YEAR($tabla.fecha)=$ano,$tabla.monto,0)),2) as 'anual',ROUND(sum($tabla.monto),2) as total, concat(banco.banco,' ',cuenta.sucursal,' ',cuenta.nombre) as tipo FROM $tabla INNER JOIN cuenta ON $tabla.cuenta_id = cuenta.id INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id WHERE $tabla.vencido = 0 AND ($tabla.concepto not like 'Reemplazado%' OR $tabla.concepto is null) AND ($tabla.concepto not like 'Anulado%' OR $tabla.concepto is null) GROUP BY $tabla.cuenta_id";
$rsTemp =  mysql_query($sql);echo mysql_error();
while($rs = mysql_fetch_array($rsTemp)){
	if($rs['total'] != NULL){ ?>
	<tr>
		<td><?php echo $rs['tipo']?></td>
		<?php for($i=0;$i<12;$i++){ ?>
		<td class="cheque_fecha_<?php echo $i?>"><?php echo $rs[$i]?></td>
		<?php } ?>
		<td class="cheque_total_anual"><?php echo $rs['anual']?></td>
		<td class="cheque_total"><?php echo $rs['total']?></td>
	</tr>
	<?php } ?>
<?php } ?>
	<tr style="background:#FFFF00;">
		<td>Total</td>
		<?php for($i=0;$i<12;$i++){ ?>
		<td class="total_cheque_fecha_<?php echo $i?>"></td>
		<?php } ?>
		<td class="total_cheque_anual"></td>
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
	$(".total_cheque_fecha_"+i).html(roundVal(sum));
}

var sum = 0;
$(".cheque_total").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_cheque_total").html(roundVal(sum));
var cheque = sum;
var sum = 0;
$(".cheque_total_anual").each(function() {
	if(!isNaN(this.innerHTML) && this.innerHTML.length!=0) {
	sum += parseFloat(this.innerHTML);
	}
});
$(".total_cheque_anual").html(roundVal(sum));

</script>

<?php 
$ant = $ano - 1;
$sql = "SELECT if(sum(monto) is null,0,sum(monto)) as 'acumulado' FROM cheque_consumo WHERE debitado = 0 AND YEAR(fecha) = '$ant'";
$rs = mysql_fetch_array(mysql_query($sql))
?>

<table class="medios_pago">
<thead>
	<tr>
		<th width="100"></th>
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
	<tr>
		<td width="100">Pendiente</td>
<?php
$campo = 'fecha';
$sql = "SELECT 
    ROUND(SUM(IF(MONTH($tabla.$campo)=1 AND YEAR($tabla.$campo)=$ano,$tabla.monto,0)),2) -  ROUND(SUM(IF(MONTH($tabla.$campo)=1 AND YEAR($tabla.$campo)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as '1',
    ROUND(SUM(IF(MONTH($tabla.$campo)=2 AND YEAR($tabla.$campo)=$ano,$tabla.monto,0)),2) -  ROUND(SUM(IF(MONTH($tabla.$campo)=2 AND YEAR($tabla.$campo)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as '2',
    ROUND(SUM(IF(MONTH($tabla.$campo)=3 AND YEAR($tabla.$campo)=$ano,$tabla.monto,0)),2) -  ROUND(SUM(IF(MONTH($tabla.$campo)=3 AND YEAR($tabla.$campo)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as '3',
    ROUND(SUM(IF(MONTH($tabla.$campo)=4 AND YEAR($tabla.$campo)=$ano,$tabla.monto,0)),2) -  ROUND(SUM(IF(MONTH($tabla.$campo)=4 AND YEAR($tabla.$campo)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as '4',
    ROUND(SUM(IF(MONTH($tabla.$campo)=5 AND YEAR($tabla.$campo)=$ano,$tabla.monto,0)),2) -  ROUND(SUM(IF(MONTH($tabla.$campo)=5 AND YEAR($tabla.$campo)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as '5', 
    ROUND(SUM(IF(MONTH($tabla.$campo)=6 AND YEAR($tabla.$campo)=$ano,$tabla.monto,0)),2) -  ROUND(SUM(IF(MONTH($tabla.$campo)=6 AND YEAR($tabla.$campo)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as '6',
    ROUND(SUM(IF(MONTH($tabla.$campo)=7 AND YEAR($tabla.$campo)=$ano,$tabla.monto,0)),2) -  ROUND(SUM(IF(MONTH($tabla.$campo)=7 AND YEAR($tabla.$campo)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as '7',
    ROUND(SUM(IF(MONTH($tabla.$campo)=8 AND YEAR($tabla.$campo)=$ano,$tabla.monto,0)),2) -  ROUND(SUM(IF(MONTH($tabla.$campo)=8 AND YEAR($tabla.$campo)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as '8',
    ROUND(SUM(IF(MONTH($tabla.$campo)=9 AND YEAR($tabla.$campo)=$ano,$tabla.monto,0)),2) -  ROUND(SUM(IF(MONTH($tabla.$campo)=9 AND YEAR($tabla.$campo)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as '9',
    ROUND(SUM(IF(MONTH($tabla.$campo)=10 AND YEAR($tabla.$campo)=$ano,$tabla.monto,0)),2) -  ROUND(SUM(IF(MONTH($tabla.$campo)=10 AND YEAR($tabla.$campo)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as '10',
    ROUND(SUM(IF(MONTH($tabla.$campo)=11 AND YEAR($tabla.$campo)=$ano,$tabla.monto,0)),2) -  ROUND(SUM(IF(MONTH($tabla.$campo)=11 AND YEAR($tabla.$campo)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as '11',
    ROUND(SUM(IF(MONTH($tabla.$campo)=12 AND YEAR($tabla.$campo)=$ano,$tabla.monto,0)),2) -  ROUND(SUM(IF(MONTH($tabla.$campo)=12 AND YEAR($tabla.$campo)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as '12',
    ROUND(SUM(IF(YEAR($tabla.fecha)=$ano,$tabla.monto,0)),2) - ROUND(SUM(IF(YEAR($tabla.fecha)=$ano AND $tabla.debitado = 1,$tabla.monto,0)),2) as 'anual',
    ROUND(sum($tabla.monto),2) -  ROUND(sum(IF($tabla.debitado = 1,$tabla.monto,0)),2) as total FROM $tabla WHERE $tabla.vencido = 0";
    $rs = mysql_fetch_array(mysql_query($sql)); //echo $sql; //echo mysql_error();
    for($i=0;$i<12;$i++){
?>
		<td width="50" class="pendiente_<?php echo $i?>"><?php echo $rs[$i]?></td>
<?php
    }
?>
                
		<td width="50"><?php echo $rs['anual']?></td>
		<td width="50"><?php echo $rs['total']?></td>
	</tr>
	<tr>
		<td width="100">Acumulado</th>
		<td width="50" class="acumulado_0"><?php echo $rs['acumulado']?></th>
		<td width="50" class="acumulado_1"></th>
		<td width="50" class="acumulado_2"></th>
		<td width="50" class="acumulado_3"></th>
		<td width="50" class="acumulado_4"></th>
		<td width="50" class="acumulado_5"></th>
		<td width="50" class="acumulado_6"></th>
		<td width="50" class="acumulado_7"></th>
		<td width="50" class="acumulado_8"></th>
		<td width="50" class="acumulado_9"></th>
		<td width="50" class="acumulado_10"></th>
		<td width="50" class="acumulado_11"></th>
		<td width="50" class="acumulado_anual"></th>
		<td width="50" class="acumulado_total"></th>
	</tr>
</tbody>
</table>
<script> 
$('.medios_pago').flexigrid({height:'auto',striped:false,singleSelect:true});
     
for (i=0; i<12; i++){

	if(i > 0){
		var ant = parseFloat(i) - 1;
		var acumulado = parseFloat($(".acumulado_"+ant).html())+parseFloat($(".pendiente_"+i).html());
	}else{
		var acumulado = parseFloat($(".pendiente_"+i).html()); console.log(acumulado)
	}
		$(".acumulado_"+i).html(roundVal(acumulado));
}

$(".acumulado_anual").html(roundVal(acumulado));

</script>
</body>
</html>
