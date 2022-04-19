<?php
echo $this->Form->input($model.'.numero',array('options' => $numeros, 'empty' => 'Seleccionar...', 'type'=>'select', 'div' => 'ym-gbox', 'label' => 'Numero'));
?>