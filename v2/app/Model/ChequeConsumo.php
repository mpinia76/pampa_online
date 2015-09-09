<?php
class ChequeConsumo extends AppModel {
    public $useTable = 'cheque_consumo';
    public $belongsTo = array('Cuenta');
    
    public function afterFind($results) {
        foreach ($results as $key => $val) {
            if (isset($val['ChequeConsumo']['fecha'])) {
                $results[$key]['ChequeConsumo']['fecha']= $this->dateFormatAfterFind($val['ChequeConsumo']['fecha']);
            }
            if (isset($val['ChequeConsumo']['fecha_debitado'])) {
                $results[$key]['ChequeConsumo']['fecha_debitado']= $this->dateFormatAfterFind($val['ChequeConsumo']['fecha_debitado']);
            }
        }
        return $results;
    }

}
?>
