<?php
class CobroTarjetaPosnet extends AppModel {
    public $displayField = 'posnet';
    public $validate = array(
        'posnet' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe completar con un nombre'
        )
    );
}
?>
