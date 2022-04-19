<?php
class CobroEfectivo extends AppModel {
    public $belongsTo = array('Caja','ReservaCobro','Moneda');
    public $validate = array(
        'caja_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una caja'
        )
     );
     
    //aplicar en movimiento de cajas
    public function afterSave($created){
        if($created){
            $CajaMovimiento = ClassRegistry::init('CajaMovimiento');
            $cobroEfectivo = $this->read();
         
            $CajaMovimiento->set('origen','reservacobro_'.$cobroEfectivo['CobroEfectivo']['id']);
            $CajaMovimiento->set('caja_id',$cobroEfectivo['CobroEfectivo']['caja_id']);
            $CajaMovimiento->set('moneda_id',$cobroEfectivo['CobroEfectivo']['moneda_id']);
            $CajaMovimiento->set('monto',$cobroEfectivo['CobroEfectivo']['monto_neto']);
            $CajaMovimiento->set('monto_moneda',$cobroEfectivo['CobroEfectivo']['monto_moneda']);
            $CajaMovimiento->set('cambio',$cobroEfectivo['CobroEfectivo']['cambio']);
            $CajaMovimiento->set('fecha',$cobroEfectivo['ReservaCobro']['fecha']);
            $CajaMovimiento->save();
        }
    }
}
?>
