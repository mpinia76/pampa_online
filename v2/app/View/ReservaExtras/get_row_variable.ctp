<?php
if(isset($error)) {
    echo 'La fecha de consumo esta fuera de rango';
}
else{ ?>
<tr class="border_bottom" id="ReservaExtra<?php echo $reserva_extra_id?>">
    <td width="25%">
        <input type="hidden" name="data[ReservaExtraCounter][]" value=""/>
        <input type="hidden" name="data[ReservaExtraVariableConsumida][]" value="<?php echo $consumida?>"/>
        <input type="hidden" name="data[ReservaExtraVariableDetalle][]" value="<?php echo $detalle?>"/>
        <input type="hidden" name="data[ReservaExtraVariablePrecio][]" value="<?php echo $precio?>"/>
        <input type="hidden" name="data[ReservaExtraVariableRubroId][]" value="<?php echo $rubro['ExtraRubro']['id']?>"/>
        <input type="hidden" name="data[ReservaExtraVariableUsuario][]" value="<?php echo $extra['ReservaExtra']['usuario_id']?>"/>
        <?php echo $consumida?>
    </td>

    <td width="25%"><?php echo $rubro['ExtraRubro']['rubro']?></td>
    <td colspan="2"><?php echo $detalle?></td>
    <td align="right" width="50">$<span class="extra_tarifa"><?php echo $precio?></span></td>
    <td align="right" width="50"><?php echo $_SESSION['usernombre']?></td>
    <td align="right" width="50"><a onclick=" quitarExtra('<?php echo $reserva_extra_id;?>');">quitar</a></td>
</tr>
<script>updateTotal();</script>
<?php } ?>
