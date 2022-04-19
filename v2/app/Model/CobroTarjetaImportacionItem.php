<?php
class CobroTarjetaImportacionItem extends AppModel {
    public $belongsTo = 'CobroTarjetaImportacion';
    
  

   public function beforeSave($options = Array()) {
        if (!empty($this->data['CobroTarjetaImportacionItem']['fecha_compra'])) {
            $this->data['CobroTarjetaImportacionItem']['fecha_compra'] = $this->dateFormatBeforeSave($this->data['CobroTarjetaImportacionItem']['fecha_compra']);
        }
        if (!empty($this->data['CobroTarjetaImportacionItem']['fecha_pago'])) {
            $this->data['CobroTarjetaImportacionItem']['fecha_pago'] = $this->dateFormatBeforeSave($this->data['CobroTarjetaImportacionItem']['fecha_pago']);
        }
        return true;
    }
    
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (!empty($val) and isset($val['CobroTarjetaImportacionItem']['fecha_compra'])) {
                $results[$key]['CobroTarjetaImportacionItem']['fecha_compra']= $this->dateFormatAfterFind($val['CobroTarjetaImportacionItem']['fecha_compra']);
            }
            if (!empty($val) and isset($val['CobroTarjetaImportacionItem']['fecha_pago'])) {
                $results[$key]['CobroTarjetaImportacionItem']['fecha_pago']= $this->dateFormatAfterFind($val['CobroTarjetaImportacionItem']['fecha_pago']);
            }
        }
        return $results;
    }
}
?>
