<div class="sectionSubtitle">Agregar descuento</div>
<div class="ym-grid" id="forma_cobro">
    <div class="ym-g25 ym-gl">
        <div class="ym-gbox"><?php echo $this->Form->input('ReservaDescuento.fecha',array('class' => 'datepicker', 'type' => 'text', 'value' => date('d/m/Y'))); ?></div>
    </div>
    <div class="ym-g25 ym-gl">
        <div class="ym-gbox"><?php echo $this->Form->input('ReservaDescuento.motivo'); ?></div>
    </div>
    <div class="ym-g25 ym-gl">
        <div class="ym-gbox"><?php echo $this->Form->input('ReservaDescuento.monto',array('class' => 'number', 'type' => 'text', 'label' => 'Monto $')); ?></div>
    </div>
    <div class="ym-g25 ym-gl">
        <span id="btn_agregar_cobro" style="margin-top: 15px;" class="boton agregar" onclick="guardar('<?php echo $this->Html->url('/reserva_descuentos/guardar.json', true);?>',$('form').serialize());">+ agregar</span>
    </div>
</div>
<div class="ym-gbox" style="text-align: center;"><a onclick="location.reload();">cancelar</a></div>