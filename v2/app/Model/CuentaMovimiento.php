<?php
class CuentaMovimiento extends AppModel {
    public $useTable = 'cuenta_movimiento';
    public $belongsTo = array('Cuenta');
    
    public function beforeSave() {
        if (!empty($this->data['CuentaMovimiento']['fecha'])) {
            $this->data['CuentaMovimiento']['fecha'] = $this->dateFormatBeforeSave($this->data['CuentaMovimiento']['fecha']);
        }
        return true;
    }
}
?>
