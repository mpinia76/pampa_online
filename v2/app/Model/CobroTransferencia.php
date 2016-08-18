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
            'format_fecha' => array(
                'rule'     => array('date','dmy'),
                'message' => 'Ingrese una fecha valida'
            ),
            'fecha_anterior' => array(
                'rule' => 'fecha_acreditado_menor_hoy',
                'message' => 'La fecha no puede ser posterior a hoy'
            )
        )
        
    );
    
    public $virtualFields = array(
        'total' => '(CobroTransferencia.monto_neto + CobroTransferencia.interes)',
        'mes_acreditado' => 'MONTH(CobroTransferencia.fecha_acreditado)',
        'ano_mes_acreditado' => 'DATE_FORMAT(CobroTransferencia.fecha_acreditado,"%y%m")'
    );
    
	public  function fecha_acreditado_menor_hoy(){
       $fecha_hoy = mktime(0, 0, 0, date('m'), date('d'), date('Y')); //echo $fecha_hoy." ]] ";
       if(isset($this->data['CobroTransferencia']['fecha_acreditado'])){ 
           $parts = explode("/",$this->data['CobroTransferencia']['fecha_acreditado']);
           $fecha_acreditado = mktime(0,0,0, $parts[1], $parts[0], $parts[2]); //echo $fecha_acreditado;
           if($fecha_acreditado <= $fecha_hoy){
               return true;
           }
       }
   }
   

    
    public function beforeSave($options = Array()) {
        if (!empty($this->data['CobroTransferencia']['fecha_acreditado'])) {
            $this->data['CobroTransferencia']['fecha_acreditado'] = $this->dateFormatBeforeSave($this->data['CobroTransferencia']['fecha_acreditado']);
        }
        return true;
    }

}
?>
