<?php
$i = rand(100,10000);
if(isset($reserva_extra_id)){ ?>
    <tr class="border_bottom" id="ReservaExtra<?php echo $reserva_extra_id?>">
<?php }else{ ?>
    <tr class="border_bottom" id="Extra<?php echo $i?>">
<?php } ?>
    <td width="25%">
        <input type="hidden" name="data[ReservaExtraCounter][]" value="<?php echo $i?>"/>
        <input type="hidden" name="data[ReservaExtraId][]" value="<?php echo $extra['Extra']['id']?>"/>
        <input type="hidden" name="data[ReservaExtraCantidad][]" value="<?php echo $cantidad?>"/>
        <input type="hidden" name="data[ReservaExtraPrecio][]" value="<?php echo $extra['Extra']['tarifa']?>"/>
        <?php echo $extra['ExtraRubro']['rubro']?>
    </td>
    <td><?php echo $extra['ExtraSubrubro']['subrubro']?> <?php echo $extra['Extra']['detalle']?></td>
    <td align="right" width="100"><span class="extra_cantidad"><?php echo $cantidad?> x $<span class="extra_tarifa"><?php echo $extra['Extra']['tarifa']?></span></td>
    <td align="right" width="50">$<?php echo $cantidad*$extra['Extra']['tarifa']?></td>
<?php if(isset($reserva_extra_id)){ ?>
    <td align="right" width="50"><a onclick=" quitarExtra('<?php echo $reserva_extra_id;?>');">quitar</a></td>
<?php }else{ ?>
    <td align="right" width="50"><a onclick="$('#Extra<?php echo $i?>').remove(); updateTotal();">quitar</a></td>
<?php } ?>
</tr>
<script>updateTotal();</script>