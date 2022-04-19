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
            'message' => 'Ingrese un numero mayor o igual a 0'
        ),
         'fecha_acreditado' => array(
            'format_fecha' => array(
                'rule'     => array('date','dmy'),
                'message' => 'Ingrese una fecha valida'
            ),
            'fecha_anterior' => array(
                'rule' => 'fecha_acreditado_menor_hoy',
                'message' => 'La fecha no puede ser posterior a hoy'
            )
        ),
        'cuenta_acreditado' => array(
            'rule'     => 'notEmpty',
            'required' => false,
            'message' => 'Seleccione una cuenta'
        ),
        'caja_acreditado' => array(
            'rule'     => 'notEmpty',
            'required' => false,
            'message' => 'Seleccione una caja'
        )
    );
    
    public $virtualFields = array(
        'total' => '(CobroCheque.monto_neto + CobroCheque.interes)',
        'mes_asociado_a_pagos' => 'MONTH(asociado_a_pagos_fecha)',
        'mes_acreditado' => 'MONTH(CobroCheque.fecha_acreditado)',
        'ano_mes_asociado_a_pagos' => 'DATE_FORMAT(CobroCheque.asociado_a_pagos_fecha,"%y%m")',
        'ano_mes_acreditado' => 'DATE_FORMAT(CobroCheque.fecha_acreditado,"%y%m")'
    );
    
	public  function fecha_acreditado_menor_hoy(){
       $fecha_hoy = mktime(0, 0, 0, date('m'), date('d'), date('Y')); //echo $fecha_hoy." ]] ";
       if(isset($this->data['CobroCheque']['fecha_acreditado'])){ 
           $parts = explode("/",$this->data['CobroCheque']['fecha_acreditado']);
           $fecha_acreditado = mktime(0,0,0, $parts[1], $parts[0], $parts[2]); //echo $fecha_acreditado;
           if($fecha_acreditado <= $fecha_hoy){
               return true;
           }
       }
   }
    
    public function beforeSave($options = Array()) {
        if (!empty($this->data['CobroCheque']['fecha_cobro'])) {
            $this->data['CobroCheque']['fecha_cobro'] = $this->dateFormatBeforeSave($this->data['CobroCheque']['fecha_cobro']);
        }
        if (!empty($this->data['CobroCheque']['fecha_acreditado'])) {
            $this->data['CobroCheque']['fecha_acreditado'] = $this->dateFormatBeforeSave($this->data['CobroCheque']['fecha_acreditado']);
        }
        return true;
    }

    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (isset($val['CobroCheque']['fecha_cobro'])) {
                $results[$key]['CobroCheque']['fecha_cobro']= $this->dateFormatAfterFind($val['CobroCheque']['fecha_cobro']);
            }
        }
        return $results;
    }
    
}
?>
