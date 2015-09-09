<?php
class CobroCheque extends AppModel {
    public $belongsTo = array('ReservaCobro');
    public $hasOne = array(
        'RelPagoOperacion' => array(
            'className'    => 'RelPagoOperacion',
            'foreignKey' => 'forma_pago_id',
            'conditions'   => array('RelPagoOperacion.forma_pago' => 'cheque_tercero')
        )
    );
    public $validate = array(
        'numero' => array(
            'required'   => true,
            'rule' => 'numeric',
            'message' => 'Debe completar con un numero'
        ),
        'banco' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe completar con un banco'
        ),
        'librado_por' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe completar con el nombre'
        ),
        'tipo' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar un tipo'
        ),
        'fecha_cobro' => array(
            'rule'     => array('date','dmy'),
            //'required' => true,
            'message' => 'Ingrese una fecha valida'
        ),
        'cuit' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Ingrese un numero de cuit/cuil'
        ),
        'a_la_orden_de' => array(
            'rule' => 'notEmpty',
            //'required' => true,
            'message' => 'Debe completar con el nombre'
        ),
        'interes' => array(
            'on' => 'create',
            'rule'    => array('range', -1,999999),
            'required'   => true,
            'message' => 'Ingrese un numero mayor o igaul a 0'
        ),
        'fecha_acreditado' => array(
            'rule'     => array('date','dmy'),
            'required' => false,
            'message' => 'Ingrese una fecha valida'
        ),
        'cuenta_acreditado' => array(
            'rule'     => 'notEmpty',
            'required' => false,
            'message' => 'Seleccione una cuenta'
        )
    );
    
    public $virtualFields = array(
        'total' => '(CobroCheque.monto_neto + CobroCheque.interes)',
        'mes_asociado_a_pagos' => 'MONTH(asociado_a_pagos_fecha)',
        'mes_acreditado' => 'MONTH(CobroCheque.fecha_acreditado)',
        'ano_mes_asociado_a_pagos' => 'DATE_FORMAT(CobroCheque.asociado_a_pagos_fecha,"%y%m")',
        'ano_mes_acreditado' => 'DATE_FORMAT(CobroCheque.fecha_acreditado,"%y%m")'
    );
    
    public function beforeSave() {
        if (!empty($this->data['CobroCheque']['fecha_cobro'])) {
            $this->data['CobroCheque']['fecha_cobro'] = $this->dateFormatBeforeSave($this->data['CobroCheque']['fecha_cobro']);
        }
        if (!empty($this->data['CobroCheque']['fecha_acreditado'])) {
            $this->data['CobroCheque']['fecha_acreditado'] = $this->dateFormatBeforeSave($this->data['CobroCheque']['fecha_acreditado']);
        }
        return true;
    }

    public function afterFind($results) {
        foreach ($results as $key => $val) {
            if (isset($val['CobroCheque']['fecha_cobro'])) {
                $results[$key]['CobroCheque']['fecha_cobro']= $this->dateFormatAfterFind($val['CobroCheque']['fecha_cobro']);
            }
        }
        return $results;
    }
    
}
?>
