<?php
class ReservaDescuento extends AppModel {
    public $validate = array(
        'fecha' => array(
            'rule'     => array('date','dmy'),
            'required' => true,
            'message' => 'Ingrese una fecha valida'
        ),
        'motivo' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe completar con un motivo'
        ),
        'monto' => array(
            'rule'    => array('range', 0,999999),
            'required'   => true,
            'message' => 'Ingrese un numero mayor a 0'
        )
     );     
}
?>
