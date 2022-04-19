<?php

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_cheque_consumo").getPosition();
    xpos = position[0];
    ypos = position[1];
');


//formulario
echo $this->Form->create(null, array('url' => '/chequera_cheques/editar','inputDefaults' => (array('div' => 'ym-gbox'))));

echo $this->Form->hidden('ChequeConsumo.id'); 
echo $this->Form->hidden('ChequeConsumo.cuenta_id'); 
echo $this->Form->input('Chequera.id',array('type'=>'hidden','value'=>$chequera_id));
echo $this->Form->input('Cheque.id',array('type'=>'hidden','value'=>$cheque_id));

//print_r($cheque_consumo);
?>
<div class="ym-grid">
	
	<div class="ym-g75 ym-gl"><?php echo $this->Form->input('Chequera.cuenta_id',array('label' => 'Banco/Cuenta','type'=>'text','disabled'=>true,'value'=>$cuenta['Banco']['banco'].'-'.$cuenta['Cuenta']['nombre']));?></div>
	
</div>
<div class="ym-grid">
	
	<div class="ym-g25 ym-gl"><?php echo $this->Form->input('Chequera.numero',array('label' => 'Chequera','type'=>'text','disabled'=>true,'value'=>$chequera));?></div>
	
</div>
<div class="ym-grid">
	<div class="ym-g25 ym-gl"><?php echo $this->Form->input('Cheque.numero',array('disabled'=>true,'value'=>str_pad($cheque_consumo['ChequeConsumo']['numero'], 8,'0',STR_PAD_LEFT)));?></div>
</div>
<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cheque.concepto',array('label' => 'Concepto','empty' => 'Seleccionar', 'type'=>'select', 'options' => $conceptos, 'value' => $concepto));?></div>
	
	
</div>


<span id="botonGuardar" onclick="guardar('<?php echo $this->Html->url('/cheque_consumos/desanular.json', true);?>',$('form').serialize(),'w_cheque_consumo');" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<span id="botonGuardarError" class="boton guardar" style="display:none">Procesando...</span>

<?php echo $this->Form->end(); ?>



