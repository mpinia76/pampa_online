<?php
//formulario
echo $this->Form->create(null, array('url' => '/cobro_tarjeta_lote/acreditar','inputDefaults' => (array('div' => 'ym-gbox'))));
echo $this->Form->hidden('CobroTarjetaLote.id');
echo $this->Form->input('CobroTarjetaLote.monto_total',array('label' => 'Monto Total $','type'=>'text', 'class'=>'number'));
echo $this->Form->input('CobroTarjetaLote.descuentos',array('label' => 'Descuentos $','type'=>'text', 'class'=>'number'));
echo $this->Form->end();
?>
<span onclick="guardar('<?php echo $this->Html->url('/cobro_tarjeta_cuotas/guardar.json', true);?>',$('form').serialize(),{id:'w_cobro_tarjeta_lotes',url:'v2/cobro_tarjeta_lotes/index'});" class="boton guardar">Acreditar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
