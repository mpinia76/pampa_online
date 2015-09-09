<?php
class CajaMovimiento extends AppModel {
    public $useTable = 'caja_movimiento';
    public $belongsTo = array(
        'EfectivoConsumo' => array(
            'foreignKey' => 'registro_id',
            'conditions' => array ('CajaMovimiento.origen' => 'efectivo_consumo')
        )
    );
    
    public function beforeSave() {
        if (!empty($this->data['CajaMovimiento']['fecha'])) {
            $this->data['CajaMovimiento']['fecha'] = $this->dateFormatBeforeSave($this->data['CajaMovimiento']['fecha']);
        }
        return true;
    }
   
}
?>
