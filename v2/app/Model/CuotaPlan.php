<?php
class CuotaPlan extends AppModel {
    public $belongsTo = array('Plan');
    
    
    public function beforeSave($options = Array()) {
        if (!empty($this->data['CuotaPlan']['fecha_pago'])) {
            $this->data['CuotaPlan']['fecha_pago'] = $this->dateFormatBeforeSave($this->data['CuotaPlan']['fecha_pago']);
        }
    	if (!empty($this->data['CuotaPlan']['vencimiento'])) {
            $this->data['CuotaPlan']['vencimiento'] = $this->dateFormatBeforeSave($this->data['CuotaPlan']['vencimiento']);
        }
        return true;
    }
    
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (isset($val['CuotaPlan']['fecha_pago'])) {
                $results[$key]['CuotaPlan']['fecha_pago']= $this->dateFormatAfterFind($val['CuotaPlan']['fecha_pago']);
            }
        	if (isset($val['CuotaPlan']['vencimiento'])) {
                $results[$key]['CuotaPlan']['vencimiento']= $this->dateFormatAfterFind($val['CuotaPlan']['vencimiento']);
            }
        }
        return $results;
    }

}
?>