<?php
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');

//formulario
echo $this->Form->create(null, array('url' => '/extras/crear','inputDefaults' => (array('div' => 'ym-gbox'))));


        echo $this->Form->hidden('ids',array('value' => $ids)); ?>
        <div class="sectionTitle">Cobro con Tarjeta</div>
        
        <?php 
        
        echo $this->Form->input('CobroTarjeta.lote_nuevo',array( 'label' => 'Lote','maxlength'=>'4'));
        echo $this->Form->input('CobroTarjeta.lote',array('label' => 'Nro liquidacion','maxlength'=>'8'));
        echo $this->Form->input('CobroTarjeta.fecha_pago',array('class'=>'datepicker','type'=>'text'));
        ?>
            <span onclick="guardar('<?php echo $this->Html->url('/cobro_tarjetas/guardarMultiple.json', true);?>',$('form').serialize(),false);" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
        

   

<?php echo $this->Form->end(); ?>