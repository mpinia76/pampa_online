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
        'tarjeta_numero' => array(
            'on' => 'create',
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
            'rule' => 'comprobar_lote',
            'message' => 'El lote ingresado ya se encuentra cerrado'
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
}
?>
