<?php
class ReservaFacturaImportacionItem extends AppModel {
    public $belongsTo = 'ReservaFacturaImportacion';
    
  

   public function beforeSave($options = Array()) {
        if (!empty($this->data['ReservaFacturaImportacionItem']['fecha'])) {
            $this->data['ReservaFacturaImportacionItem']['fecha'] = $this->dateFormatBeforeSave($this->data['ReservaFacturaImportacionItem']['fecha']);
        }
        
        return true;
    }
    
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (!empty($val) and isset($val['ReservaFacturaImportacionItem']['fecha'])) {
                $results[$key]['ReservaFacturaImportacionItem']['fecha']= $this->dateFormatAfterFind($val['ReservaFacturaImportacionItem']['fecha']);
            }
           
        }
        return $results;
    }
}
?>
