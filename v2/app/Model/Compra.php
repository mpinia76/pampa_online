<?php
class Compra extends AppModel {
    public $useTable = 'compra';
    public $belongsTo = array(
        'Rubro' => array(
            'fields' => 'id,rubro'
        ),
        'Subrubro' => array(
            'fields' => 'id,subrubro'
        ),
        'Usuario' => array(
            'className' => 'Usuario',
            'foreignKey' => 'user_id',
            'fields' => 'id,nombre,apellido'
        ),
        'Proveedor' => array(
            'className' => 'Proveedor',
            'foreignKey' => 'proveedor',
            'fields' => 'id,nombre'
        )
     );
    
	public function beforeSave($options = Array()) {
        if (!empty($this->data['Compra']['fecha'])) {
            $this->data['Compra']['fecha'] = $this->dateFormatBeforeSave($this->data['Compra']['fecha']);
        }
		if (!empty($this->data['Compra']['fecha_vencimiento'])) {
            $this->data['Compra']['fecha_vencimiento'] = $this->dateFormatBeforeSave($this->data['Compra']['fecha_vencimiento']);
        }
		if (!empty($this->data['Compra']['created'])) {
            $this->data['Compra']['created'] = $this->dateFormatBeforeSave($this->data['Compra']['created']);
        }
        return true;
    } 
     
    public function afterFind($results, $primary = false ) {
        foreach ($results as $key => $val) {
            if (isset($val['Compra']['created'])) {
                $results[$key]['Compra']['created']= $this->dateFormatAfterFind($val['Compra']['created']);
            }
            if (isset($val['Compra']['fecha'])) {
                $results[$key]['Compra']['fecha']= $this->dateFormatAfterFind($val['Compra']['fecha']);
            }
            if (isset($val['Compra']['fecha_vencimiento'])) {
                $results[$key]['Compra']['fecha_vencimiento']= $this->dateFormatAfterFind($val['Compra']['fecha_vencimiento']);
            }
        }
        return $results;
    }

}
?>
