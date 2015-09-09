<table width="100%">
    <tr>
        <td>&nbsp;</td>
        <td>Ventas Netas</td>
        <?php foreach($cobrado as $tiempo => $monto){ ?>
        <td><?=$meses[substr($tiempo, 2, 2)];?> 20<?=substr($tiempo, 0, 2)?></td>
        <? } ?>
        <td>Total Cobrado</td>
        <td>Pendiente</td>
    </tr>
    <tr>
        <td><?=$meses[$mes]?> <?=$ano?></td>
        <td><?=$ventas_netas?></td>
        <?php foreach($cobrado as $tiempo => $monto){ ?>
        <td><?=$monto?></td>
        <? } ?>
        <td><?=array_sum($cobrado)?></td>
        <td><?=$ventas_netas - array_sum($cobrado);?></td>
    </tr>
</table>
