<?php
session_start();
$user_id = $_SESSION['userid'];
if($user_id == '') { header("Location: index.php"); }

include_once("functions/form.class.php");
include_once("functions/fechasql.php");
include_once("config/db.php");
include_once("config/user.php");
include_once("functions/abm.php");

$empleado_id = $_GET['empleado_id'];
$ano = $_GET['ano'];
$mes = $_GET['mes'];



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
<script>
var dhxWins = parent.dhxWins;

function borrar_adelanto(id){
	
		if(confirm("Esta seguro que desea anular el adelanto ?")){
			
			dhxWins.window('w_empleados_sueldos_detalle').attachURL('borrar_adelanto_procesa.php?id='+id);
			//createWindow('w_borrar_adelanto','Anular Adelanto de sueldo','borrar_adelanto_procesa.php?id='+id,'650','550'); //nombre de los divs
		}
	
}
</script>
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
<link href="styles/form.css" rel="stylesheet" type="text/css" />
<link href="styles/form2.css" rel="stylesheet" type="text/css" />

</head>

<body>

<?php  include_once("config/messages.php"); ?>

<div class="container" style="font-family:arial; font-size:12px;"> 

<?php 

        

        $sql = "SELECT * FROM empleado WHERE id = $empleado_id";
        $rs = mysql_fetch_array(mysql_query($sql));
        ?>
        <p><strong><?php echo $rs['nombre']?> <?php echo $rs['apellido']?></strong> (<?php echo $mes?>/<?php echo $ano?>) </p>
        
        <p><strong>Sector de trabajo:</strong> 
        <?php 
        $sql = "SELECT empleado_trabajo.*,a.sector as 'sector1', b.sector as 'sector2', espacio_trabajo.espacio FROM empleado_trabajo LEFT JOIN sector as a ON empleado_trabajo.sector_1_id = a.id LEFT JOIN sector as b ON empleado_trabajo.sector_2_id = b.id INNER JOIN espacio_trabajo ON empleado_trabajo.espacio_trabajo_id = espacio_trabajo.id WHERE empleado_trabajo.empleado_id=$empleado_id ORDER BY empleado_trabajo.id DESC LIMIT 0,1";
        $rs = mysql_fetch_array(mysql_query($sql));
        ?>
        <?php echo $rs['sector1'] != '' ? $rs['sector1'] : ''?> <?php echo $rs['sector1'] != '' ? $rs['porcentaje_sector_1']."%" : ''?> 
        <?php echo $rs['sector2'] != '' ? " - ".$rs['sector2'] : ''?> <?php echo $rs['sector2'] != '' ? $rs['porcentaje_sector_2']."%" : ''?>
        </p>
        
        <p><strong>Salario acordado del mes:</strong></p>
        <?php 
        $sql = "SELECT * FROM empleado_sueldo WHERE empleado_id = $empleado_id AND mes = $mes AND ano = $ano ORDER BY sueldo_id DESC LIMIT 0,1";
        $rs = mysql_fetch_array(mysql_query($sql));
        ?>
        Sueldo: $<?php echo $rs['sueldo']?> <br>
        Viaticos: $<?php echo $rs['viaticos']?> <br>
        Asignaciones: $<?php echo $rs['asignaciones']?> <br>
        Presentismo: $<?php echo $rs['presentismo']?> <br>
        Aguinaldo: $<?php echo $rs['aguinaldo']?> <br>
        <?php  $salario = $rs['sueldo'] + $rs['viaticos'] + $rs['asignaciones'] + $rs['presentismo'] + $rs['aguinaldo']; ?>
        Total: $<?php echo $salario?>
        
        <p><strong>Horas extras aprobadas:</strong></p>
        <?php 
            $sql = "
            SELECT
                ehe.*,
                she.*,
                s.sector
            FROM 
                empleado_hora_extra ehe INNER JOIN sector_horas_extras she ON ehe.hora_extra_id = she.id INNER JOIN sector s ON she.sector_id = s.id 
            WHERE 
                ehe.empleado_id = $empleado_id 
                AND 
                ehe.estado = 1 
                AND
                ehe.mes = $mes 
                AND 
                ehe.ano = $ano
            ";
            $rsTemp = mysql_query($sql); echo mysql_error();
            if(mysql_num_rows($rsTemp) > 0) {
                while($rs = mysql_fetch_array($rsTemp)){ ?>
                <?php echo $rs['sector']?>: <?php echo $rs['cantidad_solicitada']?> hrs. solicitadas - <?php echo $rs['cantidad_aprobada']?> hrs. aprobadas = $<?php echo $rs['cantidad_aprobada']*$rs['valor']?> <br>
                <?php  $horas_extras = $horas_extras + ($rs['cantidad_aprobada']*$rs['valor']); ?>
                <?php  } ?>
            <?php  }else{ ?>
                No se han cargado horas extras
            <?php  } ?>
        </p>
        
        <p><strong>Adelantos otorgados:</strong></p>
        <?php 
            $sql = "SELECT empleado_adelanto.id,empleado_adelanto.creado, empleado_adelanto.monto, empleado_adelanto.comentarios, CONCAT(usuario.apellido,', ',usuario.nombre) as user, caja.caja, cuenta.sucursal, cuenta.nombre, banco.banco  FROM empleado_adelanto LEFT JOIN usuario ON empleado_adelanto.creado_por = usuario.id LEFT JOIN rel_pago_operacion rpo ON empleado_adelanto.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_adelanto' LEFT JOIN efectivo_consumo ec ON rpo.forma_pago_id = ec.id AND rpo.forma_pago = 'efectivo' LEFT JOIN caja_movimiento cm ON cm.registro_id = ec.id AND cm.origen = 'efectivo_consumo' LEFT JOIN caja ON cm.caja_id = caja.id LEFT JOIN rel_pago_operacion ON empleado_adelanto.id = rel_pago_operacion.operacion_id AND rel_pago_operacion.operacion_tipo = 'sueldo_adelanto' LEFT JOIN cuenta_movimiento ON rel_pago_operacion.forma_pago=cuenta_movimiento.origen AND rel_pago_operacion.forma_pago_id = cuenta_movimiento.registro_id LEFT JOIN cheque_consumo ON cuenta_movimiento.registro_id = cheque_consumo.id AND cuenta_movimiento.origen = 'cheque' LEFT JOIN cuenta ON cuenta_movimiento.cuenta_id = cuenta.id LEFT JOIN banco ON cuenta.banco_id = banco.id WHERE empleado_adelanto.empleado_id = $empleado_id AND empleado_adelanto.mes = $mes AND empleado_adelanto.ano = $ano";
            $rsTemp = mysql_query($sql);
            if(mysql_num_rows($rsTemp) > 0) {
                while($rs = mysql_fetch_array($rsTemp)){ ?>
                <?php 
                $caja = ($rs['caja'])? 'Caja: '.$rs['caja']:'';
        $cuenta = ($rs['sucursal'])? 'Cuenta: '.$rs['banco'].' ('.$rs['sucursal'].') '.$rs['nombre']:'';
                echo fechavista($rs['creado'])?> $<?php echo number_format($rs['monto'], 2, ',', '.')?> <?php echo $rs['comentarios']?> Abonado por: <?php echo $rs['user'].' '.$caja.' '.$cuenta?>  <button class="button" onClick="window.open('reciboPDF.php?id=<?php echo $rs['id']?>&adelanto=1&copia=1');">Recibo</button><button class="button" onClick="borrar_adelanto('<?php echo $rs['id']?>');">Anular</button><br>
                <?php  $adelantos = $adelantos + $rs['monto']; ?>
                <?php  } ?>
            <?php  }else{ ?>
                No se han otorgado adelantos
            <?php  } ?>
        </p>
        <?php 
        $sql = "SELECT empleado_pago.id, empleado_pago.abonado, empleado_pago.descuentos, 
empleado_pago.motivo_descuentos, empleado_pago.monto, CONCAT(usuario.apellido,', ',usuario.nombre) as user
FROM empleado_pago LEFT JOIN usuario ON empleado_pago.abonado_por = usuario.id 
WHERE empleado_pago.empleado_id = $empleado_id AND empleado_pago.mes = $mes AND empleado_pago.ano = $ano";
        if(mysql_num_rows(mysql_query($sql)) == 0){ ?>
        <p><strong>Pendiente de pago:</strong> $<?php echo $salario+$horas_extras-$adelantos?></p>
        <?php  }else{ 
         $rsSueldo = mysql_fetch_array(mysql_query($sql));?>
         <p><strong>Descuentos:</strong></p>
         <?php if ($rsSueldo['descuentos']) {?>
         	
        		<?php  echo '$'.$rsSueldo['descuentos'].' Motivo:'.$rsSueldo['motivo_descuentos'] ; 
         }
         else{ ?>
                No se han realizado descuentos
            <?php  } ?>
         
         
        
        <p><strong>Sueldo abonado:</strong></p>
        <?php 
        echo fechavista($rsSueldo['abonado'])?> $<?php number_format($rsSueldo['monto'], 2, ',', '.');?> Abonado por: <?php echo $rsSueldo['user'];
       $sql = "SELECT empleado_pago.id, empleado_pago.abonado, 
 caja.caja, empleado_pago.descuentos, 
empleado_pago.motivo_descuentos, ec.monto
FROM empleado_pago
LEFT JOIN rel_pago_operacion rpo ON empleado_pago.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_pago' AND rpo.forma_pago = 'efectivo'  
LEFT JOIN efectivo_consumo ec ON rpo.forma_pago_id = ec.id 
LEFT JOIN caja_movimiento cm ON cm.registro_id = ec.id AND cm.origen = 'efectivo_consumo' 
LEFT JOIN caja ON cm.caja_id = caja.id 
WHERE empleado_pago.empleado_id = $empleado_id AND empleado_pago.mes = $mes AND empleado_pago.ano = $ano";
       $rsTemp = mysql_query($sql);
       if(mysql_num_rows($rsTemp) > 0) {
       		while($rsSueldo1 = mysql_fetch_array($rsTemp)){ 
       			if ($rsSueldo1['monto']) {
			        $caja = ($rsSueldo1['caja'])? 'Caja: '.$rsSueldo1['caja']:'';
			        $cuenta = ($rsSueldo1['sucursal'])? 'Cuenta: '.$rsSueldo1['banco'].' ('.$rsSueldo1['sucursal'].') '.$rsSueldo1['nombre']:'';
			        ?><br> En efectivo $<?php echo number_format($rsSueldo1['monto'], 2, ',', '.').' '.$caja.' '.$cuenta;
       			}
       		}
       }
        $sql = "SELECT empleado_pago.id, empleado_pago.abonado, empleado_pago.descuentos, 
empleado_pago.motivo_descuentos, ec.monto
FROM empleado_pago 
LEFT JOIN rel_pago_operacion rpo ON empleado_pago.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_pago' AND rpo.forma_pago = 'cheque'  
LEFT JOIN cheque_consumo ec ON rpo.forma_pago_id = ec.id 
WHERE empleado_pago.empleado_id = $empleado_id AND empleado_pago.mes = $mes AND empleado_pago.ano = $ano";
        
        $rsTemp = mysql_query($sql);
       if(mysql_num_rows($rsTemp) > 0) {
       		while($rsSueldo2 = mysql_fetch_array($rsTemp)){ 
	        if ($rsSueldo2['monto']) {
	        	echo "<br>"?> Con cheque $<?php echo number_format($rsSueldo2['monto'], 2, ',', '.');
	        }
	        
       		}
       }
        $sql = "SELECT empleado_pago.id, empleado_pago.abonado, empleado_pago.descuentos, 
empleado_pago.motivo_descuentos, ec.monto
FROM empleado_pago 
LEFT JOIN rel_pago_operacion rpo ON empleado_pago.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_pago' AND rpo.forma_pago = 'transferencia'  
LEFT JOIN transferencia_consumo ec ON rpo.forma_pago_id = ec.id 
WHERE empleado_pago.empleado_id = $empleado_id AND empleado_pago.mes = $mes AND empleado_pago.ano = $ano";
        $rsTemp = mysql_query($sql);
       if(mysql_num_rows($rsTemp) > 0) {
       		while($rsSueldo3 = mysql_fetch_array($rsTemp)){ 
       			if ($rsSueldo3['monto']) {
	        		echo "<br>"?> Con transferencia $<?php echo number_format($rsSueldo3['monto'], 2, ',', '.');
       			}
	       	}
       }
        $sql = "SELECT empleado_pago.id, empleado_pago.abonado, empleado_pago.descuentos, 
empleado_pago.motivo_descuentos, (-1)*cuenta_movimiento.monto as monto, cuenta.sucursal, cuenta.nombre, banco.banco
FROM empleado_pago 
LEFT JOIN rel_pago_operacion rpo ON empleado_pago.id = rpo.operacion_id AND rpo.operacion_tipo = 'sueldo_pago' AND rpo.forma_pago = 'debito' LEFT JOIN cuenta_movimiento ON  rpo.forma_pago_id = cuenta_movimiento.id 
LEFT JOIN cuenta ON cuenta_movimiento.cuenta_id = cuenta.id
LEFT JOIN banco ON cuenta.banco_id = banco.id
WHERE empleado_pago.empleado_id = $empleado_id AND empleado_pago.mes = $mes AND empleado_pago.ano = $ano";
       $rsTemp = mysql_query($sql);
       if(mysql_num_rows($rsTemp) > 0) {
       		while($rsSueldo4 = mysql_fetch_array($rsTemp)){ 
       			if ($rsSueldo4['monto']) {
			        $caja = ($rsSueldo4['caja'])? 'Caja: '.$rsSueldo4['caja']:'';
			        $cuenta = ($rsSueldo4['sucursal'])? 'Cuenta: '.$rsSueldo4['banco'].' ('.$rsSueldo4['sucursal'].') '.$rsSueldo4['nombre']:'';
			        echo "<br>"?> Con debito $<?php echo number_format($rsSueldo4['monto'], 2, ',', '.').' '.$caja.' '.$cuenta;
       			}
	       	}
       }
       ?>
        <br><br><center><button class="button" onClick="window.open('reciboPDF.php?id=<?php echo $rsSueldo['id']?>&copia=1');">Recibo</button></center>
		<?php  } ?>
	    
        
        
       

</div>
</body>
</html>
