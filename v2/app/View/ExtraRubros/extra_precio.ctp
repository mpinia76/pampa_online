<?php
$options = array();
foreach($subrubro_precio as $key => $obj){
    //print_r($obj);
    //echo $obj['Extra']['activo'];
    if($obj['ExtraSubrubro']['activo']){
        $options[$obj['Extra']['id']] = $obj['ExtraSubrubro']['subrubro']." ".$obj['Extra']['detalle']." $".$obj['Extra']['tarifa'];
    }

}
?>
<div class="ym-gbox" style="width: 64%; float: left;">
    <?php echo $this->Form->input('Extra.id',array('options' => $options, 'label' => 'Subrubro y Precio')); ?>
</div> 
<div class="ym-gbox" style="width: 10%; float: left;">
    <?php echo $this->Form->input('ReservaExtra.cantidad',array('value' => '1', 'type'=>'text', 'size' => '2', 'label' => 'Cant.')); ?>
</div>
<div class="ym-gbox" style="width: 20%; float: right; margin-top:5px;"><span onclick="addExtra();" class="boton agregar">+ agregar</span></div>