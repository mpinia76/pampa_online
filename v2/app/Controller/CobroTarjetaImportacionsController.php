<?php
class CobroTarjetaImportacionsController extends AppController {
    public $scaffold;
    
	public function index(){
    	$this->layout = 'index';
    }
    
	public function dataTable($limit = ""){
        $rows = array();
        $this->loadModel('CobroTarjetaImportacion');
        if($limit == "todos"){
            $cobroTarjetaImportacions = $this->CobroTarjetaImportacion->find('all',array('order' => 'fecha desc')); 
        }else{
            $cobroTarjetaImportacions = $this->CobroTarjetaImportacion->find('all',array('limit' => $limit,'order' => 'fecha desc')); 
        }
        foreach ($cobroTarjetaImportacions as $cobroTarjetaImportacion) {
        	
        	
        	$rows[] = array(
                $cobroTarjetaImportacion['CobroTarjetaImportacion']['id'],
                $cobroTarjetaImportacion['CobroTarjetaImportacion']['fecha']
            );
            
        }
       
        
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));
    }
}
?>
