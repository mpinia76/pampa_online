<?php
//agregar el calendario
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');


//formulario
echo $this->Form->create(null, array('url' => '/plans/refinanciar','inputDefaults' => (array('div' => 'ym-gbox'))));

?>



<div class="ym-grid">

	<div class="ym-g33 ym-gl"><?php echo $this->Form->input('Plan.monto',array('label' => 'Deuda original','disabled' => true,'value' => $monto,'type' => 'text'));?></div>
    <div class="ym-g25 ym-gl"><label id="labelIntereses" for="PlanIntereses" style="margin-bottom: -3px;margin-top: 2px; margin-left: 5px;">Intereses</label> <?php echo $this->Form->input('Plan.intereses',array('label'=>false,'type' => 'text'));?><?php echo $this->Form->input('descuento',array('label'=>false, 'div' => false,'type'=>'checkbox')); ?> <strong>Descuento</strong></div>
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
<?php echo $this->Form->hidden('ids',array('value' => $ids)); ?>
<?php echo $this->Form->hidden('ordenes',array('value' => $ordenes)); ?>

<span id="botonGuardar" onclick="guardarCerrando('<?php echo $this->Html->url('/plans/guardarRefinanciacion.json', true);?>',$('form').serialize(),{id:'w_planes_pagos',url:'v2/cuota_plans/index'},'w_refinanciar_plan');" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<?php echo $this->Form->end(); ?>

<script>
$('#PlanIntereses').change(function(){
	$('#PlanTotalPlan').val(parseFloat(parseFloat($('#PlanMonto').val())+parseFloat($('#PlanIntereses').val())).toFixed(2));

})
$('#PlanCuotas').change(function(){
	$('#PlanCuotaMensual').val(parseFloat(parseFloat($('#PlanTotalPlan').val())/parseFloat($('#PlanCuotas').val())).toFixed(2));

})

$('#PlanDescuento').change(function(){
    if ($(this).is(':checked')) {
        $("#labelIntereses").text("Descuentos");
        if ($('#PlanIntereses').val()>0){
            $('#PlanIntereses').val($('#PlanIntereses').val()*(-1));
        }
    }
    else {
        $("#labelIntereses").text("Intereses");
        if ($('#PlanIntereses').val()<0){
            $('#PlanIntereses').val($('#PlanIntereses').val()*(-1));
        }
    }
    $('#PlanIntereses').change();
    $('#PlanCuotas').change();
})

</script>
