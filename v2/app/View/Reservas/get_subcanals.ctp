<?php
echo $this->Form->input($model.'.canal_id',array('options' => $subcanals, 'empty' => 'Seleccionar...', 'type'=>'select', 'div' => 'ym-gbox', 'label' => 'Subcanal de venta'));
?>