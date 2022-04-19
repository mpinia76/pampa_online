<?php
class Categoria extends AppModel {
    public $displayField = 'categoria';
    public $validate = array(
        'categoria' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un nombre valido'
        )
    );
}
?>
