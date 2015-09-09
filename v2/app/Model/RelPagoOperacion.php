<?php
class RelPagoOperacion extends AppModel {
    public $useTable = 'rel_pago_operacion';
    public $belongsTo = array(
        'Gasto' => array(
            'foreignKey' => 'operacion_id'
        ),
        'Compra' => array(
            'foreignKey' => 'operacion_id'
        )
    );
}
?>