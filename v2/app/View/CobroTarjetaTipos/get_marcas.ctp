<?php
echo $this->Form->input($model.'.cobro_tarjeta_tipo_id',array('options' => $marcas, 'empty' => 'Seleccionar...', 'type'=>'select', 'div' => 'ym-gbox', 'label' => 'Marca'));
?>