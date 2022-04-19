<div class="ym-gbox" style="width: 64%; float: left;">
    <?php echo $this->Form->input('ExtraVariable.detalle',array('label' => 'Detalle')); ?>
</div> 
<div class="ym-gbox" style="width: 10%; float: left;">
    <?php echo $this->Form->input('ReservaExtra.precio',array('type'=>'text', 'size' => '2', 'label' => 'Precio $')); ?>
</div>
<div class="ym-gbox" style="width: 20%; float: right; margin-top:5px;"><span onclick="addExtraVariable();" class="boton agregar">+ agregar</span></div>
