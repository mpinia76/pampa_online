<?php
class ReservaFactura extends AppModel {
    public $belongsTo = array('Usuario','Reserva');
    public $validate = array(
        'fecha_emision' => array(
            'rule'     => array('date','dmy'),
            'required' => true,
            'message' => 'Ingrese una fecha valida'
        ),
        'tipo' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una forma de cobro'
        ),
        'titular' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe completer el titular'
        ),
        'numero' => array(
            'no_vacio' => array(
                'required'   => true,
                'rule' => 'numeric',
                'message' => 'Ingrese solo numeros'
             ),
            'longitud' => array(
                'rule'    => array('minLength', 6),
                'message' => 'Ingrese como minimo 6 digitos.'
            ),
            'unico' => array(
                'rule' => 'isUnique',
                'message' => 'Este numero de factura ya existe'
            )
        ),
        'monto' => array(
            'basico' => array(
                'rule'    => array('range', 0,999999),
                'required'   => true,
                'message' => 'Ingrese un numero mayor a 0'
            ),
            'max' => array(
                'rule' => 'monto_max',
                'message' => 'El monto no puede ser mayor al total de cobros'
            )
        )
    );
    
    public function monto_max(){
        $reserva_cobro = ClassRegistry::init('ReservaCobro');
        $cobrado = 0;
        $cobros = $reserva_cobro->find('all',array('conditions'=>array('reserva_id' => $this->data['ReservaFactura']['reserva_id'])));
        foreach($cobros as $cobro){
            if($cobro['ReservaCobro']['tipo'] != 'DESCUENTO'){
                $cobrado += $cobro['ReservaCobro']['monto_cobrado'];
            }
        }
        if($this->data['ReservaFactura']['monto'] > $cobrado){
            return false;
        }else{
            return true;
        }
    }
    
    public function beforeSave($options = Array()) {
        if (!empty($this->data['ReservaFactura']['fecha_emision'])) {
            $this->data['ReservaFactura']['fecha_emision'] = $this->dateFormatBeforeSave($this->data['ReservaFactura']['fecha_emision']);
        }
        return true;
    }
    
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (isset($val['ReservaFactura']['fecha_emision'])) {
                $results[$key]['ReservaFactura']['fecha_emision']= $this->dateFormatAfterFind($val['ReservaFactura']['fecha_emision']);
            }
        }
        return $results;
    }

}
?>