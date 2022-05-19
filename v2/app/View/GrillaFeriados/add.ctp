<?php
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');

//formulario
echo $this->Form->create(null, array('url' => '/grilla_feriados/add','inputDefaults' => (array('div' => 'ym-gbox'))));

?>

<div class="ym-grid">
    <div class="ym-g33 ym-gl"><?php echo $this->Form->input('GrillaFeriado.nombre');?></div>
    <div class="ym-g33 ym-gl"><?php echo $this->Form->input('GrillaFeriado.desde',array('label'=>'Desde','class'=>'datepicker','type'=>'text'));?></div>
    <div class="ym-g33 ym-gl"><?php echo $this->Form->input('GrillaFeriado.hasta',array('label'=>'Hasta','class'=>'datepicker','type'=>'text'));?></div>
</div>










<span id="botonGuardar" onclick="guardar('<?php echo $this->Html->url('/grilla_feriados/guardar.json', true);?>',$('form').serialize(),{id:'w_grilla_feriados',url:'v2/grilla_feriados/index'});" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<span id="botonGuardarError" class="boton guardar" style="display:none">Procesando...</span>
<!--<span id="botonEliminar" onclick="eliminar();" class="boton guardar">Eliminar</span>-->
<?php echo $this->Form->end(); ?>

