<?php

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_chequeras_add").getPosition();
    xpos = position[0];
    ypos = position[1];
');


//formulario
echo $this->Form->create(null, array('url' => '/chequeras/crear','inputDefaults' => (array('div' => 'ym-gbox'))));

?>
<?php echo $this->Form->hidden('Chequera.ultimo'); ?>
<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.cuenta_id',array('label' => 'Banco/Cuenta','empty' => 'Seleccionar', 'type'=>'select', 'options' => $cuentas));?></div>
	<div class="ym-gl" id="divNumero" style="padding: 5px;"><?php echo $this->Form->input('Chequera.numero',array('div' => false,'disabled' => true));?> <?php echo $this->Form->input('cambiar',array('label'=>false, 'div' => false,'type'=>'checkbox')); ?> <strong>Cambiar</strong></div>
	
	
</div>

<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.tipo',array('label' => 'Tipo de cheque', 'type'=>'select', 'options' => $tipos));?></div>
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.cantidad',array('type'=>'select', 'options' => $cantidad));?></div>
</div>

<div class="ym-grid">
	
    <div class="ym-g50 ym-gl" id="divInicio"><?php echo $this->Form->input('Chequera.inicio',array('label' => 'Nro. Inicio','maxlength'=>'8'));?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.final',array('label' => 'Nro. Final','maxlength'=>'8'));?></div>
</div>

<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.usuario_id',array('label' => 'Responsable','empty' => 'Seleccionar', 'type'=>'select', 'options' => $usuarios));?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.estado',array('type'=>'select', 'options' => $estados));?></div>
   
</div>


<span onclick="guardar('guardar.json',$('form').serialize(),{id:'w_chequeras',url:'v2/chequeras/index'});" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<?php echo $this->Form->end(); ?>

<script>
function pad(num, largo, char) {
    char = char || '0';
    num = num + '';
    return num.length >= largo ? num : new Array(largo - num.length + 1).join(char) + num;
}


$('#ChequeraCuentaId').change(function(){
	
    if($(this).val()!=''){
        $.ajax({
            url: '<?php echo $this->Html->url('/chequeras/getNumero/', true);?>'+$(this).val(),
            dataType: 'json',
            success: function(data){
            	$('#ChequeraUltimo').val(data.detalle.ultimo_nro);
                $('#ChequeraNumero').val(data.detalle.ultimo_nro);
                 $('#ChequeraInicio').val(pad(data.detalle.ultimo_rango, 8, '0'));
                 modificarFin();
            }
        });
    }
})

$('#ChequeraCantidad').change(function(){
	modificarFin();
})

$('#ChequeraNumero').change(function(){
	$('#ChequeraUltimo').val($('#ChequeraNumero').val());
})

$('#ChequeraCambiar').change(function(){
	if ($(this).is(':checked')) {
      if (confirm("Se esta alterando la correlatividad de las chequeras, Desea continuar?")){
      	$('#ChequeraNumero').prop('disabled', false);
      }
      else{
      	$( this ).prop( "checked", false );
      }
    }
    else {
    	$('#ChequeraNumero').prop('disabled', true);
    	}
})

function modificarFin(){
	var inicio = parseInt($('#ChequeraInicio').val());
	var fin = inicio +( parseInt($('#ChequeraCantidad').val())-1);
	 $('#ChequeraFinal').val(pad(fin, 8, '0'));
}

</script>

