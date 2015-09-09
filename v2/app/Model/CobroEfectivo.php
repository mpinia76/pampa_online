<?php
class CobroEfectivo extends AppModel {
    public $belongsTo = array('Caja','ReservaCobro');
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
            $CajaMovimiento->set('monto',$cobroEfectivo['CobroEfectivo']['monto_neto']);
            $CajaMovimiento->set('fecha',$cobroEfectivo['ReservaCobro']['fecha']);
            $CajaMovimiento->save();
        }
    }
}
?>
