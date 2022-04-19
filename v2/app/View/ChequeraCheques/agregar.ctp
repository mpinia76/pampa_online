<?php

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_cheque_consumo").getPosition();
    xpos = position[0];
    ypos = position[1];
');


//formulario
echo $this->Form->create(null, array('url' => '/chequera_cheques/agregar','inputDefaults' => (array('div' => 'ym-gbox'))));

?>
<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.cuenta_id',array('label' => 'Banco/Cuenta','empty' => 'Seleccionar', 'type'=>'select', 'options' => $cuentas));?></div>
	
	
</div>
<div class="ym-grid">
	
	<div class="ym-g25 ym-gl"><?php echo $this->Form->input('Chequera.chequera_id',array('label' => 'Chequera','empty' => 'Seleccionar', 'type'=>'select'));?></div>
	
</div>
<div class="ym-grid" style="padding:5px; display:none" id="divCheques">

</div>
<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.concepto',array('label' => 'Concepto','empty' => 'Seleccionar', 'type'=>'select', 'options' => $conceptos));?></div>
	
	
</div>
<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.obs',array('label' => 'Descripcion', 'type'=>'text','maxlength'=>'20'));?></div>
	
	
</div>

<span id="botonGuardar" onclick="guardar('<?php echo $this->Html->url('/cheque_consumos/guardar.json', true);?>',$('form').serialize(),'w_cheque_consumo');" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<span id="botonGuardarError" class="boton guardar" style="display:none">Procesando...</span>

<?php echo $this->Form->end(); ?>

<script>



$('#ChequeraCuentaId').change(function(){
	
    if($(this).val()!=''){
    	$('#divCheques').html('');
         $('#divCheques').hide();
        $.ajax({
            url: '<?php echo $this->Html->url('/chequeras/getChequeras/', true);?>'+$(this).val(),
            dataType: 'html',
            
            success: function(data){
                $('#ChequeraChequeraId').html(data);
            }
        });
    }else{
         $('#ChequeraChequeraId').html('');
    }
})

$('#ChequeraChequeraId').change(function(){
	
    if($(this).val()!=''){
        $.ajax({
            url: '<?php echo $this->Html->url('/chequera_cheques/getCheques/', true);?>'+$(this).val()+'/1/""/5',
            dataType: 'html',
            
            success: function(data){
                $('#divCheques').html(data);
                $('#divCheques').show();
            }
        });
    }else{
         $('#divCheques').html('');
         $('#divCheques').hide();
    }
})

</script>

