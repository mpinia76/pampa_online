<?php
class Extra extends AppModel {
    public $belongsTo = array('ExtraRubro','ExtraSubrubro');
    public $hasMany = array('ReservaExtra');
    public $validate = array(
        'extra_rubro_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar un rubro'
        ),
        'extra_subrubro_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar un subrubro'
        ),
        'tarifa' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe ingresar un monto'
        )
    );
    
    public function getPrecioTotal($reserva_id){
        $extras = $this->Extra->findByReservaId($reserva_id);
        return $extras;
    }
}
?>
