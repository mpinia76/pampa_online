<div class="ym-grid border_bottom">
    <div class="ym-gbox"><?php echo $reserva_cobro['fecha']?> - EFECTIVO</div>
</div>
<?php echo $this->Form->hidden('CobroEfectivo.monto_neto',array('value' => $reserva_cobro['monto_neto'])); ?>
<div class="ym-grid">
    <div class="ym-gbox"><?php echo $this->Form->input('CobroEfectivo.caja_id',array( 'label' => 'Caja de ingreso', 'empty' => 'Seleccionar...')); ?></div>
</div>
<div class="ym-grid" style="text-align: right;">
    <div class="ym-gbox">Total a cobrar: $<span id="total" style="font-weight: bold;"><?php echo $reserva_cobro['monto_neto']; ?></span></div>
</div>
<?php echo $this->Form->hidden('CobroEfectivo.interes',array('value' => 0)); ?>
<span onclick="guardar('<?php echo $this->Html->url('/reserva_cobros/guardar.json', true);?>',$('form').serialize(),{id:'w_reservas',url:'/v2/reservas/index'});" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<div class="ym-gbox" style="text-align: center;"><a onclick="location.reload();">cancelar</a></div>
