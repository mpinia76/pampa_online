<?php
class ChequeConsumosController extends AppController {
    public $scaffold;
    
    public function dataTable($limit = ""){
        $rows = array();
        if($limit == "todos"){
            $cheques = $this->ChequeConsumo->find('all',array('order' => 'ChequeConsumo.fecha desc')); 
        }else{
            $cheques = $this->ChequeConsumo->find('all',array('limit' => $limit, 'order' => 'ChequeConsumo.fecha desc')); 
        }
        
        foreach($cheques as $cheque){
            $rows[] = array(
                $cheque['ChequeConsumo']['id'],
                $cheque['ChequeConsumo']['fecha'],
                $cheque['ChequeConsumo']['fecha_debitado']
            );
        }
        
        $this->set('cheques',$cheques);
        $this->set('_serialize', array(
            'cheques'
        ));
    }
}
?>
