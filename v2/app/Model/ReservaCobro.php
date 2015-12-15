<?php
class ReservaCobro extends AppModel {
    public $belongsTo = array('Usuario','Reserva');
    public $hasOne = array(
        'CobroTarjeta' => array(
            'className' => 'CobroTarjeta',
            'foreignKey' => 'reserva_cobro_id',
            'dependent' => true
        ),
        'CobroCheque' => array(
            'className' => 'CobroCheque',
            'foreignKey' => 'reserva_cobro_id',
            'dependent' => true
        ),
        'CobroEfectivo' => array(
            'className' => 'CobroEfectivo',
            'foreignKey' => 'reserva_cobro_id',
            'dependent' => true
        ),
        'CobroTransferencia' => array(
            'className' => 'CobroTransferencia',
            'foreignKey' => 'reserva_cobro_id',
            'dependent' => true
        ),
        'Descuento' => array(
            'className' => 'ReservaDescuento',
            'foreignKey' => 'reserva_cobro_id',
            'dependent' => true
        )
    );
    
    public $virtualFields = array(
        'mes' => 'MONTH(fecha)',
        'ano_mes' => 'DATE_FORMAT(fecha,"%y%m")'
    );
    
    public $validate = array(
        'fecha' => array(
            'rule'     => array('date','dmy'),
            'required' => true,
            'message' => 'Ingrese una fecha valida'
        ),
        'tipo' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una forma de cobro'
        ),
        'monto_neto' => array(
            'numero' => array(
                'rule'    => array('range', 0,999999),
                'required'   => true,
                'message' => 'Ingrese un numero mayor a 0'
             ),
            'monto_maximo' => array(
                'rule' => 'monto_pagado',
                'message' => 'No puede ingresar un monto de pago mayor al saldo pendiente'
            )
        )
    );
    
    public function monto_pagado(){
        if($this->data['ReservaCobro']['monto_neto'] > $this->data['ReservaCobro']['pendiente']){
            return false;
        }else{
            return true;
        }
    }
    
    public function beforeSave($options = Array()) {
        if (!empty($this->data['ReservaCobro']['fecha'])) {
            $this->data['ReservaCobro']['fecha'] = $this->dateFormatBeforeSave($this->data['ReservaCobro']['fecha']);
        }
        return true;
    }
    
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (isset($val['ReservaCobro']['fecha'])) {
                $results[$key]['ReservaCobro']['fecha']= $this->dateFormatAfterFind($val['ReservaCobro']['fecha']);
            }
        }
        return $results;
    }

}
?>