<?php
class ApartamentosController extends AppController {
    public $scaffold;
    
	public function index(){
    	$this->layout = 'index';
    	$this->setLogUsuario('Apartamentos');
    }
    
 	
    
	public function dataTable($limit = ""){
        $rows = array();
        
        if($limit == "todos"){
            $apartamentos = $this->Apartamento->find('all'); 
        }else{
            $apartamentos = $this->Apartamento->find('all',array('limit' => $limit)); 
        }
        foreach ($apartamentos as $apartamento) {
        	$excluir = ($apartamento['Apartamento']['excluir'])?'SI':'NO';
        	
        	
        	$rows[] = array(
                $apartamento['Apartamento']['id'],
                $apartamento['Apartamento']['apartamento'],
                $apartamento['Categoria']['categoria'],
            
                $apartamento['Apartamento']['capacidad'],
                $apartamento['Apartamento']['orden'],
                
                $excluir
            );
            
        }
       
        
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));
    }
}
?>
