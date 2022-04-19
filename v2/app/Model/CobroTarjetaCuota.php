<?php
class CobroTarjetaCuota extends AppModel {
    public $belongsTo = 'CobroTarjetaTipo';
    public $displayField = 'cuota';
    public $validate = array(
        'posnet_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una locacion'
        ),
        'cobro_tarjeta_tipo_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una marca'
        ),
        'cuota' => array(
            'rule'    => array('range', 0,24),
            'required'   => true,
            'message' => 'Ingrese un numero mayor a 0'
        ),
        'interes' => array(
            'rule'    => array('range', 0.99,2),
            'required'   => true,
            'message' => 'Ingrese un numero mayor o igual a 1'
        )
    );
}
?>
