<?php
class CobroTarjetaTipo extends AppModel {
    public $displayField = 'marca';
    public $belongsTo = array('CobroTarjetaPosnet','Cuenta');
    public $hasMany = 'CobroTarjetaCuota';
    public $validate = array(
        'marca' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe completar con la marca'
        ),
        'cobro_tarjeta_posnet_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar un posnet'
        ),
        'cuenta_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una cuenta'
        )
    );
}
?>