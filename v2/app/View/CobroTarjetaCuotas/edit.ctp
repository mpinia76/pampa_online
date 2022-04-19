<?php
//formulario
echo $this->Form->create(null, array('url' => '/cobro_tarjeta_cuotas/edit','inputDefaults' => (array('div' => 'ym-gbox'))));
echo $this->Form->hidden('CobroTarjetaCuota.id');
echo $this->Form->input('CobroTarjetaCuota.posnet_id',array('options' => $posnets, 'empty' => 'Seleccionar', 'type'=>'select', 'label' => 'Locacion', 'value' => $cobro_tarjeta_cuota['CobroTarjetaTipo']['cobro_tarjeta_posnet_id']));
echo '<div id="tarjeta_marcas">'.$this->Form->input('CobroTarjetaCuota.cobro_tarjeta_tipo_id',array('options' => $marcas, 'empty' => 'Seleccionar', 'type'=>'select', 'div' => 'ym-gbox', 'label' => 'Marca')).'</div>';
echo $this->Form->input('CobroTarjetaCuota.cuota',array('label' => 'Cuota','type'=>'text', 'class'=>'number'));
echo $this->Form->input('CobroTarjetaCuota.interes',array('label' => 'Coeficiente','type'=>'text', 'class'=>'number'));
echo $this->Form->end();
?>
<span onclick="guardar('<?php echo $this->Html->url('/cobro_tarjeta_cuotas/guardar.json', true);?>',$('form').serialize(),{id:'w_cobro_tarjeta_cuotas',url:'v2/cobro_tarjeta_cuotas/index'});" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<script>
$('#CobroTarjetaCuotaPosnetId').change(function(){
    if($(this).val()!=''){
        $.ajax({
            url: '<?php echo $this->Html->url('/cobro_tarjeta_tipos/getMarcas/', true);?>'+$(this).val(),
            dataType: 'html',
            data: {'model' : 'CobroTarjetaCuota'},
            success: function(data){
                $('#tarjeta_marcas').html(data);
            }
        });
    }else{
         $('#tarjeta_marcas').html('');
    }
})
</script>