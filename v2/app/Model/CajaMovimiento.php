<?php
class CajaMovimiento extends AppModel {
    public $useTable = 'caja_movimiento';
    public $belongsTo = array(
        'EfectivoConsumo' => array(
            'foreignKey' => 'registro_id',
            'conditions' => array ('CajaMovimiento.origen' => 'efectivo_consumo')
        ),
        'Moneda'
    );
    
    public function beforeSave() {
        if (!empty($this->data['CajaMovimiento']['fecha'])) {
        	$fecha =$this->dateFormatBeforeSave($this->data['CajaMovimiento']['fecha']);
        	$time = time();

			$hora = date("H:i:s", $time);
		
			$fecha .=' '.$hora;
            $this->data['CajaMovimiento']['fecha'] = $fecha;
        }
        return true;
    }
   
}
?>
