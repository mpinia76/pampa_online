<?php
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');



//formulario
echo $this->Form->create(null, array('url' => '/cheque_consumos/editar','inputDefaults' => (array('div' => 'ym-gbox'))));

?>
<?php echo $this->Form->hidden('ChequeConsumo.id'); 
		//echo $this->Form->hidden('ChequeConsumo.numero'); 
//print_r($cheque_consumo);
?>

<div class="ym-grid">
	<div class="ym-g75 ym-gl"><?php echo $this->Form->input('ChequeConsumo.cuenta_id',array('label' => 'Banco/Cuenta', 'disabled'=>true,'empty' => 'Seleccionar', 'type'=>'select', 'options' => $cuentas));?></div>
	
	
</div>


<div class="ym-grid">
	
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.numero',array('label' => 'Numero', 'disabled'=>true,'value'=>str_pad($cheque_consumo['ChequeConsumo']['numero'], 8,'0',STR_PAD_LEFT)));?></div>
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.fecha',array('class'=>'datepicker','type'=>'text'));?></div>
</div>

<div class="ym-grid">
	
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.titular');?></div>
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.monto',array( 'disabled'=>true));?></div>
</div>

<div class="ym-grid">
	
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.interes',array('disabled'=>true));?></div>
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('ChequeConsumo.descuento',array('disabled'=>true));?></div>
</div>


<span id="botonGuardar" onclick="guardar('<?php echo $this->Html->url('/cheque_consumos/guardar2.json', true);?>',$('form').serialize(),'w_cheque_consumo');" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
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
</script>

