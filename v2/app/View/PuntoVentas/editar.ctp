<?php

//formulario
echo $this->Form->create(null, array('url' => '/puntoVentas/crear','inputDefaults' => (array('div' => 'ym-gbox'))));

?>
<?php echo $this->Form->hidden('PuntoVenta.id'); ?>


<div class="ym-grid">
	<div class="ym-g25 ym-gl"><?php echo $this->Form->input('PuntoVenta.cuit');?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('PuntoVenta.numero',array('type' => 'text', 'class' => 'number')); ?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('PuntoVenta.alicuota',array('type' => 'text', 'class' => 'number')); ?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('PuntoVenta.descripcion'); ?></div>
</div>
<div class="ym-grid">
	<div class="ym-g75 ym-gl"><?php echo $this->Form->input('PuntoVenta.direccion');?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('PuntoVenta.ivaVentas'); ?></div>
    
</div>





<span id="botonGuardar" onclick="guardar('<?php echo $this->Html->url('/puntoVentas/guardar.json', true);?>',$('form').serialize(),{id:'w_puntoVentas',url:'v2/puntoVentas/index'});" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<?php echo $this->Form->end(); ?>

<script>

</script>