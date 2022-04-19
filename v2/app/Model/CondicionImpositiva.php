<?php
class CondicionImpositiva extends AppModel {
	public $useTable = 'condicion_impositiva';
    public $displayField = 'nombre';
    public $validate = array(
        'nombre' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un nombre valido'
        )
    );
}
?>
