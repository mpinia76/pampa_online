<?php
class EfectivoConsumo extends AppModel {
    public $useTable = 'efectivo_consumo';
//    public $hasMany = array(
//        'RelPagoOperacion' => array(
//            'className' => 'RelPagoOperacion',
//            'foreignKey' => 'forma_pago_id',
//            'conditions' => array ('RelPagoOperacion.forma_pago' => 'efectivo')
//        )
//    );
    public $hasAndBelongsToMany = array( 
        'Gastos' =>
            array(
                'className'              => 'Gasto',
                'joinTable'              => 'rel_pago_operacion',
                'foreignKey'             => 'forma_pago_id',
                'associationForeignKey'  => 'operacion_id',
                'unique'                 => true,
                'conditions'             => array('RelPagoOperacion.forma_pago' => 'efectivo', 'RelPagoOperacion.operacion_tipo' => 'gasto'),
            )
    );
}
?>
