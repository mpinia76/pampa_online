<?php
echo $this->Form->input($model.'.chequera_id',array('options' => $chequeras, 'empty' => 'Seleccionar...', 'type'=>'select', 'div' => 'ym-gbox', 'label' => 'Chequera'));
?>