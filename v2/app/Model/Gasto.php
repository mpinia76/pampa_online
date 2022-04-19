<?php
class Gasto extends AppModel {
    public $useTable = 'gasto';
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
            'fields' => 'id,nombre,condicion_impositiva_id,jurisdiccion_inscripcion_id,razon,cuit'
        )
     );
    
	public function beforeSave($options = Array()) {
        if (!empty($this->data['Gasto']['fecha'])) {
            $this->data['Gasto']['fecha'] = $this->dateFormatBeforeSave($this->data['Gasto']['fecha']);
        }
		if (!empty($this->data['Gasto']['fecha_vencimiento'])) {
            $this->data['Gasto']['fecha_vencimiento'] = $this->dateFormatBeforeSave($this->data['Gasto']['fecha_vencimiento']);
        }
		if (!empty($this->data['Gasto']['created'])) {
            $this->data['Gasto']['created'] = $this->dateFormatBeforeSave($this->data['Gasto']['created']);
        }
        return true;
    }
     
     
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (!empty($val) and isset($val['Gasto']['created'])) {
                $results[$key]['Gasto']['created']= $this->dateFormatAfterFind($val['Gasto']['created']);
            }
            if (!empty($val) and isset($val['Gasto']['fecha'])) {
                $results[$key]['Gasto']['fecha']= $this->dateFormatAfterFind($val['Gasto']['fecha']);
            }
            if (!empty($val) and isset($val['Gasto']['fecha_vencimiento'])) {
                $results[$key]['Gasto']['fecha_vencimiento']= $this->dateFormatAfterFind($val['Gasto']['fecha_vencimiento']);
            }
        }
        return $results;
    }

}
?>