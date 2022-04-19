<?php
class Canal extends AppModel {
    public $displayField = 'canal';
    public $validate = array(
        'canal' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un nombre valido'
        )
    );
}
?>
