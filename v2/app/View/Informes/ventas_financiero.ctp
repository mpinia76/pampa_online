<table width="100%" cellspacing="0">
    <tr class="titulo">
        <td><?php echo $ano?></td>
        <td>&nbsp;</td>
        <td colspan="<?php echo count($cajas)+2?>">Efectivo</td>
        <td colspan="<?php echo count($cuentas)+2?>">Desposito y Transferencias</td>
        <td colspan="<?php echo count($posnets)+1?>">Tarjetas</td>
        <td colspan="4">Cheques de terceros</td>
    </tr>
    <tr class="titulo2">
        <td>&nbsp;</td>
        <td class="total">Cobros netos</td>
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
    </tr>
    <?php for($j = 1; $j<=12; $j++){ ?>
    <tr class="contenido">
        <td class="mes"><?php echo $meses[$j]?></td>
        <td class="total"><?php echo $cobro_neto[$j]?></td>
        <?php 
        $total_caja[$j] = 0;
        foreach($cobro_caja as $caja){ 
            $total_caja[$j] += $caja[$j]; ?>
            <td><?php echo $caja[$j]?></td>
        <?php } ?>
            <td><?php echo $devoluciones_pago['EFECTIVO'][$j]?></td>
            <td class="total"><?php echo $total_caja[$j] + $devoluciones_pago['EFECTIVO'][$j]?></td>
        
        <?php
        $total_cuenta[$j] = 0;
        foreach($cobro_cuenta as $cuenta){
            $total_cuenta[$j] += $cuenta[$j]; ?>
            <td><?php echo $cuenta[$j]?></td>
        <?php } ?>
            <td><?php echo $devoluciones_pago['TRANSFERENCIA'][$j]?></td>
            <td class="total"><?php echo $total_cuenta[$j] + $devoluciones_pago['TRANSFERENCIA'][$j]?></td>
            
         <?php 
         $total_posnets[$j] = 0;
         foreach($cobro_posnet as $posnet){
             $total_posnets[$j] += $posnet[$j]; ?>
            <td><?php echo $posnet[$j]?></td>
         <?php } ?>
            <td class="total"><?php echo $total_posnets[$j]?></td>
            
         <td><?php echo $cobro_cheque['COMUN'][$j]?></td>
         <td><?php echo $cobro_cheque['DIFERIDO'][$j]?></td>
         <td><?php echo $devoluciones_pago['CHEQUE'][$j]?></td>
         <td class="total"><?php echo $cobro_cheque['COMUN'][$j] + $cobro_cheque['DIFERIDO'][$j] + $devoluciones_pago['CHEQUE'][$j]?></td>
    </tr>
    <?php } ?>
    <tr class="titulo">
        <td align="left" class="total2">Total</td>
        <td class="total2"><?php echo array_sum($cobro_neto)?></td>
        <?php foreach($cobro_caja as $caja){  ?>
            <td class="total2"><?php echo array_sum($caja)?></td>
        <?php } ?>
        <td class="total2"><?php echo array_sum($devoluciones_pago['EFECTIVO'])?></td>
        <td class="total2"><?php echo array_sum($total_caja) + array_sum($devoluciones_pago['EFECTIVO'])?></td>
        <?php foreach($cobro_cuenta as $cuenta){ ?>
            <td class="total2"><?php echo array_sum($cuenta)?></td>
        <?php } ?>
        <td class="total2"><?php echo array_sum($devoluciones_pago['TRANSFERENCIA'])?></td>
        <td class="total2"><?php echo array_sum($total_cuenta) + array_sum($devoluciones_pago['TRANSFERENCIA'])?></td>
        <?php  foreach($cobro_posnet as $id => $posnet){ ?>
            <td class="total2"><?php echo array_sum($posnet)?></td>
         <?php } ?>
        <td class="total2"><?php echo array_sum($total_posnets)?></td>
        <td class="total2"><?php echo array_sum($cobro_cheque['COMUN'])?></td>
        <td class="total2"><?php echo array_sum($cobro_cheque['DIFERIDO'])?></td>
        <td class="total2"><?php echo array_sum($devoluciones_pago['CHEQUE'])?></td>
        <td class="total2"><?php echo array_sum($cobro_cheque['COMUN']) + array_sum($cobro_cheque['DIFERIDO']) + array_sum($devoluciones_pago['CHEQUE'])?></td>
    </tr>
</table>
<script>
$('tr').mouseover(function(){
    $('tr').removeClass('hover');
    $(this).addClass('hover');
});
</script>