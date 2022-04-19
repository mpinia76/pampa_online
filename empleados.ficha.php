<?php
include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("functions/abm.php");

$sql = "SELECT * FROM empleado WHERE id=".$_GET['empleado_id'];
$r = mysql_query($sql);
$rs = mysql_fetch_array($r);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Administrador de empleados</title>
<script type="text/javascript" src="library/jquery/jquery-1.4.2.min.js"></script>

<!--JQuery Uploadify-->
<script type="text/javascript" src="library/uploadify/jquery.uploadify.v2.1.0.min.js"></script>
<script type="text/javascript" src="library/uploadify/swfobject.js"></script>
<link href="library/uploadify/uploadify.css" rel="stylesheet" type="text/css" />
<!--/JQuery Uploadify-->

<!--JQuery editor-->
<script type="text/javascript" src="library/jwysiwyg/jquery.wysiwyg.js"></script>
<link rel="stylesheet" href="library/jwysiwyg/jquery.wysiwyg.css" type="text/css" />
<!--/JQuery editor-->

<!--JQuery Date Picker-->
<script type="text/javascript" src="library/datepicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="library/datepicker/jquery.bgiframe.js"></script><![endif]-->
<script type="text/javascript" src="library/datepicker/jquery.datePicker.min-2.1.2.js"></script>
<link href="library/datepicker/datePicker.css" rel="stylesheet" type="text/css" />
<style>
a.dp-choose-date {
	float: left;
	width: 16px;
	height: 16px;
	padding: 0;
	margin: 5px 3px 0;
	display: block;
	text-indent: -2000px;
	overflow: hidden;
	background: url(images/calendar.png) no-repeat; 
}
a.dp-choose-date.dp-disabled {
	background-position: 0 -20px;
	cursor: default;
}
/* makes the input field shorter once the date picker code
 * has run (to allow space for the calendar icon
 */
input.dp-applied {
	width: 140px;
	float: left;
}
</style>
<!--/JQuery Date Picker-->

<style>
#wrapper{
	font-family:Arial;
	font-size:12px;
	background:white;
	margin:5px;
	padding:10px;
}
</style>
<script>
var dhxWins = parent.dhxWins;

var position = dhxWins.window('w_empleado_view').getPosition(); //id de la ventana

var xpos = position[0];
var ypos = position[1];

function addJornada(){
	createWindow('w_emplado_add_jornada','Jornada de trabajo de <?php echo $rs['nombre']?> <?php echo $rs['apellido']?>','empleados.trabajo.php?empleado_id=<?php echo $rs['id']?>','600','400');
}
function addSalario(){
	createWindow('w_emplado_add_salario','Salario de <?php echo $rs['nombre']?> <?php echo $rs['apellido']?>','empleados.sueldo.php?empleado_id=<?php echo $rs['id']?>&ano=<?php echo date("Y")?>','600','600');
}
function addHoraExtra(){
	createWindow('w_emplado_add_hora_extra','Agregar Horas extras a <?php echo $rs['nombre']?> <?php echo $rs['apellido']?>','empleado.horas_extras.php?empleado_id=<?php echo $rs['id']?>','500','300');
}
function verHorasExtras(){
	createWindow('w_empleado_hora_extra','Ver Horas extras de <?php echo $rs['nombre']?> <?php echo $rs['apellido']?>','horas_extras.php?empleado_id=<?php echo $rs['id']?>','700','500');
}
function addAdelanto(){
	createWindow('w_empleado_adelanto','Adelantos a <?php echo $rs['nombre']?> <?php echo $rs['apellido']?>','empleado.adelanto.php?empleado_id=<?php echo $rs['id']?>','500','400');
}

</script>
<script src="js/createWindow.js"></script>

<style>
.jornada-historico, .sueldos-historico{
	display:none;
}
</style>
</head>

<body>

<div id="wrapper">
<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
	<?php  if($rs['foto'] != ''){ ?>
		<td valign="top" width="120"><img src="empleados/<?php echo $rs['foto']?>" width="100" style="margin-top:10px;" /></td>
	<?php  } ?>
		<td>
		<p>
		<b><?php echo $rs['nombre']?> <?php echo $rs['apellido']?></b> (<?php echo $rs['nro_legajo']?>) <br>
		Alta: <?php echo fechavista($rs['fecha_alta'])?> - Inicio actividades: <?php echo fechavista($rs['inicio_actividades'])?>
		<p> 
		<p>
		<?php echo $rs['email']?><br>
		<?php echo $rs['telefono_fijo']?> / <?php echo $rs['telefono_cel']?><br>
		<?php echo $rs['domicilio_reside']?> <?php echo $rs['localidad']?> <?php echo $rs['provincia']?>
		</p>
		<p>
		DNI: <?php echo $rs['dni']?> <br>
		Nacimiento: <?php echo fechavista($rs['nacimiento'])?> <br>
		Domicilio DNI: <?php echo $rs['domicilio_dni']?> <br>
		Estado civil: <?php echo $rs['estado_civil']?> <br>
		Hijos: <?php echo $rs['cant_hijos']?> <br>
		Estudios: <?php echo $rs['estudios']?>
		</p>
		</td>
	</tr>
</table>
<p><strong>Detalle de la jornada de trabajo</strong></p>
<?php 
$sql = "SELECT empleado_trabajo.*,a.sector as 'sector1', b.sector as 'sector2', espacio_trabajo.espacio FROM empleado_trabajo LEFT JOIN sector as a ON empleado_trabajo.sector_1_id = a.id LEFT JOIN sector as b ON empleado_trabajo.sector_2_id = b.id INNER JOIN espacio_trabajo ON empleado_trabajo.espacio_trabajo_id = espacio_trabajo.id WHERE empleado_trabajo.empleado_id=".$_GET['empleado_id']." ORDER BY id DESC";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp) > 0){
	$i = 1;
?>
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr style="font-weight:bold;">
			<td>Asignado </td>
            <td>Hrs. 0001</td>
			<td>Hrs. 0002</td>
			<td>Dur. Jornada</td>
			<td>Centro de costos</td>
			<td>Sector 1</td>
			<td width="50">%</td>
			<td>Sector 2</td>
			<td width="50">%</td>
		</tr>
<?php  	while($rs = mysql_fetch_array($rsTemp)){ ?>
		
		<tr <?php  if($i != 1){ ?> class="jornada-historico" <?php  } ?>>
			<td><?php echo fechavista($rs['fecha'])?></td>
            <td><?php echo $rs['horas_0001']?></td>
			<td><?php echo $rs['horas_0002']?></td>
			<td><?php echo $rs['duracion_jornada']?></td>
			<td><?php echo $rs['espacio']?></td>
			<td><?php echo $rs['sector1'] != '' ? $rs['sector1'] : ''?></td>
			<td><?php echo $rs['sector1'] != '' ? $rs['porcentaje_sector_1'] : ''?></td>
			<td><?php echo $rs['sector2'] != '' ? $rs['sector2'] : ''?></td>
			<td><?php echo $rs['sector2'] != '' ? $rs['porcentaje_sector_2'] : ''?></td>
		</tr>
		<?php  $i++; ?>
<?php  	} ?>
</table>
<?php  } ?>

<p>
<a style="color:blue; text-decoration: underline; cursor:pointer;" onclick="$('.jornada-historico').toggle();">Ver historico</a> - 
<a style="color:blue; text-decoration: underline; cursor:pointer;" onclick="addJornada()">Asignar nueva jornada de trabajo</a>
</p>

<p><strong>Salario convenido</strong></p>
<?php 
$sql = "SELECT * FROM empleado_sueldo_0001 WHERE empleado_id = ".$_GET['empleado_id']."";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){
	$sueldo_0001[$rs['sueldo_id']] = $rs;
}

$sql = "SELECT 
			ano,
			creado, 
			sueldo_id,
			sum(sueldo*(1-abs(sign(mes-1)))) as 'sueldo1', 
			sum(sueldo*(1-abs(sign(mes-2)))) as 'sueldo2',
			sum(sueldo*(1-abs(sign(mes-3)))) as 'sueldo3',
			sum(sueldo*(1-abs(sign(mes-4)))) as 'sueldo4',
			sum(sueldo*(1-abs(sign(mes-5)))) as 'sueldo5',
			sum(sueldo*(1-abs(sign(mes-6)))) as 'sueldo6',
			sum(sueldo*(1-abs(sign(mes-7)))) as 'sueldo7',
			sum(sueldo*(1-abs(sign(mes-8)))) as 'sueldo8',
			sum(sueldo*(1-abs(sign(mes-9)))) as 'sueldo9',
			sum(sueldo*(1-abs(sign(mes-10)))) as 'sueldo10',
			sum(sueldo*(1-abs(sign(mes-11)))) as 'sueldo11',
			sum(sueldo*(1-abs(sign(mes-12)))) as 'sueldo12',
			sum(viaticos*(1-abs(sign(mes-1)))) as 'viaticos1', 
			sum(viaticos*(1-abs(sign(mes-2)))) as 'viaticos2',
			sum(viaticos*(1-abs(sign(mes-3)))) as 'viaticos3',
			sum(viaticos*(1-abs(sign(mes-4)))) as 'viaticos4',
			sum(viaticos*(1-abs(sign(mes-5)))) as 'viaticos5',
			sum(viaticos*(1-abs(sign(mes-6)))) as 'viaticos6',
			sum(viaticos*(1-abs(sign(mes-7)))) as 'viaticos7',
			sum(viaticos*(1-abs(sign(mes-8)))) as 'viaticos8',
			sum(viaticos*(1-abs(sign(mes-9)))) as 'viaticos9',
			sum(viaticos*(1-abs(sign(mes-10)))) as 'viaticos10',
			sum(viaticos*(1-abs(sign(mes-11)))) as 'viaticos11',
			sum(viaticos*(1-abs(sign(mes-12)))) as 'viaticos12',
			sum(asignaciones*(1-abs(sign(mes-1)))) as 'asignaciones1', 
			sum(asignaciones*(1-abs(sign(mes-2)))) as 'asignaciones2',
			sum(asignaciones*(1-abs(sign(mes-3)))) as 'asignaciones3',
			sum(asignaciones*(1-abs(sign(mes-4)))) as 'asignaciones4',
			sum(asignaciones*(1-abs(sign(mes-5)))) as 'asignaciones5',
			sum(asignaciones*(1-abs(sign(mes-6)))) as 'asignaciones6',
			sum(asignaciones*(1-abs(sign(mes-7)))) as 'asignaciones7',
			sum(asignaciones*(1-abs(sign(mes-8)))) as 'asignaciones8',
			sum(asignaciones*(1-abs(sign(mes-9)))) as 'asignaciones9',
			sum(asignaciones*(1-abs(sign(mes-10)))) as 'asignaciones10',
			sum(asignaciones*(1-abs(sign(mes-11)))) as 'asignaciones11',
			sum(asignaciones*(1-abs(sign(mes-12)))) as 'asignaciones12',
			sum(presentismo*(1-abs(sign(mes-1)))) as 'presentismo1', 
			sum(presentismo*(1-abs(sign(mes-2)))) as 'presentismo2',
			sum(presentismo*(1-abs(sign(mes-3)))) as 'presentismo3',
			sum(presentismo*(1-abs(sign(mes-4)))) as 'presentismo4',
			sum(presentismo*(1-abs(sign(mes-5)))) as 'presentismo5',
			sum(presentismo*(1-abs(sign(mes-6)))) as 'presentismo6',
			sum(presentismo*(1-abs(sign(mes-7)))) as 'presentismo7',
			sum(presentismo*(1-abs(sign(mes-8)))) as 'presentismo8',
			sum(presentismo*(1-abs(sign(mes-9)))) as 'presentismo9',
			sum(presentismo*(1-abs(sign(mes-10)))) as 'presentismo10',
			sum(presentismo*(1-abs(sign(mes-11)))) as 'presentismo11',
			sum(presentismo*(1-abs(sign(mes-12)))) as 'presentismo12',
			sum(aguinaldo*(1-abs(sign(mes-1)))) as 'aguinaldo1', 
			sum(aguinaldo*(1-abs(sign(mes-2)))) as 'aguinaldo2',
			sum(aguinaldo*(1-abs(sign(mes-3)))) as 'aguinaldo3',
			sum(aguinaldo*(1-abs(sign(mes-4)))) as 'aguinaldo4',
			sum(aguinaldo*(1-abs(sign(mes-5)))) as 'aguinaldo5',
			sum(aguinaldo*(1-abs(sign(mes-6)))) as 'aguinaldo6',
			sum(aguinaldo*(1-abs(sign(mes-7)))) as 'aguinaldo7',
			sum(aguinaldo*(1-abs(sign(mes-8)))) as 'aguinaldo8',
			sum(aguinaldo*(1-abs(sign(mes-9)))) as 'aguinaldo9',
			sum(aguinaldo*(1-abs(sign(mes-10)))) as 'aguinaldo10',
			sum(aguinaldo*(1-abs(sign(mes-11)))) as 'aguinaldo11',
			sum(aguinaldo*(1-abs(sign(mes-12)))) as 'aguinaldo12'
		FROM empleado_sueldo 
		WHERE empleado_id = ".$_GET['empleado_id']." 
		GROUP BY sueldo_id 
		ORDER BY sueldo_id DESC";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp) > 0){ 
	$i = 1;
?>
<table cellpadding="0" cellspacing="0">
	<tr style="font-weight:bold;">
    	<td width="60">A&ntilde;o</td>
    	<td width="100">Asignado</td>
        <td width="120">Convenio de</td>
        <td width="80">Ene</td>
        <td width="80">Feb</td>
        <td width="80">Mar</td>
        <td width="80">Abr</td>
        <td width="80">May</td>
        <td width="80">Jun</td>
        <td width="80">Jul</td>
        <td width="80">Ago</td>
        <td width="80">Sep</td>
        <td width="80">Oct</td>
        <td width="80">Nov</td>
        <td width="80">Dic</td>
   	</tr>
<?php  while($rs = mysql_fetch_array($rsTemp)){ ?>
	<tr <?php  if($i != 1){ ?> class="sueldos-historico" <?php  } ?>>
    	<td rowspan="6" valign="middle"><?php echo $rs['ano']?></td>
    	<td rowspan="6" valign="middle"><?php echo fechavista($rs['creado'])?></td>
    	<td>Salario</td>
        <td><?php echo $rs['sueldo1']?></td>
        <td><?php echo $rs['sueldo2']?></td>
        <td><?php echo $rs['sueldo3']?></td>
        <td><?php echo $rs['sueldo4']?></td>
        <td><?php echo $rs['sueldo5']?></td>
        <td><?php echo $rs['sueldo6']?></td>
        <td><?php echo $rs['sueldo7']?></td>
        <td><?php echo $rs['sueldo8']?></td>
        <td><?php echo $rs['sueldo9']?></td>
        <td><?php echo $rs['sueldo10']?></td>
        <td><?php echo $rs['sueldo11']?></td>
        <td><?php echo $rs['sueldo12']?></td>
   	</tr>
	<tr <?php  if($i != 1){ ?> class="sueldos-historico" <?php  } ?>>
    	<td>Viaticos</td>
        <td><?php echo $rs['viaticos1']?></td>
        <td><?php echo $rs['viaticos2']?></td>
        <td><?php echo $rs['viaticos3']?></td>
        <td><?php echo $rs['viaticos4']?></td>
        <td><?php echo $rs['viaticos5']?></td>
        <td><?php echo $rs['viaticos6']?></td>
        <td><?php echo $rs['viaticos7']?></td>
        <td><?php echo $rs['viaticos8']?></td>
        <td><?php echo $rs['viaticos9']?></td>
        <td><?php echo $rs['viaticos10']?></td>
        <td><?php echo $rs['viaticos11']?></td>
        <td><?php echo $rs['viaticos12']?></td>
   	</tr>
	<tr <?php  if($i != 1){ ?> class="sueldos-historico" <?php  } ?>>
    	<td>Asignaciones</td>
        <td><?php echo $rs['asignaciones1']?></td>
        <td><?php echo $rs['asignaciones2']?></td>
        <td><?php echo $rs['asignaciones3']?></td>
        <td><?php echo $rs['asignaciones4']?></td>
        <td><?php echo $rs['asignaciones5']?></td>
        <td><?php echo $rs['asignaciones6']?></td>
        <td><?php echo $rs['asignaciones7']?></td>
        <td><?php echo $rs['asignaciones8']?></td>
        <td><?php echo $rs['asignaciones9']?></td>
        <td><?php echo $rs['asignaciones10']?></td>
        <td><?php echo $rs['asignaciones11']?></td>
        <td><?php echo $rs['asignaciones12']?></td>
   	</tr>
	<tr <?php  if($i != 1){ ?> class="sueldos-historico" <?php  } ?>>
    	<td>Presentismo</td>
        <td><?php echo $rs['presentismo1']?></td>
        <td><?php echo $rs['presentismo2']?></td>
        <td><?php echo $rs['presentismo3']?></td>
        <td><?php echo $rs['presentismo4']?></td>
        <td><?php echo $rs['presentismo5']?></td>
        <td><?php echo $rs['presentismo6']?></td>
        <td><?php echo $rs['presentismo7']?></td>
        <td><?php echo $rs['presentismo8']?></td>
        <td><?php echo $rs['presentismo9']?></td>
        <td><?php echo $rs['presentismo10']?></td>
        <td><?php echo $rs['presentismo11']?></td>
        <td><?php echo $rs['presentismo12']?></td>
   	</tr>
	<tr <?php  if($i != 1){ ?> class="sueldos-historico" <?php  } ?>>
    	<td>Aguinaldo</td>
        <td><?php echo $rs['aguinaldo1']?></td>
        <td><?php echo $rs['aguinaldo2']?></td>
        <td><?php echo $rs['aguinaldo3']?></td>
        <td><?php echo $rs['aguinaldo4']?></td>
        <td><?php echo $rs['aguinaldo5']?></td>
        <td><?php echo $rs['aguinaldo6']?></td>
        <td><?php echo $rs['aguinaldo7']?></td>
        <td><?php echo $rs['aguinaldo8']?></td>
        <td><?php echo $rs['aguinaldo9']?></td>
        <td><?php echo $rs['aguinaldo10']?></td>
        <td><?php echo $rs['aguinaldo11']?></td>
        <td><?php echo $rs['aguinaldo12']?></td>
   	</tr>
	<tr style="background:#FF9;" <?php  if($i != 1){ ?> class="sueldos-historico" <?php  } ?>>
    	<td>Total</td>
        <td><?php echo $rs['sueldo1'] + $rs['viaticos1'] + $rs['asignaciones1'] + $rs['presentismo1'] + $rs['aguinaldo1']?></td>
        <td><?php echo $rs['sueldo2'] + $rs['viaticos2'] + $rs['asignaciones2'] + $rs['presentismo2'] + $rs['aguinaldo2']?></td>
        <td><?php echo $rs['sueldo3'] + $rs['viaticos3'] + $rs['asignaciones3'] + $rs['presentismo3'] + $rs['aguinaldo3']?></td>
        <td><?php echo $rs['sueldo4'] + $rs['viaticos4'] + $rs['asignaciones4'] + $rs['presentismo4'] + $rs['aguinaldo4']?></td>
        <td><?php echo $rs['sueldo5'] + $rs['viaticos5'] + $rs['asignaciones5'] + $rs['presentismo5'] + $rs['aguinaldo5']?></td>
        <td><?php echo $rs['sueldo6'] + $rs['viaticos6'] + $rs['asignaciones6'] + $rs['presentismo6'] + $rs['aguinaldo6']?></td>
        <td><?php echo $rs['sueldo7'] + $rs['viaticos7'] + $rs['asignaciones7'] + $rs['presentismo7'] + $rs['aguinaldo7']?></td>
        <td><?php echo $rs['sueldo8'] + $rs['viaticos8'] + $rs['asignaciones8'] + $rs['presentismo8'] + $rs['aguinaldo8']?></td>
        <td><?php echo $rs['sueldo9'] + $rs['viaticos9'] + $rs['asignaciones9'] + $rs['presentismo9'] + $rs['aguinaldo9']?></td>
        <td><?php echo $rs['sueldo10'] + $rs['viaticos10'] + $rs['asignaciones10'] + $rs['presentismo10'] + $rs['aguinaldo10']?></td>
        <td><?php echo $rs['sueldo11'] + $rs['viaticos11'] + $rs['asignaciones11'] + $rs['presentismo11'] + $rs['aguinaldo11']?></td>
        <td><?php echo $rs['sueldo12'] + $rs['viaticos12'] + $rs['asignaciones12'] + $rs['presentismo12'] + $rs['aguinaldo12']?></td>
   	</tr>
	<tr <?php  if($i != 1){ ?> class="sueldos-historico" <?php  } ?>>
		<td colspan="15">
		<b>Calificacion:</b> <?php echo $sueldo_0001[$rs['sueldo_id']]['calificacion']?> <br>
		<b>Seccion:</b> <?php echo $sueldo_0001[$rs['sueldo_id']]['seccion']?> <br>
		<b>Sueldo de bolsillo estimado:</b> <?php echo $sueldo_0001[$rs['sueldo_id']]['sueldo']?> <br>
		<b>Categoria:</b> <?php echo $sueldo_0001[$rs['sueldo_id']]['categoria']?>
		</td>
	</tr>
    <tr <?php  if($i != 1){ ?> class="sueldos-historico" <?php  } ?>>
    	<td colspan="15">&nbsp;</td>
    </tr>
    <?php  $i++; ?>
<?php  } ?>
</table>
<?php  } ?>
<p>
<a style="color:blue; text-decoration: underline; cursor:pointer;" onclick="$('.sueldos-historico').toggle();">Ver historico</a> -
<a style="color:blue; text-decoration: underline; cursor:pointer;" onclick="addSalario()">Asignar nuevo sueldo</a>
</p>

<p><strong>Horas Extras Aprobadas</strong></p>
<?php 
$sql = "SELECT 
			ano,
			sum(if(mes = 1,ehe.cantidad_aprobada*she.valor,0)) as '1', 
			sum(if(mes = 2,ehe.cantidad_aprobada*she.valor,0)) as '2',
			sum(if(mes = 3,ehe.cantidad_aprobada*she.valor,0)) as '3',
			sum(if(mes = 4,ehe.cantidad_aprobada*she.valor,0)) as '4',
			sum(if(mes = 5,ehe.cantidad_aprobada*she.valor,0)) as '5',
			sum(if(mes = 6,ehe.cantidad_aprobada*she.valor,0)) as '6',
			sum(if(mes = 7,ehe.cantidad_aprobada*she.valor,0)) as '7',
			sum(if(mes = 8,ehe.cantidad_aprobada*she.valor,0)) as '8',
			sum(if(mes = 9,ehe.cantidad_aprobada*she.valor,0)) as '9',
			sum(if(mes = 10,ehe.cantidad_aprobada*she.valor,0)) as '10',
			sum(if(mes = 11,ehe.cantidad_aprobada*she.valor,0)) as '11',
			sum(if(mes = 12,ehe.cantidad_aprobada*she.valor,0)) as '12'
		FROM 
			empleado_hora_extra ehe INNER JOIN sector_horas_extras she ON ehe.hora_extra_id = she.id 
		WHERE 
			ehe.empleado_id = ".$_GET['empleado_id']." 
			AND 
			ehe.estado = 1 
		GROUP BY 
			ehe.ano";
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp) > 0){ 
?>
<table cellpadding="0" cellspacing="0">
	<tr style="font-weight:bold;">
    	<td width="450" align="center">A&ntilde;o</td>
        <td width="80">Ene</td>
        <td width="80">Feb</td>
        <td width="80">Mar</td>
        <td width="80">Abr</td>
        <td width="80">May</td>
        <td width="80">Jun</td>
        <td width="80">Jul</td>
        <td width="80">Ago</td>
        <td width="80">Sep</td>
        <td width="80">Oct</td>
        <td width="80">Nov</td>
        <td width="80">Dic</td>
   	</tr>
<?php  while($rs = mysql_fetch_array($rsTemp)){ ?>
	<tr>
    	<td valign="middle" align="center"><?php echo $rs['ano']?></td>
        <td><?php echo round($rs['1'],2)?></td>
        <td><?php echo round($rs['2'],2)?></td>
        <td><?php echo round($rs['3'],2)?></td>
        <td><?php echo round($rs['4'],2)?></td>
        <td><?php echo round($rs['5'],2)?></td>
        <td><?php echo round($rs['6'],2)?></td>
        <td><?php echo round($rs['7'],2)?></td>
        <td><?php echo round($rs['8'],2)?></td>
        <td><?php echo round($rs['9'],2)?></td>
        <td><?php echo round($rs['10'],2)?></td>
        <td><?php echo round($rs['11'],2)?></td>
        <td><?php echo round($rs['12'],2)?></td>
   	</tr>
<?php  } ?>
</table>
<?php  }else{ ?>
No se ha cargado ninguna hora extra aprobada
<?php  } ?>
<p>
<a style="color:blue; text-decoration: underline; cursor:pointer;" onclick="addHoraExtra()">Agregar hora extra</a> 
- <a style="color:blue; text-decoration: underline; cursor:pointer;" onclick="verHorasExtras()">Ver horas extras asignadas</a>
<?php 
$sql = "SELECT id FROM empleado_hora_extra WHERE empleado_id = ".$_GET['empleado_id']." AND estado = 0";
$cant = mysql_num_rows(mysql_query($sql));
if($cant > 0){?>
	(<?php echo $cant?> pendientes de aprobar) 
<?php  } ?>
</p>

<p><strong>Adelantos otorgados</strong></p>
<?php 
$sql = "SELECT 
			ano,
			sum(if(mes = 1,monto,0)) as '1', 
			sum(if(mes = 2,monto,0)) as '2',
			sum(if(mes = 3,monto,0)) as '3',
			sum(if(mes = 4,monto,0)) as '4',
			sum(if(mes = 5,monto,0)) as '5',
			sum(if(mes = 6,monto,0)) as '6',
			sum(if(mes = 7,monto,0)) as '7',
			sum(if(mes = 8,monto,0)) as '8',
			sum(if(mes = 9,monto,0)) as '9',
			sum(if(mes = 10,monto,0)) as '10',
			sum(if(mes = 11,monto,0)) as '11',
			sum(if(mes = 12,monto,0)) as '12'
		FROM 
			empleado_adelanto 
		WHERE 
			empleado_id = ".$_GET['empleado_id'];
			
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp) > 0){ 
?>
<table cellpadding="0" cellspacing="0">
	<tr style="font-weight:bold;">
    	<td width="450" align="center">A&ntilde;o</td>
        <td width="80">Ene</td>
        <td width="80">Feb</td>
        <td width="80">Mar</td>
        <td width="80">Abr</td>
        <td width="80">May</td>
        <td width="80">Jun</td>
        <td width="80">Jul</td>
        <td width="80">Ago</td>
        <td width="80">Sep</td>
        <td width="80">Oct</td>
        <td width="80">Nov</td>
        <td width="80">Dic</td>
   	</tr>
<?php  while($rs = mysql_fetch_array($rsTemp)){ ?>
	<tr>
    	<td valign="middle" align="center"><?php echo $rs['ano']?></td>
        <td><?php echo round($rs['1'],2)?></td>
        <td><?php echo round($rs['2'],2)?></td>
        <td><?php echo round($rs['3'],2)?></td>
        <td><?php echo round($rs['4'],2)?></td>
        <td><?php echo round($rs['5'],2)?></td>
        <td><?php echo round($rs['6'],2)?></td>
        <td><?php echo round($rs['7'],2)?></td>
        <td><?php echo round($rs['8'],2)?></td>
        <td><?php echo round($rs['9'],2)?></td>
        <td><?php echo round($rs['10'],2)?></td>
        <td><?php echo round($rs['11'],2)?></td>
        <td><?php echo round($rs['12'],2)?></td>
   	</tr>
<?php  } ?>
</table>
<?php  }else{ ?>
No se ha otorgado ningun adelanto
<?php  } ?>
<p><a style="color:blue; text-decoration: underline; cursor:pointer;" onclick="addAdelanto()">Otorgar adelanto</a></p>
<p><strong>Hist&oacute;rico</strong></p>
<?php 
$sql = "SELECT 
			*
			
		FROM 
			empleado_historico
		WHERE 
			empleado_id = ".$_GET['empleado_id'];
			
$rsTemp = mysql_query($sql);
if(mysql_num_rows($rsTemp) > 0){ 
?>
<table cellpadding="0" cellspacing="0">
	<tr style="font-weight:bold;">
    	
        <td width="80">Alta</td>
        <td width="80">Baja</td>
        
   	</tr>
<?php  while($rs = mysql_fetch_array($rsTemp)){ ?>
	<tr>
    	
        <td><?php 
        $alta = ((fechavista($rs['alta'])=="//")||(fechavista($rs['alta'])=="00/00/0000"))?'':fechavista($rs['alta']);
        echo $alta;
        ?></td>
        <td><?php 
        $baja = ((fechavista($rs['baja'])=="//")||(fechavista($rs['baja'])=="00/00/0000"))?'':fechavista($rs['baja']);
        echo $baja;
        ?></td>
        
   	</tr>
<?php  } ?>
</table>
<?php  } ?>
</div>
</body>
</html>
