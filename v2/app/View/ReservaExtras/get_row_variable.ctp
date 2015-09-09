<tr class="border_bottom" id="ReservaExtra<?php echo $reserva_extra_id?>">
    <td width="25%">
        <input type="hidden" name="data[ReservaExtraCounter][]" value=""/>
        <input type="hidden" name="data[ReservaExtraVariableDetalle][]" value="<?php echo $precio?>"/>
        <input type="hidden" name="data[ReservaExtraVariablePrecio][]" value="<?php echo $precio?>"/>
        <input type="hidden" name="data[ReservaExtraVariableRubroId][]" value="<?php echo $rubro['ExtraRubro']['id']?>"/>
        <?php echo $rubro['ExtraRubro']['rubro']?>
    </td>
    <td colspan="2"><?php echo $detalle?></td>
    <td align="right" width="50">$<span class="extra_tarifa"><?php echo $precio?></span></td>
    <td align="right" width="50"><a onclick=" quitarExtra('<?php echo $reserva_extra_id;?>');">quitar</a></td>
</tr>
<script>updateTotal();</script>