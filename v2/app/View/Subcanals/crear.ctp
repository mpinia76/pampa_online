<?php



//formulario
echo $this->Form->create(null, array('url' => '/subcanals/crear','inputDefaults' => (array('div' => 'ym-gbox'))));

?>



<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Subcanal.canal_id',array('label' => 'Canal de venta','empty' => 'Seleccionar', 'type'=>'select'));?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Subcanal.subcanal',array('label' => 'Subcanal de venta'));?></div>
</div>




<span onclick="guardar('guardar.json',$('form').serialize(),{id:'w_subcanals',url:'v2/subcanals/index'});" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<?php echo $this->Form->end(); ?>

<script>

</script>