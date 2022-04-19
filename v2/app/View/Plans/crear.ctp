<?php
//agregar el calendario
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');


//formulario
echo $this->Form->create(null, array('url' => '/plans/crear','inputDefaults' => (array('div' => 'ym-gbox'))));

?>



<div class="ym-grid">
	
	<div class="ym-g33 ym-gl"><?php echo $this->Form->input('Plan.monto',array('label' => 'Deuda original','disabled' => true,'value' => $monto,'type' => 'text'));?></div>
    <div class="ym-g33 ym-gl"><?php echo $this->Form->input('Plan.intereses',array('type' => 'text'));?></div>
    <div class="ym-g33 ym-gl"><?php echo $this->Form->input('totalPlan',array('label' => 'Monto total plan','disabled' => true));?></div>
</div>
<div class="ym-grid">
	<div class="ym-g33 ym-gl"><?php echo $this->Form->input('Plan.plan',array('maxlength'=>'8'));?></div>
    <div class="ym-g33 ym-gl"><?php echo $this->Form->input('Plan.cuotas',array('label' => 'Cantidad de cuotas','type' => 'text'));?></div>
    <div class="ym-g33 ym-gl"><?php echo $this->Form->input('cuotaMensual',array('label' => 'Cuota mensual','disabled' => true));?></div>
</div>
<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Plan.vencimiento1',array('label' => 'Vencimiento cuota 1','class'=>'datepicker','type'=>'text')); ?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Plan.vencimiento2',array('label' => 'Vencimiento cuota 2','class'=>'datepicker','type'=>'text')); ?></div>
</div>
<?php echo $this->Form->hidden('Plan.tipo',array('value' => $tipo)); ?>
<?php echo $this->Form->hidden('Plan.rubro_id',array('value' => $rubro)); ?>
<?php echo $this->Form->hidden('Plan.subrubro_id',array('value' => $subrubro)); ?>
<?php echo $this->Form->hidden('Plan.proveedor',array('value' => $proveedor)); ?>
<?php echo $this->Form->hidden('Plan.monto',array('value' => $monto)); ?>
<?php echo $this->Form->hidden('Plan.user_id',array('value' => $user)); ?>
<?php echo $this->Form->hidden('Plan.factura_nro',array('value' => $factura)); ?>
<?php echo $this->Form->hidden('ids',array('value' => $ids)); ?>

<span id="botonGuardar" onclick="guardarCerrando('<?php echo $this->Html->url('/plans/guardar.json', true);?>',$('form').serialize(),{id:'<?php echo $id_ventana;?>',url:'<?php echo $url_ventana;?>'},'w_generar_plan');" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<?php echo $this->Form->end(); ?>

<script>
$('#PlanIntereses').change(function(){
	$('#PlanTotalPlan').val(parseFloat(parseFloat($('#PlanMonto').val())+parseFloat($('#PlanIntereses').val())).toFixed(2));
    
})
$('#PlanCuotas').change(function(){
	$('#PlanCuotaMensual').val(parseFloat(parseFloat($('#PlanTotalPlan').val())/parseFloat($('#PlanCuotas').val())).toFixed(2));
    
})

</script>