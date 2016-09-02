<table width="100%">
    <tr>
        <td>&nbsp;</td>
        <td>Ventas Netas</td>
        <?php foreach($cobrado as $tiempo => $monto){ ?>
        <td><?php echo $meses[substr($tiempo, 2, 2)];?> 20<?php echo substr($tiempo, 0, 2)?></td>
        <?php } ?>
        <td>Total Cobrado</td>
        <td>Pendiente</td>
    </tr>
    <tr>
        <td><?php echo $meses[$mes]?> <?php echo $ano?></td>
        <td><?php echo $ventas_netas?></td>
        <?php foreach($cobrado as $tiempo => $monto){ ?>
        <td><?php echo $monto?></td>
        <?php } ?>
        <td><?php echo array_sum($cobrado)?></td>
        <td><?php echo round(round($ventas_netas,2) - round(array_sum($cobrado),2),2);?></td>
    </tr>
</table>
