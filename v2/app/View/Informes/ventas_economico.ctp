    <table width="100%" cellspacing="0">
    <tr class="titulo">
        <td width="150"><?php echo $ano?></td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td  width="90""><?php echo $meses[$i]?></td>
        <?php } ?>
        <td width="90">Total</td>
    </tr>
    <tr class="contenido">
        <td class="mes">Ventas alojamiento totales</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $alojamientos[$i]?></td>
        <?php } ?>
        <td><?php echo array_sum($alojamientos);?></td>
    </tr>
    <?php foreach($adelantadas_rubro as $rubro_id=>$valores){ 
        if(array_sum($valores) > 0){ ?>
        <tr class="contenido extras_adelantados">
            <td align="left"><?php echo $extra_rubros[$rubro_id]?></td>
            <?php for($i=1; $i<=12; $i++){ ?>
            <td><?php echo $valores[$i]?></td>
            <?php } ?>
            <td><?php echo array_sum($valores)?></td>
        </tr>
    <?php }} ?>
    <tr class="contenido extras adelantados_totales">
        <td class="mes link">Extras adelantados totales</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $adelantadas[$i]?></td>
        <?php } ?>
        <td><?php echo array_sum($adelantadas);?></td>
    </tr>
    <?php foreach($no_adelantadas_rubro as $rubro_id=>$valores){ 
        if(array_sum($valores) > 0){ ?>
        <tr class="contenido extras_no_adelantados">
            <td align="left"><?php echo $extra_rubros[$rubro_id]?></td>
            <?php for($i=1; $i<=12; $i++){ ?>
            <td><?php echo $valores[$i]?></td>
            <?php } ?>
            <td><?php echo array_sum($valores)?></td>
        </tr>
    <?php }} ?>
    <tr class="contenido extras no_adelantados_totales">
        <td class="mes link">Extras no adelantados totales</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $no_adelantadas[$i]?></td>
        <?php } ?>
        <td><?php echo array_sum($no_adelantadas);?></td>
    </tr>
    <tr class="contenido extras_totales">
        <td class="mes link">Extras totales</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $adelantadas[$i] + $no_adelantadas[$i]?></td>
        <?php } ?>
        <td><?php echo array_sum($adelantadas)+array_sum($no_adelantadas);?></td>
    </tr>
    <tr class="contenido">
        <td class="total" align="left">Tarifa Bruta Total</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td class="total" ><?php echo $alojamientos[$i] + $adelantadas[$i] + $no_adelantadas[$i]?></td>
        <?php } ?>
        <td class="total"><?php echo array_sum($alojamientos) + array_sum($adelantadas) + array_sum($no_adelantadas)?></td>
    </tr>
    <tr class="contenido">
        <td class="mes">Intereses cobrados</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $intereses[$i]?></td>
        <?php } ?>
        <td><?php echo array_sum($intereses);?></td>
    </tr>
    <tr class="contenido">
        <td class="mes">Descuentos com.</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $descuentos[$i]?></td>
        <?php } ?>
        <td><?php echo array_sum($descuentos);?></td>
    </tr>
    <tr class="contenido">
        <td class="mes">Descuentos por Tarjeta</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $descuentos_tarjetas[$i]?></td>
        <?php } ?>
        <td><?php echo array_sum($descuentos_tarjetas);?></td>
    </tr>
    <!--<tr class="contenido">
        <td class="mes">Devoluciones</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $devoluciones[$i]?></td>
        <?php } ?>
        <td><?php echo array_sum($devoluciones);?></td>
    </tr> -->
    <tr class="contenido">
        <td class="total" align="left">Ventas netas</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td class="total"><?php echo $ventas_netas[$i]?></td>
        <?php } ?>
        <td class="total"><?php echo array_sum($ventas_netas);?></td>
    </tr>
    <tr class="contenido">
        <td class="mes">Capacidad maxima</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $capacidad_total[$i]?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <tr class="contenido">
        <td class="mes">Deptos. ocupados</td>
        <?php 
        $cant = 0;
        $total = 0;
        for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $capacidad_ocupada[$i]?></td>
        <?php 
        	if($capacidad_ocupada[$i]!=0){
        		$cant++;
        		$total += $capacidad_ocupada[$i];
        	}
        } ?>
        <td><?php 
        if($cant>0){
        	echo round(($total/$cant),2);
        }?>
        </td>
    </tr>
    <tr class="contenido">
        <td class="mes">Deptos. disponibles</td>
        <?php 
         $cant = 0;
        $total = 0;
        for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $capacidad_total[$i] - $capacidad_ocupada[$i]?></td>
        <?php 
        	if(($capacidad_total[$i] - $capacidad_ocupada[$i])>0){
        		$cant++;
        		$total += ($capacidad_total[$i] - $capacidad_ocupada[$i]);
        	}
        } ?>
        <td><?php 
        if($cant){
        	echo round(($total/$cant),2);
        }
        ?></td>
    </tr>
    <tr class="contenido">
        <td class="mes">Ocupacion %</td>
        <?php 
         $cant = 0;
        $total = 0;
        for($i=1; $i<=12; $i++){ ?>
        <td><?php 
        	if($capacidad_total[$i]){
        		echo number_format(($capacidad_ocupada[$i] / $capacidad_total[$i])*100,2);
        	}
        	else {
        		echo '0';
        	}?>%</td>
        <?php 
        	if($capacidad_total[$i]){
	        	if((($capacidad_ocupada[$i] / $capacidad_total[$i])*100)>0){
	        		$cant++;
	        		$total += (($capacidad_ocupada[$i] / $capacidad_total[$i])*100);
	        	}
	        }
	        
        } ?>
        <td><?php 
        if($cant>0){
        	echo round(($total/$cant),2);
        }?>
        </td>
    </tr>
    <tr class="contenido">
        <td class="mes">Tarifa media diaria por dpto ocupado</td>
        <?php for($i=1; $i<=12; $i++){ 
            if($capacidad_ocupada[$i] > 0){ ?>
            <td><?php echo number_format($ventas_netas[$i]/$capacidad_ocupada[$i],2)?></td>
        <?php }else{ ?>
            <td>0</td>
        <?php }} ?>
            <td>&nbsp;</td>
    </tr>
    <!--<tr class="contenido">
        <td class="mes">Tarifa media diaria / departamento</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php echo number_format($ventas_netas[$i] / ($q_apartamentos * 30) ,2)?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>-->
</table>

<p>&nbsp;</p>

<table width="100%" cellspacing="0">
    <tr class="titulo">
        <td><?php echo $ano?></td>
        <td>&nbsp;</td>
        <td colspan="<?php echo count($cajas)+2?>">Efectivo</td>
        <td colspan="<?php echo count($cuentas)+2?>">Desposito y Transferencias</td>
        <td colspan="<?php echo count($posnets)+2?>">Tarjetas</td>
        <td colspan="4">Cheques de terceros</td>
        <td colspan="2">Totales</td>
    </tr>
    <tr class="titulo2">
        <td>&nbsp;</td>
        <td class="total">Ventas netas</td>
        <?php foreach($cajas as $id => $caja){ ?>
        <td><?php echo $caja?></td>
        <?php } ?>
        <td>Devoluciones</td>
        <td class="total">Total</td>
        <?php foreach($cuentas as $id => $cuenta){ ?>
        <td><?php echo $cuenta?></td>
        <?php } ?>
        <td>Devoluciones</td>
        <td class="total">Total</td>
        <?php foreach($posnets as $id => $posnet){ ?>
        <td><?php echo $posnet?></td>
        <?php } ?>
        <td class="total">Total</td>
        <td>Comunes</td>
        <td>Diferidos</td>
        <td>Devoluciones</td>
        <td class="total">Total</td>
        <td class="total2">Cobrado</td>
        <td class="total2">Pendiente de cobro</td>
    </tr>
    <?php for($j = 1; $j<=12; $j++){ ?>
    <tr class="contenido">
        <td class="mes"><?php echo $meses[$j]?></td>
        <td class="total"><?php echo $ventas_netas[$j]?></td>
        <?php 
        $total_caja[$j] = 0;
        foreach($cobro_caja as $caja){ 
            $total_caja[$j] += $caja[$j]; ?>
            <td><?php echo $caja[$j]?></td>
        <?php } ?>
            <td><?php echo $devoluciones_pago['EFECTIVO'][$j];?></td>
            <td class="total"><?php echo $total_caja[$j] + $devoluciones_pago['EFECTIVO'][$j]?></td>
        
        <?php
        $total_cuenta[$j] = 0;
        foreach($cobro_cuenta as $cuenta){
            $total_cuenta[$j] += $cuenta[$j]; ?>
            <td><?php echo $cuenta[$j]?></td>
        <?php } ?>
            <td><?php echo $devoluciones_pago['TRANSFERENCIA'][$j];?></td>
            <td class="total"><?php echo $total_cuenta[$j] + $devoluciones_pago['TRANSFERENCIA'][$j]?></td>
            
         <?php 
         $total_posnet[$j] = 0;
         foreach($cobro_posnet as $id=>$posnet){
             $total_posnet[$j] += $posnet[$j]?>
            <td><?php echo $posnet[$j]?></td>
         <?php } ?>
            <td class="total"><?php echo $total_posnet[$j]?></td>
            
         <td><?php echo $cobro_cheque['COMUN'][$j]?></td>
         <td><?php echo $cobro_cheque['DIFERIDO'][$j]?></td>
         <td><?php echo $devoluciones_pago['CHEQUE'][$j];?></td>
         <td class="total"><?php echo $cobro_cheque['COMUN'][$j] + $cobro_cheque['DIFERIDO'][$j] + $devoluciones_pago['CHEQUE'][$j]?></td>
         
         <td class="total2"><?php echo round($cobrado[$j] + $devoluciones_pago['CHEQUE'][$j] + $devoluciones_pago['EFECTIVO'][$j] + $devoluciones_pago['TRANSFERENCIA'][$j],2)?></td>
         <td class="total2"><?php echo round($ventas_netas[$j]-$cobrado[$j] - ($devoluciones_pago['CHEQUE'][$j] + $devoluciones_pago['EFECTIVO'][$j] + $devoluciones_pago['TRANSFERENCIA'][$j]),2)?></td>
    </tr>
    <?php } ?>
    <tr class="titulo">
        <td align="left" class="total2">Total</td>
        <td class="total2"><?php echo array_sum($ventas_netas)?></td>
        <?php foreach($cobro_caja as $caja){  ?>
            <td class="total2"><?php echo array_sum($caja)?></td>
        <?php } ?>
            <td class="total2"><?php echo array_sum($devoluciones_pago['EFECTIVO'])?></td>
        <td class="total2"><?php echo array_sum($total_caja) + array_sum($devoluciones_pago['EFECTIVO'])?></td>
        <?php foreach($cobro_cuenta as $cuenta){ ?>
            <td class="total2"><?php echo array_sum($cuenta)?></td>
        <?php } ?>
            <td class="total2"><?php echo array_sum($devoluciones_pago['TRANSFERENCIA'])?></td>
        <td class="total2"><?php echo array_sum($total_cuenta)  + array_sum($devoluciones_pago['TRANSFERENCIA'])?></td>
        <?php  foreach($cobro_posnet as $id => $posnet){ ?>
            <td class="total2"><?php echo array_sum($posnet)?></td>
         <?php } ?>
        <td class="total2"><?php echo array_sum($total_posnet)?></td>
        <td class="total2"><?php echo array_sum($cobro_cheque['COMUN'])?></td>
        <td class="total2"><?php echo array_sum($cobro_cheque['DIFERIDO'])?></td>
        <td class="total2"><?php echo array_sum($devoluciones_pago['CHEQUE'])?></td>
        <td class="total2"><?php echo array_sum($cobro_cheque['COMUN']) + array_sum($cobro_cheque['DIFERIDO']) + array_sum($devoluciones_pago['CHEQUE'])?></td>
        <td class="total2"><?php echo round(array_sum($cobrado) + array_sum($devoluciones_pago['CHEQUE']) + array_sum($devoluciones_pago['TRANSFERENCIA']) + array_sum($devoluciones_pago['EFECTIVO']),2)?></td>
        <td class="total2"><?php echo round(array_sum($ventas_netas) - array_sum($cobrado) - (array_sum($devoluciones_pago['CHEQUE']) + array_sum($devoluciones_pago['TRANSFERENCIA']) + array_sum($devoluciones_pago['EFECTIVO'])),2)?></td>
    </tr>
</table>

<script>
$('tr').mouseover(function(){
    $('tr').removeClass('hover');
    $(this).addClass('hover');
});
$('.extras_totales').click(function(){
    $('.extras').toggle()
})
$('.adelantados_totales').click(function(){
    $('.extras_adelantados').toggle()
})
$('.no_adelantados_totales').click(function(){
    $('.extras_no_adelantados').toggle()
})
</script>