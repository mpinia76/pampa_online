<?php
class Apartamento extends AppModel {
    public $displayField = 'apartamento';
    public $validate = array(
        'apartamento' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un nombre valido'
        )
    );
}
?>
