<?php
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');



//formulario
echo $this->Form->create(null, array('url' => '/cheque_consumos/reemplazar','inputDefaults' => (array('div' => 'ym-gbox'))));

?>
<?php echo $this->Form->hidden('ChequeConsumo.id');
		echo $this->Form->hidden('ChequeConsumo.numero');
		echo $this->Form->hidden('vencio',array('value'=>$cheque_consumo['ChequeConsumo']['vencido']));
		echo $this->Form->hidden('cuenta',array('value'=>$cheque_consumo['ChequeConsumo']['cuenta_id']));
//print_r($cheque_consumo);
?>
<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.cuenta_id',array('label' => 'Banco/Cuenta', 'disabled'=>true, 'type'=>'select', 'options' => $cuentas2, 'default' => $defaultCuenta));?></div>

	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.numero',array('disabled'=>true,'value'=>str_pad($cheque_consumo['ChequeConsumo']['numero'], 8,'0',STR_PAD_LEFT)));?></div>
</div>

<div class="ym-grid">

	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.fecha',array('class'=>'datepicker','type'=>'text','disabled'=>true, 'value'=>$cheque_consumo['ChequeConsumo']['fecha']));?></div>
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.titular',array('disabled'=>true,'value'=>$cheque_consumo['ChequeConsumo']['titular']));?></div>
</div>

<div class="ym-grid">

	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.monto',array('disabled'=>true,'value'=>$cheque_consumo['ChequeConsumo']['monto']));?></div>
	<div class="ym-gl" style="padding: 20px;"><?php echo $this->Form->input('ChequeConsumo.vencido',array('label'=>false, 'div' => false,'type'=>'checkbox')); ?> <strong>Vencido</strong></div>
</div>
<div class="ym-grid" id="divSpeech" style="color: red; font-weight: bold">

</div>
<div class="sectionTitle">Ingrese los datos del nuevo cheque</div>
<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.cuenta_id',array('label' => 'Banco/Cuenta','empty' => 'Seleccionar', 'type'=>'select', 'options' => $cuentas, 'value' => ''));?></div>
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Chequera.chequera_id',array('label' => 'Chequera','empty' => 'Seleccionar', 'type'=>'select'));?></div>

</div>


<div class="ym-grid">

	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.cheque_id',array('label' => 'Numero','empty' => 'Seleccionar', 'type'=>'select'));?></div>
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.fecha',array('class'=>'datepicker','type'=>'text'));?></div>
</div>

<div class="ym-grid">

	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.titular');?></div>
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.monto');?></div>
</div>

<div class="ym-grid">

	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.interes',array('value'=>$cheque_consumo['ChequeConsumo']['interes']));?></div>
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.descuento',array('value'=>$cheque_consumo['ChequeConsumo']['descuento']));?></div>
</div>


<span id="botonGuardar" onclick="guardar('<?php echo $this->Html->url('/cheque_consumos/reemplazo.json', true);?>',$('form').serialize(),'w_cheque_consumo');" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<span id="botonGuardarError" class="boton guardar" style="display:none">Procesando...</span>

<?php echo $this->Form->end(); ?>

<script>



$('#ChequeConsumoCuentaId').change(function(){

    if($(this).val()!=''){

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
            url: '<?php echo $this->Html->url('/chequera_cheques/getNumeros/', true);?>'+$(this).val(),
            dataType: 'html',

            success: function(data){
                $('#ChequeConsumoChequeId').html(data);

            }
        });
    }else{
         $('#ChequeConsumoChequeId').html('');

    }
})
 $("#ChequeConsumoCuentaId").change();

$(function()
{
    if($('#ChequeConsumoVencido').is(':checked')){
        $('#divSpeech').html('Si desea reemplazar un cheque VENCIDO, primero debe pasarlo a PENDIENTE');
    }
    else{
        $('#divSpeech').html('');
    }
});
</script>

