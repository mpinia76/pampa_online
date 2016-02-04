<?php
class CobroTransferencia extends AppModel {
    public $belongsTo = array('ReservaCobro','Cuenta');
    
    public $validate = array(
        'cuenta_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una cuenta'
        ),
        'numero_operacion' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe completar con un numero'
        ),
        'quien_transfiere' => array(
            'required'   => true,
            'rule' => 'notEmpty',
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
        )
    );
    
    public $virtualFields = array(
        'total' => '(CobroTransferencia.monto_neto + CobroTransferencia.interes)',
        'mes_acreditado' => 'MONTH(CobroTransferencia.fecha_acreditado)',
        'ano_mes_acreditado' => 'DATE_FORMAT(CobroTransferencia.fecha_acreditado,"%y%m")'
    );
    
    public function beforeSave($options = Array()) {
        if (!empty($this->data['CobroTransferencia']['fecha_acreditado'])) {
            $this->data['CobroTransferencia']['fecha_acreditado'] = $this->dateFormatBeforeSave($this->data['CobroTransferencia']['fecha_acreditado']);
        }
        return true;
    }

}
?>
