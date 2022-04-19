<?php

$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');

//formulario
echo $this->Form->create(null, array('url' => '/reserva_facturas/crear','inputDefaults' => (array('div' => 'ym-gbox'))));

?>
<?php echo $this->Form->hidden('ReservaFactura.id'); ?>
<?php echo $this->Form->hidden('ReservaFactura.reserva_id'); ?>
<?php echo $this->Form->hidden('ReservaFactura.agregada_por'); ?>

<div class="ym-grid">
	<div class="ym-g33 ym-gl"><?php echo $this->Form->input('ReservaFactura.tipoDoc',array('options' => $tipos_doc));?></div>
	<div class="ym-g33 ym-gl"><?php echo $this->Form->input('ReservaFactura.tipo',array('options' => $facturas_tipo, 'empty' => 'Seleccionar...'));?></div>
    <div class="ym-g33 ym-gl"><?php echo $this->Form->input('ReservaFactura.fecha_emision',array('class' => 'datepicker', 'type' => 'text')); ?></div>
</div>

<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ReservaFactura.punto_venta_id',array('empty' => 'Seleccionar...', 'type'=>'select', 'options' => $puntos_venta));?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('ReservaFactura.numero',array('maxlength'=>'8'));?></div>
</div>

<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ReservaFactura.titular');?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('ReservaFactura.monto',array('type' => 'text', 'label' => 'Monto $', 'class' => 'number'));?></div>
</div>



<span id="botonGuardar" onclick="guardar('<?php echo $this->Html->url('/reserva_facturas/guardar.json', true);?>',$('form').serialize(),{id:'w_reservas_finalizar',url:'v2/reserva_cobros/finalizar/<?php echo $reserva_factura['ReservaFactura']['reserva_id']; ?>'});" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<?php echo $this->Form->end(); ?>

<script>

</script>