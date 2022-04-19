<?php
class CobroTarjeta extends AppModel {
    public $belongsTo = array('CobroTarjetaTipo','ReservaCobro','CobroTarjetaLote');
    
    public $validate = array(
        'cobro_tarjeta_tipo_id' => array(
            'on' => 'create',
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una tarjeta'
        ),
        'titular' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe completar el titular'
        ),
        'tarjeta_numero' => array(
            'rule'    => array('between', 4, 4),
            'required'   => true,
            'message' => 'Ingrese los ultimos 4 digitos'
        ),
        'posnet_id' => array(
            'on' => 'create',
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una locacion'
        ),
        'cuotas' => array(
            'on' => 'create',
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar cantidad de cuotas'
        ),
        'lote' => array(
            'rule' => array('between', 8, 8),
            'message' => 'El nro de liquidacion debe contener 8 digitos'
        ),
        'lote_nuevo' => array(
            'rule' => array('between', 4, 4),
            'message' => 'El lote debe contener 4 digitos'
        ),
        'fecha_pago' => array(
            'rule'     => array('date','dmy'),
        	'allowEmpty' => true,
            'message' => 'Ingrese una fecha valida'
        )
    );
    
    public $virtualFields = array(
        'total' => '(CobroTarjeta.monto_neto + CobroTarjeta.interes - CobroTarjeta.descuento_lote)'
    );
    
    public function comprobar_lote(){
        if(isset($this->data['CobroTarjeta']['cobro_tarjeta_tipo_id'])){
            ClassRegistry::init('CobroTarjetaLote');
            $lotes = $this->CobroTarjetaLote->find('all',array('conditions' => array ('cobro_tarjeta_tipo_id' => $this->data['CobroTarjeta']['cobro_tarjeta_tipo_id'], 'numero' => $this->data['CobroTarjeta']['lote'])));
            if(count($lotes) > 0){
                return false;
            }else{
                return true;
            }
        }
    }
    
	public function beforeSave($options = Array()) {
    		
    	if(($this->data['CobroTarjeta']['fecha_pago']!='')){
            $this->data['CobroTarjeta']['fecha_pago'] = $this->dateFormatBeforeSave($this->data['CobroTarjeta']['fecha_pago']);
        }
        
 		
        return true;
    }
    
	public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (!empty($val) and isset($val['CobroTarjeta']['fecha_pago'])) {
                $results[$key]['CobroTarjeta']['fecha_pago']= $this->dateFormatAfterFind($val['CobroTarjeta']['fecha_pago']);
            }
           
           
        }
        return $results;
    }
}
?>
