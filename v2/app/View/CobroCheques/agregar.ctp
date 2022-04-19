<div class="ym-grid border_bottom">
    <div class="ym-gbox"><?php echo $reserva_cobro['fecha']?> - CHEQUE</div>
</div>
<?php echo $this->Form->hidden('CobroCheque.monto_neto',array('value' => $reserva_cobro['monto_neto'])); ?>
<div class="ym-grid">
    <div class="ym-g33 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroCheque.tipo',array( 'type' => 'select', 'options' => $tipos, 'empty' => 'Seleccionar...')); ?></div></div>
    <div class="ym-g33 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroCheque.banco'); ?></div></div>
    <div class="ym-g33 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroCheque.numero'); ?></div></div>
</div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroCheque.librado_por'); ?></div></div>
    <div class="ym-g50 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroCheque.cuit',array('label' => 'Cuit / Cuil')); ?></div></div>
</div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroCheque.a_la_orden_de', array('label' => 'A la orden de')); ?></div></div>
    <div class="ym-g50 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroCheque.fecha_cobro',array('label' => 'Fecha de cobro', 'type' => 'text', 'class' => 'datepicker')); ?></div></div>
</div>
<div class="ym-grid" style="text-align: right;">
    <div class="ym-gbox">Libre de intereses: $<span id="monto_neto" style="font-weight: bold;"><?php echo $reserva_cobro['monto_neto']; ?></span></div>
    <div class="ym-gbox">Intereses: $ <input onkeyup="actualizaTotal();" id="CobroChequeInteres" type="text" class="number" value="0" name="data[CobroCheque][interes]" /></div>
    <div class="ym-gbox">Total a cobrar: $<span id="total" style="font-weight: bold;"></span></div>
</div>
<span id="botonGuardar" onclick="guardar('<?php echo $this->Html->url('/reserva_cobros/guardar.json', true);?>',$('form').serialize(),'w_reservas');" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<span id="botonGuardarError" class="boton guardar" style="display:none">Procesando...</span>
<div class="ym-gbox" style="text-align: center;"><a onclick="location.reload();">cancelar</a></div>
<script>
function actualizaTotal(){
    var total = parseFloat($('#monto_neto').html()) + parseFloat($('#CobroChequeInteres').val());
    $('#total').html(total);
}
actualizaTotal();
$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });
</script>